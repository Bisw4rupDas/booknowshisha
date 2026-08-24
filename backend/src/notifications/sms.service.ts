import { Injectable, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';

export interface SmsSendResult {
  success: boolean;
  provider: string;
  messageId?: string;
  error?: string;
}

@Injectable()
export class SmsService {
  private readonly logger = new Logger(SmsService.name);

  constructor(private readonly configService: ConfigService) {}

  /**
   * Normalize an Indian mobile number to 10 digits
   */
  public normalizeIndianPhone(rawPhone: string): string {
    let clean = (rawPhone || '').replace(/[^0-9]/g, '');
    if (clean.length === 12 && clean.startsWith('91')) {
      clean = clean.substring(2);
    } else if (clean.length === 11 && clean.startsWith('0')) {
      clean = clean.substring(1);
    }
    return clean;
  }

  /**
   * Mask phone number for safe logging (e.g. +91 98300 •••45)
   */
  public maskPhone(phone10: string): string {
    if (phone10.length === 10) {
      return `+91 ${phone10.substring(0, 5)} •••${phone10.substring(8)}`;
    }
    return '+91 ••••• •••••';
  }

  /**
   * Determine the active SMS provider from environment configuration
   */
  public getActiveProvider(): string {
    const explicit = (this.configService.get<string>('SMS_PROVIDER') || '').toLowerCase().trim();
    if (explicit) {
      return explicit;
    }

    if (this.configService.get<string>('FAST2SMS_API_KEY')) return 'fast2sms';
    if (this.configService.get<string>('TWOFACTOR_API_KEY')) return '2factor';
    if (this.configService.get<string>('MSG91_AUTH_KEY')) return 'msg91';
    if (this.configService.get<string>('TWILIO_ACCOUNT_SID')) return 'twilio';
    if (this.configService.get<string>('SMS_API_KEY')) return 'fast2sms';

    return 'none';
  }

  /**
   * Send 6-digit OTP SMS via the configured SMS Gateway
   */
  async sendOtpSms(rawPhone: string, otp: string): Promise<SmsSendResult> {
    const phone10 = this.normalizeIndianPhone(rawPhone);
    const maskedPhone = this.maskPhone(phone10);
    const provider = this.getActiveProvider();

    this.logger.log(`[SMS Gateway] Initiating OTP dispatch to ${maskedPhone} via provider: ${provider}`);

    if (provider === 'none') {
      const err = 'SMS provider is not configured. Please set SMS_PROVIDER and SMS_API_KEY (or FAST2SMS_API_KEY / TWOFACTOR_API_KEY / MSG91_AUTH_KEY / TWILIO_ACCOUNT_SID) in backend environment.';
      this.logger.warn(`[SMS Gateway] ${err}`);
      return {
        success: false,
        provider: 'none',
        error: err,
      };
    }

    try {
      switch (provider) {
        case 'fast2sms':
          return await this.sendViaFast2Sms(phone10, otp, maskedPhone);

        case '2factor':
          return await this.sendVia2Factor(phone10, otp, maskedPhone);

        case 'msg91':
          return await this.sendViaMsg91(phone10, otp, maskedPhone);

        case 'twilio':
          return await this.sendViaTwilio(phone10, otp, maskedPhone);

        default:
          return await this.sendViaGeneric(phone10, otp, maskedPhone);
      }
    } catch (err: any) {
      const errMsg = err?.message || 'Unknown network error occurred while communicating with SMS gateway.';
      this.logger.error(`[SMS Gateway] Unexpected error during SMS dispatch to ${maskedPhone}: ${errMsg}`);
      return {
        success: false,
        provider,
        error: `SMS Gateway communication failed: ${errMsg}`,
      };
    }
  }

  /**
   * 1. Fast2SMS (Indian Quick OTP Route)
   * API Doc: https://www.fast2sms.com/dashboard/dev/bulkV2
   */
  private async sendViaFast2Sms(phone10: string, otp: string, maskedPhone: string): Promise<SmsSendResult> {
    const apiKey =
      this.configService.get<string>('FAST2SMS_API_KEY') ||
      this.configService.get<string>('SMS_API_KEY');

    if (!apiKey) {
      const err = 'Fast2SMS API Key (FAST2SMS_API_KEY or SMS_API_KEY) is missing in environment.';
      this.logger.error(`[Fast2SMS] ${err}`);
      return { success: false, provider: 'fast2sms', error: err };
    }

    const endpoint = 'https://www.fast2sms.com/dev/bulkV2';
    const payload = {
      route: 'otp',
      variables_values: otp,
      numbers: phone10,
    };

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        authorization: apiKey,
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(payload),
      signal: AbortSignal.timeout(8000),
    });

    const responseText = await response.text();
    let json: any = {};
    try {
      json = JSON.parse(responseText);
    } catch {
      json = { raw: responseText };
    }

    if (response.ok && json.return === true) {
      const reqId = json.request_id || json.message?.[0] || 'OK';
      this.logger.log(`[Fast2SMS] OTP successfully accepted for ${maskedPhone}. Request ID: ${reqId}`);
      return {
        success: true,
        provider: 'fast2sms',
        messageId: reqId,
      };
    }

    const providerError =
      (Array.isArray(json.message) ? json.message.join(', ') : json.message) ||
      `HTTP ${response.status}: ${response.statusText}`;

    this.logger.error(`[Fast2SMS] Gateway rejected SMS for ${maskedPhone}. Reason: ${providerError}`);
    return {
      success: false,
      provider: 'fast2sms',
      error: `Fast2SMS rejected request: ${providerError}`,
    };
  }

  /**
   * 2. 2Factor (Indian SMS OTP Service)
   * API Doc: https://2factor.in/v2/SMS
   */
  private async sendVia2Factor(phone10: string, otp: string, maskedPhone: string): Promise<SmsSendResult> {
    const apiKey =
      this.configService.get<string>('TWOFACTOR_API_KEY') ||
      this.configService.get<string>('SMS_API_KEY');

    if (!apiKey) {
      const err = '2Factor API Key (TWOFACTOR_API_KEY or SMS_API_KEY) is missing in environment.';
      this.logger.error(`[2Factor] ${err}`);
      return { success: false, provider: '2factor', error: err };
    }

    const templateName = this.configService.get<string>('SMS_TEMPLATE_ID') || 'BOOKMYSMOKE';
    const endpoint = `https://2factor.in/v2/SMS/${apiKey}/SMS/${phone10}/${otp}/${templateName}`;

    const response = await fetch(endpoint, {
      method: 'GET',
      headers: { Accept: 'application/json' },
      signal: AbortSignal.timeout(8000),
    });

    const responseText = await response.text();
    let json: any = {};
    try {
      json = JSON.parse(responseText);
    } catch {
      json = { raw: responseText };
    }

    if (response.ok && json.Status === 'Success') {
      const sessionId = json.Details || 'OK';
      this.logger.log(`[2Factor] OTP successfully accepted for ${maskedPhone}. Session ID: ${sessionId}`);
      return {
        success: true,
        provider: '2factor',
        messageId: sessionId,
      };
    }

    const providerError = json.Details || `HTTP ${response.status}: ${response.statusText}`;
    this.logger.error(`[2Factor] Gateway rejected SMS for ${maskedPhone}. Reason: ${providerError}`);
    return {
      success: false,
      provider: '2factor',
      error: `2Factor rejected request: ${providerError}`,
    };
  }

  /**
   * 3. MSG91 (Indian DLT SMS Gateway)
   * API Doc: https://docs.msg91.com/p/tf9GText/otp
   */
  private async sendViaMsg91(phone10: string, otp: string, maskedPhone: string): Promise<SmsSendResult> {
    const authKey =
      this.configService.get<string>('MSG91_AUTH_KEY') ||
      this.configService.get<string>('SMS_API_KEY');
    const templateId =
      this.configService.get<string>('MSG91_TEMPLATE_ID') ||
      this.configService.get<string>('SMS_TEMPLATE_ID');

    if (!authKey || !templateId) {
      const err = 'MSG91 requires both MSG91_AUTH_KEY and MSG91_TEMPLATE_ID in environment.';
      this.logger.error(`[MSG91] ${err}`);
      return { success: false, provider: 'msg91', error: err };
    }

    const endpoint = `https://control.msg91.com/api/v5/otp?template_id=${templateId}&mobile=91${phone10}&authkey=${authKey}`;
    const payload = { otp };

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(payload),
      signal: AbortSignal.timeout(8000),
    });

    const responseText = await response.text();
    let json: any = {};
    try {
      json = JSON.parse(responseText);
    } catch {
      json = { raw: responseText };
    }

    if (response.ok && json.type === 'success') {
      const msgId = json.message || 'OK';
      this.logger.log(`[MSG91] OTP successfully accepted for ${maskedPhone}. Message: ${msgId}`);
      return {
        success: true,
        provider: 'msg91',
        messageId: msgId,
      };
    }

    const providerError = json.message || `HTTP ${response.status}: ${response.statusText}`;
    this.logger.error(`[MSG91] Gateway rejected SMS for ${maskedPhone}. Reason: ${providerError}`);
    return {
      success: false,
      provider: 'msg91',
      error: `MSG91 rejected request: ${providerError}`,
    };
  }

  /**
   * 4. Twilio (SMS Gateway)
   * API Doc: https://www.twilio.com/docs/sms/api/message-resource
   */
  private async sendViaTwilio(phone10: string, otp: string, maskedPhone: string): Promise<SmsSendResult> {
    const accountSid =
      this.configService.get<string>('TWILIO_ACCOUNT_SID') ||
      this.configService.get<string>('SMS_ACCOUNT_SID');
    const authToken =
      this.configService.get<string>('TWILIO_AUTH_TOKEN') ||
      this.configService.get<string>('SMS_AUTH_TOKEN');
    const fromNumber =
      this.configService.get<string>('TWILIO_PHONE_NUMBER') ||
      this.configService.get<string>('SMS_FROM_NUMBER');

    if (!accountSid || !authToken || !fromNumber) {
      const err = 'Twilio requires TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, and TWILIO_PHONE_NUMBER in environment.';
      this.logger.error(`[Twilio] ${err}`);
      return { success: false, provider: 'twilio', error: err };
    }

    const endpoint = `https://api.twilio.com/2010-04-01/Accounts/${accountSid}/Messages.json`;
    const bodyParams = new URLSearchParams();
    bodyParams.append('To', `+91${phone10}`);
    bodyParams.append('From', fromNumber);
    bodyParams.append('Body', `Your BookMySmoke verification code is ${otp}. Valid for 5 minutes. Do not share this code.`);

    const authHeader = 'Basic ' + Buffer.from(`${accountSid}:${authToken}`).toString('base64');

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        Authorization: authHeader,
        'Content-Type': 'application/x-www-form-urlencoded',
        Accept: 'application/json',
      },
      body: bodyParams.toString(),
      signal: AbortSignal.timeout(8000),
    });

    const responseText = await response.text();
    let json: any = {};
    try {
      json = JSON.parse(responseText);
    } catch {
      json = { raw: responseText };
    }

    if (response.ok && (json.status === 'queued' || json.status === 'sent' || json.sid)) {
      this.logger.log(`[Twilio] OTP successfully queued for ${maskedPhone}. SID: ${json.sid}`);
      return {
        success: true,
        provider: 'twilio',
        messageId: json.sid,
      };
    }

    const providerError = json.message || `HTTP ${response.status}: ${response.statusText}`;
    this.logger.error(`[Twilio] Gateway rejected SMS for ${maskedPhone}. Reason: ${providerError}`);
    return {
      success: false,
      provider: 'twilio',
      error: `Twilio rejected request: ${providerError}`,
    };
  }

  /**
   * 5. Generic HTTP SMS API (Custom URL or Aggregator)
   */
  private async sendViaGeneric(phone10: string, otp: string, maskedPhone: string): Promise<SmsSendResult> {
    const apiUrl = this.configService.get<string>('SMS_API_URL');
    const apiKey = this.configService.get<string>('SMS_API_KEY');

    if (!apiUrl) {
      const err = 'No SMS_API_URL configured for generic SMS provider.';
      this.logger.error(`[Generic SMS] ${err}`);
      return { success: false, provider: 'generic', error: err };
    }

    const renderedUrl = apiUrl
      .replace('{phone}', phone10)
      .replace('{otp}', otp)
      .replace('{key}', apiKey || '');

    const response = await fetch(renderedUrl, {
      method: 'GET',
      headers: {
        Accept: 'application/json, text/plain',
        ...(apiKey ? { Authorization: `Bearer ${apiKey}` } : {}),
      },
      signal: AbortSignal.timeout(8000),
    });

    if (response.ok) {
      this.logger.log(`[Generic SMS] OTP dispatched for ${maskedPhone} via ${apiUrl}`);
      return {
        success: true,
        provider: 'generic',
      };
    }

    const err = `HTTP ${response.status}: ${response.statusText}`;
    this.logger.error(`[Generic SMS] Gateway error for ${maskedPhone}: ${err}`);
    return {
      success: false,
      provider: 'generic',
      error: err,
    };
  }
}
