import { Test, TestingModule } from '@nestjs/testing';
import { AuthService } from './auth.service';
import { PrismaService } from '../prisma/prisma.service';
import { JwtService } from '@nestjs/jwt';
import { ConfigService } from '@nestjs/config';
import { RedisService } from '../common/redis/redis.service';
import { SmsService } from '../notifications/sms.service';
import {
  UnauthorizedException,
  ConflictException,
  BadRequestException,
} from '@nestjs/common';
import { UserRole } from '@prisma/client';
import * as bcrypt from 'bcryptjs';

describe('AuthService (Email-Only Authentication & OTP)', () => {
  let service: AuthService;
  let prisma: any;
  let jwt: any;
  let config: any;
  let redis: any;
  let sms: any;

  beforeEach(async () => {
    prisma = {
      user: {
        findUnique: jest.fn(),
        findFirst: jest.fn(),
        create: jest.fn(),
        update: jest.fn(),
      },
      customer: {
        findUnique: jest.fn(),
      },
      auditLog: {
        create: jest.fn().mockResolvedValue({ id: 'audit-1' }),
      },
    };

    jwt = {
      sign: jest.fn().mockReturnValue('mock-jwt-token-xyz'),
    };

    config = {
      get: jest.fn((key: string, defaultVal = '') => {
        if (key === 'JWT_SECRET') return 'test_jwt_secret_key_12345678901234567890';
        return defaultVal;
      }),
    };

    const redisStore: Record<string, string> = {};
    redis = {
      getClient: jest.fn().mockReturnValue({
        get: jest.fn((key: string) => Promise.resolve(redisStore[key] || null)),
        set: jest.fn((key: string, val: string) => {
          redisStore[key] = val;
          return Promise.resolve('OK');
        }),
        del: jest.fn((key: string) => {
          delete redisStore[key];
          return Promise.resolve(1);
        }),
        ttl: jest.fn().mockResolvedValue(290),
      }),
    };

    sms = {
      sendOtpSms: jest.fn().mockResolvedValue({ success: true, messageId: 'msg-1' }),
    };

    const module: TestingModule = await Test.createTestingModule({
      providers: [
        AuthService,
        { provide: PrismaService, useValue: prisma },
        { provide: JwtService, useValue: jwt },
        { provide: ConfigService, useValue: config },
        { provide: RedisService, useValue: redis },
        { provide: SmsService, useValue: sms },
      ],
    }).compile();

    service = module.get<AuthService>(AuthService);
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  // ===========================================================================
  // 1. EMAIL REGISTRATION TESTS
  // ===========================================================================
  describe('Email Registration', () => {
    it('should successfully register a new customer account with hashed password', async () => {
      prisma.user.findUnique.mockResolvedValue(null);
      prisma.customer.findUnique.mockResolvedValue(null);
      prisma.user.create.mockImplementation((args: any) =>
        Promise.resolve({
          id: 'usr-new-1',
          email: args.data.email,
          passwordHash: args.data.passwordHash,
          role: args.data.role,
          isVerified: false,
          customer: {
            id: 'cust-1',
            firstName: args.data.customer.create.firstName,
            lastName: args.data.customer.create.lastName,
            phone: args.data.customer.create.phone,
          },
        }),
      );

      const result = await service.register({
        email: 'newcustomer@shisharent.com',
        password: 'Password123!',
        firstName: 'Rahul',
        lastName: 'Das',
        phone: '+919903556825',
        city: 'Kolkata',
      });

      expect(result.accessToken).toBe('mock-jwt-token-xyz');
      expect(result.user.email).toBe('newcustomer@shisharent.com');
      expect(result.user.role).toBe(UserRole.CUSTOMER);
      expect(prisma.user.create).toHaveBeenCalled();
    });

    it('should reject registration if email is already taken with 409 Conflict', async () => {
      prisma.user.findUnique.mockResolvedValue({
        id: 'usr-existing-1',
        email: 'taken@shisharent.com',
      });

      await expect(
        service.register({
          email: 'taken@shisharent.com',
          password: 'Password123!',
          firstName: 'Rahul',
          lastName: 'Das',
          phone: '+919903556825',
        }),
      ).rejects.toThrow(ConflictException);
    });

    it('should reject registration if phone number already exists', async () => {
      prisma.user.findUnique.mockResolvedValue(null);
      prisma.customer.findUnique.mockResolvedValue({
        id: 'cust-existing-1',
        phone: '+919903556825',
      });

      await expect(
        service.register({
          email: 'unique@shisharent.com',
          password: 'Password123!',
          firstName: 'Rahul',
          lastName: 'Das',
          phone: '+919903556825',
        }),
      ).rejects.toThrow(ConflictException);
    });
  });

  // ===========================================================================
  // 2. EMAIL LOGIN TESTS
  // ===========================================================================
  describe('Email Login', () => {
    it('should successfully login user with correct email and password', async () => {
      const passwordHash = await bcrypt.hash('SecretPass123!', 10);
      prisma.user.findUnique.mockResolvedValue({
        id: 'usr-std-1',
        email: 'standard.user@shisharent.com',
        passwordHash,
        role: UserRole.CUSTOMER,
        isActive: true,
        customer: { id: 'cust-std-1', firstName: 'Rahul', lastName: 'Das' },
      });

      const result = await service.login({
        email: 'standard.user@shisharent.com',
        password: 'SecretPass123!',
      });

      expect(result.accessToken).toBe('mock-jwt-token-xyz');
      expect(result.user.email).toBe('standard.user@shisharent.com');
      expect(prisma.auditLog.create).toHaveBeenCalledWith(
        expect.objectContaining({
          data: expect.objectContaining({
            action: 'AUTH_LOGIN_SUCCESS',
            userId: 'usr-std-1',
          }),
        }),
      );
    });

    it('should reject login with incorrect password with 401 Unauthorized', async () => {
      const passwordHash = await bcrypt.hash('CorrectPassword!', 10);
      prisma.user.findUnique.mockResolvedValue({
        id: 'usr-std-1',
        email: 'standard.user@shisharent.com',
        passwordHash,
        role: UserRole.CUSTOMER,
        isActive: true,
      });

      await expect(
        service.login({
          email: 'standard.user@shisharent.com',
          password: 'WrongPassword!',
        }),
      ).rejects.toThrow(UnauthorizedException);
    });

    it('should reject login for non-existent email with 401 Unauthorized', async () => {
      prisma.user.findUnique.mockResolvedValue(null);

      await expect(
        service.login({
          email: 'unknown@shisharent.com',
          password: 'SomePassword123!',
        }),
      ).rejects.toThrow(UnauthorizedException);
    });

    it('should reject login for deactivated user with 401 Unauthorized', async () => {
      const passwordHash = await bcrypt.hash('SecretPass123!', 10);
      prisma.user.findUnique.mockResolvedValue({
        id: 'usr-deact-1',
        email: 'deactivated@shisharent.com',
        passwordHash,
        role: UserRole.CUSTOMER,
        isActive: false,
      });

      await expect(
        service.login({
          email: 'deactivated@shisharent.com',
          password: 'SecretPass123!',
        }),
      ).rejects.toThrow(UnauthorizedException);
    });
  });

  // ===========================================================================
  // 3. PROFILE RETRIEVAL TESTS
  // ===========================================================================
  describe('Profile Retrieval', () => {
    it('should retrieve user profile by user ID', async () => {
      prisma.user.findUnique.mockResolvedValue({
        id: 'usr-1',
        email: 'user@shisharent.com',
        role: UserRole.CUSTOMER,
        isActive: true,
        isVerified: true,
        customer: { firstName: 'Rahul', lastName: 'Das' },
        createdAt: new Date(),
      });

      const profile = await service.getProfile('usr-1');
      expect(profile.email).toBe('user@shisharent.com');
      expect(profile.role).toBe(UserRole.CUSTOMER);
    });

    it('should throw UnauthorizedException if user not found', async () => {
      prisma.user.findUnique.mockResolvedValue(null);

      await expect(service.getProfile('non-existent')).rejects.toThrow(
        UnauthorizedException,
      );
    });
  });

  // ===========================================================================
  // 4. PHONE NUMBER CLEANING & OTP TESTS
  // ===========================================================================
  describe('Phone Cleaning & OTP Authentication', () => {
    it('should normalize 10-digit Indian phone numbers', () => {
      expect(service.cleanIndianPhone('+919903556825')).toBe('9903556825');
      expect(service.cleanIndianPhone('919903556825')).toBe('9903556825');
      expect(service.cleanIndianPhone('09903556825')).toBe('9903556825');
      expect(service.cleanIndianPhone('99035-56825')).toBe('9903556825');
    });

    it('should reject invalid Indian phone numbers', () => {
      expect(() => service.cleanIndianPhone('12345')).toThrow(BadRequestException);
      expect(() => service.cleanIndianPhone('1234567890')).toThrow(BadRequestException); // starts with 1
      expect(() => service.cleanIndianPhone('0000000000')).toThrow(BadRequestException);
    });
  });
});
