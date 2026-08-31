import {
  Injectable,
  ConflictException,
  UnauthorizedException,
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
  // 1. STANDARD EMAIL / PASSWORD AUTHENTICATION (CUSTOMER & ADMIN)
  // ===========================================================================

  async register(dto: RegisterDto) {
    const normalizedEmail = dto.email.trim().toLowerCase();

    // Airtight Duplicate Email Check
    const existingUser = await this.prisma.user.findUnique({
      where: { email: normalizedEmail },
    });

    if (existingUser) {
      throw new ConflictException('An account with this email address already exists. Please log in or reset your password.');
    }

    if (dto.phone) {
      const existingCustomerPhone = await this.prisma.customer.findUnique({
        where: { phone: dto.phone },
      });

      if (existingCustomerPhone) {
        throw new ConflictException('A customer with this phone number already exists.');
      }
    }

    const passwordHash = await bcrypt.hash(dto.password, 10);

    const user = await this.prisma.user.create({
      data: {
        email: normalizedEmail,
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
    const normalizedEmail = dto.email.trim().toLowerCase();

    const user = await this.prisma.user.findUnique({
      where: { email: normalizedEmail },
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
      throw new UnauthorizedException('Your account has been deactivated. Please contact customer support.');
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
  // 2. CUSTOMER MOBILE NUMBER + OTP AUTHENTICATION
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

    if (this.redisService.isAvailable() && redis) {
      // 1. Check Cooldown (45 seconds) via Redis
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
          `[AuthService] SMS delivery failed for +91 ${phone.substring(0, 5)} xxxxx: ${smsResult.error}`,
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
      await redis.set(cooldownKey, '1', 'EX', 45);
      await redis.set(rateKey, (requestCount + 1).toString(), 'EX', 900);
    } else {
      // Database-backed OTP management (multi-process safe on cPanel MySQL)
      const now = new Date();
      const existing = await this.prisma.otpVerification.findUnique({ where: { phone } });

      if (existing && existing.cooldownAt > now) {
        const remainingSeconds = Math.ceil((existing.cooldownAt.getTime() - now.getTime()) / 1000);
        throw new BadRequestException(`Please wait ${remainingSeconds > 0 ? remainingSeconds : 45} seconds before requesting another OTP.`);
      }

      if (existing && existing.attempts >= 5 && existing.expiresAt > now) {
        throw new BadRequestException('Too many OTP requests. Please wait 15 minutes before trying again.');
      }

      const otp = crypto.randomInt(100000, 1000000).toString();

      const smsResult = await this.smsService.sendOtpSms(phone, otp);
      if (!smsResult.success) {
        this.logger.error(
          `[AuthService] SMS delivery failed for +91 ${phone.substring(0, 5)} xxxxx: ${smsResult.error}`,
        );
        throw new BadRequestException(
          smsResult.error || 'We could not send the OTP right now. Please verify SMS provider configuration or try again.',
        );
      }

      const otpHash = await bcrypt.hash(otp, 10);
      const expiresAt = new Date(Date.now() + 5 * 60 * 1000);
      const cooldownAt = new Date(Date.now() + 45 * 1000);

      await this.prisma.otpVerification.upsert({
        where: { phone },
        update: {
          otpHash,
          attempts: 0,
          expiresAt,
          cooldownAt,
        },
        create: {
          phone,
          otpHash,
          attempts: 0,
          expiresAt,
          cooldownAt,
        },
      });
    }

    const maskedPhone = `+91 ${phone.substring(0, 5)} xxxxx${phone.substring(8)}`;

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

    if (this.redisService.isAvailable() && redis) {
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
    } else {
      // Database-backed OTP verification
      const record = await this.prisma.otpVerification.findUnique({ where: { phone } });

      if (!record || record.expiresAt < new Date()) {
        throw new BadRequestException('This OTP has expired. Please request a new one.');
      }

      if (record.attempts >= 5) {
        await this.prisma.otpVerification.delete({ where: { phone } }).catch(() => {});
        throw new BadRequestException('Too many incorrect attempts. This OTP has been invalidated. Please request a new code.');
      }

      const isValid = await bcrypt.compare(otp, record.otpHash);
      if (!isValid) {
        const updated = await this.prisma.otpVerification.update({
          where: { phone },
          data: { attempts: { increment: 1 } },
        });
        const remaining = Math.max(0, 5 - updated.attempts);
        throw new BadRequestException(`Incorrect OTP. ${remaining} attempts remaining. Please try again.`);
      }

      // Valid OTP: delete record immediately
      await this.prisma.otpVerification.delete({ where: { phone } }).catch(() => {});
    }

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
