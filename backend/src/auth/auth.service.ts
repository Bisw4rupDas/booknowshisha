import {
  Injectable,
  ConflictException,
  UnauthorizedException,
  ForbiddenException,
  ServiceUnavailableException,
  BadRequestException,
  Logger,
} from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { ConfigService } from '@nestjs/config';
import { PrismaService } from '../prisma/prisma.service';
import { RedisService } from '../common/redis/redis.service';
import { RegisterDto } from './dto/register.dto';
import { LoginDto } from './dto/login.dto';
import { SendOtpDto, VerifyOtpDto } from './dto/otp-auth.dto';
import { UserRole } from '@prisma/client';
import * as bcrypt from 'bcryptjs';
import * as crypto from 'crypto';

import { SmsService } from '../notifications/sms.service';

export interface GoogleUserProfile {
  googleId: string;
  email: string;
  firstName: string;
  lastName: string;
  avatarUrl: string | null;
  emailVerified: boolean;
}

@Injectable()
export class AuthService {
  private readonly logger = new Logger(AuthService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly jwtService: JwtService,
    private readonly configService: ConfigService,
    private readonly redisService: RedisService,
    private readonly smsService: SmsService,
  ) {}


  // ===========================================================================
  // 1. STANDARD USER AUTHENTICATION
  // ===========================================================================

  async register(dto: RegisterDto) {
    const existingUser = await this.prisma.user.findUnique({
      where: { email: dto.email.toLowerCase() },
    });

    if (existingUser) {
      throw new ConflictException('A user with this email address already exists');
    }

    if (dto.phone) {
      const existingCustomerPhone = await this.prisma.customer.findUnique({
        where: { phone: dto.phone },
      });

      if (existingCustomerPhone) {
        throw new ConflictException('A customer with this phone number already exists');
      }
    }

    const passwordHash = await bcrypt.hash(dto.password, 10);

    const user = await this.prisma.user.create({
      data: {
        email: dto.email.toLowerCase(),
        passwordHash,
        role: UserRole.CUSTOMER,
        isVerified: false,
        customer: {
          create: {
            firstName: dto.firstName,
            lastName: dto.lastName,
            phone: dto.phone,
            addressLine1: dto.addressLine1,
            city: dto.city,
            postalCode: dto.postalCode,
          },
        },
      },
      include: {
        customer: true,
      },
    });

    this.logger.log(`New customer registered: ${user.email} (${user.id})`);

    const token = this.generateToken(user.id, user.email, user.role);

    return {
      accessToken: token,
      user: {
        id: user.id,
        email: user.email,
        role: user.role,
        customer: user.customer,
      },
    };
  }

  async login(dto: LoginDto) {
    const user = await this.prisma.user.findUnique({
      where: { email: dto.email.toLowerCase() },
      include: {
        customer: true,
        staff: true,
        admin: true,
      },
    });

    if (!user || !user.passwordHash) {
      throw new UnauthorizedException('Invalid email or password');
    }

    const isMatch = await bcrypt.compare(dto.password, user.passwordHash);
    if (!isMatch) {
      throw new UnauthorizedException('Invalid email or password');
    }

    if (!user.isActive) {
      throw new UnauthorizedException('Your account has been deactivated. Please contact support.');
    }

    const token = this.generateToken(user.id, user.email, user.role);

    // Audit log
    await this.prisma.auditLog.create({
      data: {
        userId: user.id,
        action: 'AUTH_LOGIN_SUCCESS',
        entity: 'User',
        entityId: user.id,
      },
    });

    return {
      accessToken: token,
      user: {
        id: user.id,
        email: user.email,
        role: user.role,
        profile: user.customer || user.staff || user.admin,
      },
    };
  }

  async getProfile(userId: string) {
    const user = await this.prisma.user.findUnique({
      where: { id: userId },
      select: {
        id: true,
        email: true,
        role: true,
        isActive: true,
        isVerified: true,
        customer: true,
        staff: true,
        admin: true,
        createdAt: true,
      },
    });

    if (!user) {
      throw new UnauthorizedException('User not found');
    }

    return user;
  }

  // ===========================================================================
  // 2. GOOGLE OAUTH CONFIGURATION & PLACEHOLDER MANAGEMENT
  // ===========================================================================

  isGoogleAuthConfigured(): boolean {
    const clientId = this.configService.get<string>('GOOGLE_CLIENT_ID', '').trim();
    const clientSecret = this.configService.get<string>('GOOGLE_CLIENT_SECRET', '').trim();

    if (!clientId || !clientSecret) {
      return false;
    }

    const isPlaceholder =
      clientId.includes('placeholder') ||
      clientId.includes('YOUR_') ||
      clientId.includes('EXAMPLE') ||
      clientId.startsWith('ck_') ||
      clientSecret.includes('placeholder') ||
      clientSecret.includes('YOUR_') ||
      clientSecret.includes('EXAMPLE') ||
      clientSecret.startsWith('cs_');

    return !isPlaceholder;
  }

  getGoogleConfigStatus() {
    const configured = this.isGoogleAuthConfigured();
    const clientId = configured ? this.configService.get<string>('GOOGLE_CLIENT_ID') : null;
    const callbackUrl = this.configService.get<string>(
      'GOOGLE_CALLBACK_URL',
      'http://localhost:3000/api/auth/google/callback',
    );
    const adminEmailsRaw = this.configService.get<string>('ADMIN_GOOGLE_EMAILS', '');
    const adminAllowlist = adminEmailsRaw
      .split(',')
      .map((e) => e.trim().toLowerCase())
      .filter((e) => e.length > 0);

    return {
      configured,
      clientId: configured ? clientId : null,
      callbackUrl,
      adminAllowlistConfigured: adminAllowlist.length > 0,
      adminAllowlistCount: adminAllowlist.length,
      message: configured
        ? 'Google OAuth is fully configured and ready for live authentication.'
        : 'Google Sign-In is not configured yet. Please configure GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in backend environment variables.',
    };
  }

  getGoogleAuthUrl(isAdmin = false): { url: string; state: string } {
    if (!this.isGoogleAuthConfigured()) {
      throw new ServiceUnavailableException(
        'Google Sign-In is not configured yet. Please configure Google OAuth credentials in backend environment variables.',
      );
    }

    const clientId = this.configService.get<string>('GOOGLE_CLIENT_ID')!;
    const callbackUrl = this.configService.get<string>(
      'GOOGLE_CALLBACK_URL',
      'http://localhost:3000/api/auth/google/callback',
    );

    // Create tamper-proof HMAC state token
    const nonce = crypto.randomBytes(16).toString('hex');
    const secret = this.configService.get<string>('JWT_SECRET', 'secret');
    const statePayload = JSON.stringify({ nonce, isAdmin, timestamp: Date.now() });
    const hmac = crypto.createHmac('sha256', secret).update(statePayload).digest('hex');
    const state = Buffer.from(JSON.stringify({ payload: statePayload, sig: hmac })).toString('base64url');

    const params = new URLSearchParams({
      client_id: clientId,
      redirect_uri: callbackUrl,
      response_type: 'code',
      scope: 'openid email profile',
      access_type: 'offline',
      state,
      prompt: 'select_account',
    });

    const url = `https://accounts.google.com/o/oauth2/v2/auth?${params.toString()}`;

    return { url, state };
  }

  isEmailAuthorizedForAdmin(email: string): boolean {
    if (!email) return false;
    const normalizedEmail = email.trim().toLowerCase();
    const adminEmailsRaw = this.configService.get<string>('ADMIN_GOOGLE_EMAILS', '');
    const allowlist = adminEmailsRaw
      .split(',')
      .map((e) => e.trim().toLowerCase())
      .filter((e) => e.length > 0);

    return allowlist.includes(normalizedEmail);
  }

  // ===========================================================================
  // 3. GOOGLE IDENTITY VERIFICATION & ACCOUNT LINKING
  // ===========================================================================

  async verifyGoogleIdentity(tokenOrCode: string): Promise<GoogleUserProfile> {
    if (!this.isGoogleAuthConfigured()) {
      throw new ServiceUnavailableException(
        'Google Sign-In is not configured yet. Please configure Google OAuth credentials in backend environment variables.',
      );
    }

    const clientId = this.configService.get<string>('GOOGLE_CLIENT_ID')!;
    const clientSecret = this.configService.get<string>('GOOGLE_CLIENT_SECRET')!;
    const callbackUrl = this.configService.get<string>(
      'GOOGLE_CALLBACK_URL',
      'http://localhost:3000/api/auth/google/callback',
    );

    // Case 1: If input is an OAuth2 Authorization Code (from redirect or SDK)
    if (!tokenOrCode.startsWith('eyJ')) {
      try {
        const tokenResponse = await fetch('https://oauth2.googleapis.com/token', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            code: tokenOrCode,
            client_id: clientId,
            client_secret: clientSecret,
            redirect_uri: callbackUrl,
            grant_type: 'authorization_code',
          }),
        });

        if (!tokenResponse.ok) {
          const errData = await tokenResponse.json().catch(() => ({}));
          this.logger.error(`Google token exchange failed: ${JSON.stringify(errData)}`);
          throw new UnauthorizedException('Failed to exchange Google authorization code');
        }

        const tokenData = await tokenResponse.json();
        const accessToken = tokenData.access_token;
        const idToken = tokenData.id_token;

        // Fetch UserInfo with access token
        const userInfoResponse = await fetch('https://www.googleapis.com/oauth2/v3/userinfo', {
          headers: { Authorization: `Bearer ${accessToken}` },
        });

        if (!userInfoResponse.ok) {
          throw new UnauthorizedException('Failed to retrieve user profile from Google');
        }

        const userInfo = await userInfoResponse.json();

        if (!userInfo.email_verified) {
          throw disaster('Google account email is not verified');
        }

        return {
          googleId: userInfo.sub,
          email: userInfo.email.toLowerCase(),
          firstName: userInfo.given_name || userInfo.name?.split(' ')[0] || 'GoogleUser',
          lastName: userInfo.family_name || userInfo.name?.split(' ').slice(1).join(' ') || '',
          avatarUrl: userInfo.picture || null,
          emailVerified: true,
        };
      } catch (err: any) {
        if (err instanceof UnauthorizedException || err instanceof ServiceUnavailableException) throw err;
        this.logger.error(`Error exchanging Google OAuth code: ${err.message}`);
        throw new UnauthorizedException('Invalid Google authorization code');
      }
    }

    // Case 2: If input is a JWT ID Token (from Google One Tap / Sign-In Button)
    try {
      const verifyResponse = await fetch(
        `https://oauth2.googleapis.com/tokeninfo?id_token=${encodeURIComponent(tokenOrCode)}`,
      );

      if (!verifyResponse.ok) {
        throw new UnauthorizedException('Invalid or expired Google ID token');
      }

      const payload = await verifyResponse.json();

      // Security: Validate audience matches our Google Client ID
      if (payload.aud !== clientId) {
        this.logger.warn(`Google token audience mismatch: expected ${clientId}, received ${payload.aud}`);
        throw new UnauthorizedException('Google ID token audience mismatch');
      }

      // Security: Validate email verified status
      if (payload.email_verified !== 'true' && payload.email_verified !== true) {
        throw new UnauthorizedException('Google account email is not verified by Google');
      }

      return {
        googleId: payload.sub,
        email: payload.email.toLowerCase(),
        firstName: payload.given_name || payload.name?.split(' ')[0] || 'GoogleUser',
        lastName: payload.family_name || payload.name?.split(' ').slice(1).join(' ') || '',
        avatarUrl: payload.picture || null,
        emailVerified: true,
      };
    } catch (err: any) {
      if (err instanceof UnauthorizedException || err instanceof ServiceUnavailableException) throw err;
      this.logger.error(`Error verifying Google ID token: ${err.message}`);
      throw new UnauthorizedException('Google token verification failed');
    }
  }

  async loginWithGoogle(tokenOrCode: string, isAdminLogin = false) {
    const googleProfile = await this.verifyGoogleIdentity(tokenOrCode);
    const isAllowedAdmin = this.isEmailAuthorizedForAdmin(googleProfile.email);

    // CRITICAL SECURITY ENFORCEMENT:
    // If the request explicitly attempts Admin Login, ensure the Google account is strictly allowlisted.
    if (isAdminLogin && !isAllowedAdmin) {
      this.logger.warn(
        `SECURITY: Unauthorized Google admin login attempt by ${googleProfile.email} (Google ID: ${googleProfile.googleId})`,
      );
      throw new ForbiddenException('You are not authorized to access the administrator account.');
    }

    // 1. Check if user already exists by Google ID
    let user = await this.prisma.user.findUnique({
      where: { googleId: googleProfile.googleId },
      include: {
        customer: true,
        admin: true,
        staff: true,
      },
    });

    // 2. If not found by Google ID, check if user exists by verified email (Safe Account Linking)
    if (!user) {
      user = await this.prisma.user.findUnique({
        where: { email: googleProfile.email },
        include: {
          customer: true,
          admin: true,
          staff: true,
        },
      });

      if (user) {
        // Link Google ID to existing verified user account
        user = await this.prisma.user.update({
          where: { id: user.id },
          data: {
            googleId: googleProfile.googleId,
            isVerified: true,
          },
          include: {
            customer: true,
            admin: true,
            staff: true,
          },
        });
        this.logger.log(`Linked Google ID ${googleProfile.googleId} to existing user ${user.email} (${user.id})`);
      }
    }

    // 3. If user still does not exist, create a new User record
    if (!user) {
      const targetRole = isAdminLogin && isAllowedAdmin ? UserRole.ADMIN : UserRole.CUSTOMER;

      user = await this.prisma.user.create({
        data: {
          email: googleProfile.email,
          googleId: googleProfile.googleId,
          role: targetRole,
          isVerified: true,
          isActive: true,
          customer:
            targetRole === UserRole.CUSTOMER
              ? {
                  create: {
                    firstName: googleProfile.firstName,
                    lastName: googleProfile.lastName,
                    avatarUrl: googleProfile.avatarUrl,
                  },
                }
              : undefined,
          admin:
            targetRole === UserRole.ADMIN
              ? {
                  create: {
                    fullName: `${googleProfile.firstName} ${googleProfile.lastName}`.trim() || 'Administrator',
                  },
                }
              : undefined,
        },
        include: {
          customer: true,
          admin: true,
          staff: true,
        },
      });

      this.logger.log(
        `Created new ${targetRole} user via Google OAuth: ${user.email} (${user.id})`,
      );
    } else {
      // If user exists and is authorized for Admin, handle role alignment if admin login requested
      if (isAdminLogin && isAllowedAdmin && user.role !== UserRole.ADMIN && user.role !== UserRole.SUPER_ADMIN) {
        user = await this.prisma.user.update({
          where: { id: user.id },
          data: {
            role: UserRole.ADMIN,
            admin: {
              upsert: {
                create: {
                  fullName: `${googleProfile.firstName} ${googleProfile.lastName}`.trim() || 'Administrator',
                },
                update: {},
              },
            },
          },
          include: {
            customer: true,
            admin: true,
            staff: true,
          },
        });
        this.logger.log(`Promoted allowlisted user ${user.email} to ADMIN role.`);
      }
    }

    // 4. Verify account active state
    if (!user.isActive) {
      throw new UnauthorizedException('Your account has been deactivated. Please contact support.');
    }

    // 5. Final guard against non-admin users obtaining admin token if isAdminLogin was passed
    if (isAdminLogin && user.role !== UserRole.ADMIN && user.role !== UserRole.SUPER_ADMIN) {
      throw new ForbiddenException('You are not authorized to access the administrator account.');
    }

    const token = this.generateToken(user.id, user.email, user.role);

    // Audit log
    await this.prisma.auditLog.create({
      data: {
        userId: user.id,
        action: isAdminLogin ? 'AUTH_GOOGLE_ADMIN_LOGIN' : 'AUTH_GOOGLE_CUSTOMER_LOGIN',
        entity: 'User',
        entityId: user.id,
      },
    });

    return {
      accessToken: token,
      user: {
        id: user.id,
        email: user.email,
        role: user.role,
        profile: user.customer || user.admin || user.staff,
      },
    };
  }

  async handleGoogleOAuthCallback(code: string, state?: string) {
    let isAdmin = false;
    if (state) {
      try {
        const decodedState = JSON.parse(Buffer.from(state, 'base64url').toString('utf8'));
        const secret = this.configService.get<string>('JWT_SECRET', 'secret');
        const expectedSig = crypto.createHmac('sha256', secret).update(decodedState.payload).digest('hex');
        if (expectedSig === decodedState.sig) {
          const payload = JSON.parse(decodedState.payload);
          isAdmin = payload.isAdmin === true;
        }
      } catch (e) {
        this.logger.warn('Could not parse or verify OAuth state signature, defaulting to customer login.');
      }
    }

    return this.loginWithGoogle(code, isAdmin);
  }

  // ===========================================================================
  // 4. CUSTOMER MOBILE NUMBER + OTP AUTHENTICATION
  // ===========================================================================

  cleanIndianPhone(rawPhone: string): string {
    let clean = rawPhone.replace(/[^0-9]/g, '');
    if (clean.length === 12 && clean.startsWith('91')) {
      clean = clean.substring(2);
    } else if (clean.length === 11 && clean.startsWith('0')) {
      clean = clean.substring(1);
    }

    if (clean.length !== 10 || !['6', '7', '8', '9'].includes(clean.charAt(0))) {
      throw new BadRequestException('Please provide a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.');
    }

    return clean;
  }

  async sendOtp(dto: SendOtpDto) {
    const phone = this.cleanIndianPhone(dto.phone);
    const redis = this.redisService.getClient();

    // 1. Check Cooldown (45 seconds)
    const cooldownKey = `bns:otp:cd:${phone}`;
    const inCooldown = await redis.get(cooldownKey);
    if (inCooldown) {
      const ttl = await redis.ttl(cooldownKey);
      throw new BadRequestException(`Please wait ${ttl > 0 ? ttl : 45} seconds before requesting another OTP.`);
    }

    // 2. Check Rate Limit (5 requests per 15 minutes)
    const rateKey = `bns:otp:rate:${phone}`;
    const requestCount = parseInt((await redis.get(rateKey)) || '0', 10);
    if (requestCount >= 5) {
      throw new BadRequestException('Too many OTP requests. Please wait 15 minutes before trying again.');
    }

    // 3. Generate Cryptographically Secure 6-digit OTP
    const otp = crypto.randomInt(100000, 1000000).toString();

    // 4. Dispatch Real SMS via SMS Gateway Provider
    const smsResult = await this.smsService.sendOtpSms(phone, otp);
    if (!smsResult.success) {
      this.logger.error(
        `[AuthService] SMS delivery failed for +91 ${phone.substring(0, 5)} •••${phone.substring(8)}: ${smsResult.error}`,
      );
      throw new BadRequestException(
        smsResult.error || 'We could not send the OTP right now. Please verify SMS provider configuration or try again.',
      );
    }

    // 5. Store OTP Hash in Redis with 5-minute (300s) TTL ONLY after successful gateway dispatch
    const otpHash = await bcrypt.hash(otp, 10);
    const otpKey = `bns:otp:${phone}`;

    const otpPayload = JSON.stringify({
      hash: otpHash,
      attempts: 0,
      phone,
      createdAt: Date.now(),
    });

    await redis.set(otpKey, otpPayload, 'EX', 300);
    await redis.set(cooldownKey, '1', 'EX', 45); // 45 seconds cooldown
    await redis.set(rateKey, (requestCount + 1).toString(), 'EX', 900); // 15 minutes

    const maskedPhone = `+91 ${phone.substring(0, 5)} •••${phone.substring(8)}`;

    return {
      success: true,
      message: `OTP sent successfully to ${maskedPhone}`,
      maskedPhone,
      phone,
      cooldownSeconds: 45,
      expiresInSeconds: 300,
    };
  }

  async verifyOtp(dto: VerifyOtpDto) {
    const phone = this.cleanIndianPhone(dto.phone);
    const otp = dto.otp.trim().replace(/[^0-9]/g, '');

    if (otp.length !== 6) {
      throw new BadRequestException('Please enter a valid 6-digit OTP code.');
    }

    const redis = this.redisService.getClient();
    const otpKey = `bns:otp:${phone}`;
    const rawData = await redis.get(otpKey);

    if (!rawData) {
      throw new BadRequestException('This OTP has expired. Please request a new one.');
    }

    let otpData: { hash: string; attempts: number; phone: string; createdAt: number };
    try {
      otpData = JSON.parse(rawData);
    } catch {
      await redis.del(otpKey);
      throw new BadRequestException('OTP data corrupted. Please request a new OTP.');
    }

    // Check max attempts
    if (otpData.attempts >= 5) {
      await redis.del(otpKey);
      throw new BadRequestException('Too many incorrect attempts. This OTP has been invalidated. Please request a new code.');
    }

    const isValid = await bcrypt.compare(otp, otpData.hash);
    if (!isValid) {
      otpData.attempts += 1;
      const ttl = await redis.ttl(otpKey);
      if (ttl > 0) {
        await redis.set(otpKey, JSON.stringify(otpData), 'EX', ttl);
      }
      const remaining = 5 - otpData.attempts;
      throw new BadRequestException(`Incorrect OTP. ${remaining} attempts remaining. Please try again.`);
    }

    // Valid OTP: delete key immediately to prevent replay attacks
    await redis.del(otpKey);

    // Find or create customer in PostgreSQL
    const formattedPhone = `+91${phone}`;
    let user = await this.prisma.user.findFirst({
      where: {
        OR: [
          { customer: { phone: formattedPhone } },
          { customer: { phone: phone } },
          { email: `${phone}@bookmysmoke.local` },
        ],
      },
      include: {
        customer: true,
        admin: true,
        staff: true,
      },
    });

    let isNewUser = false;
    if (!user) {
      const randomPassword = crypto.randomBytes(24).toString('hex');
      const passwordHash = await bcrypt.hash(randomPassword, 10);

      user = await this.prisma.user.create({
        data: {
          email: `${phone}@bookmysmoke.local`,
          passwordHash,
          role: UserRole.CUSTOMER,
          isVerified: true,
          customer: {
            create: {
              firstName: 'Customer',
              lastName: phone.slice(-4),
              phone: formattedPhone,
              city: 'Kolkata',
            },
          },
        },
        include: {
          customer: true,
          admin: true,
          staff: true,
        },
      });
      isNewUser = true;
    }

    const accessToken = this.generateToken(user.id, user.email, user.role);

    return {
      success: true,
      message: 'Mobile number verified successfully.',
      accessToken,
      isNewUser,
      user: {
        id: user.id,
        name: user.customer ? `${user.customer.firstName} ${user.customer.lastName}` : 'Customer',
        phone: formattedPhone,
        email: user.email,
        role: user.role,
      },
    };
  }

  private generateToken(userId: string, email: string, role: string): string {
    return this.jwtService.sign({
      sub: userId,
      email,
      role,
    });
  }
}


