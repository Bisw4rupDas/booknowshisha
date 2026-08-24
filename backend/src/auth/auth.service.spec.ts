import { Test, TestingModule } from '@nestjs/testing';
import { AuthService } from './auth.service';
import { PrismaService } from '../prisma/prisma.service';
import { JwtService } from '@nestjs/jwt';
import { ConfigService } from '@nestjs/config';
import {
  UnauthorizedException,
  ForbiddenException,
  ServiceUnavailableException,
  ConflictException,
} from '@nestjs/common';
import { UserRole } from '@prisma/client';
import * as bcrypt from 'bcryptjs';

describe('AuthService (Standard & Google OAuth Authentication)', () => {
  let service: AuthService;
  let prisma: any;
  let jwt: any;
  let config: any;

  const mockConfigValues: Record<string, string> = {
    GOOGLE_CLIENT_ID: 'placeholder_google_client_id.apps.googleusercontent.com',
    GOOGLE_CLIENT_SECRET: 'placeholder_google_client_secret',
    GOOGLE_CALLBACK_URL: 'http://localhost:3000/api/auth/google/callback',
    ADMIN_GOOGLE_EMAILS: 'admin@shisharent.com,owner@shisharent.com',
    JWT_SECRET: 'test_jwt_secret_key_12345678901234567890',
  };

  beforeEach(async () => {
    prisma = {
      user: {
        findUnique: jest.fn(),
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
      get: jest.fn((key: string, defaultVal = '') => mockConfigValues[key] ?? defaultVal),
    };

    const module: TestingModule = await Test.createTestingModule({
      providers: [
        AuthService,
        { provide: PrismaService, useValue: prisma },
        { provide: JwtService, useValue: jwt },
        { provide: ConfigService, useValue: config },
      ],
    }).compile();

    service = module.get<AuthService>(AuthService);
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  // ===========================================================================
  // 1. PLACEHOLDER & CONFIGURATION HANDLING TESTS
  // ===========================================================================
  describe('Google OAuth Configuration Status & Placeholders', () => {
    it('should detect placeholder Google credentials and mark as NOT configured', () => {
      expect(service.isGoogleAuthConfigured()).toBe(false);

      const status = service.getGoogleConfigStatus();
      expect(status.configured).toBe(false);
      expect(status.clientId).toBeNull();
      expect(status.message).toContain('Google Sign-In is not configured yet');
    });

    it('should throw ServiceUnavailableException if attempting to generate auth URL with placeholder credentials', () => {
      expect(() => service.getGoogleAuthUrl(false)).toThrow(ServiceUnavailableException);
    });

    it('should detect REAL Google credentials when valid values are provided in environment', () => {
      mockConfigValues.GOOGLE_CLIENT_ID = '9876543210-abcdef.apps.googleusercontent.com';
      mockConfigValues.GOOGLE_CLIENT_SECRET = 'GOCSPX-real-secret-12345678';

      expect(service.isGoogleAuthConfigured()).toBe(true);

      const status = service.getGoogleConfigStatus();
      expect(status.configured).toBe(true);
      expect(status.clientId).toBe('9876543210-abcdef.apps.googleusercontent.com');

      const authUrl = service.getGoogleAuthUrl(false);
      expect(authUrl.url).toContain('accounts.google.com');
      expect(authUrl.url).toContain('client_id=9876543210-abcdef.apps.googleusercontent.com');
    });
  });

  // ===========================================================================
  // 2. ADMIN GOOGLE EMAIL ALLOWLIST TESTS
  // ===========================================================================
  describe('Admin Google Email Allowlist Security', () => {
    it('should return TRUE for emails in ADMIN_GOOGLE_EMAILS allowlist (case-insensitive)', () => {
      expect(service.isEmailAuthorizedForAdmin('admin@shisharent.com')).toBe(true);
      expect(service.isEmailAuthorizedForAdmin('ADMIN@SHISHARENT.COM')).toBe(true);
      expect(service.isEmailAuthorizedForAdmin('owner@shisharent.com')).toBe(true);
    });

    it('should return FALSE for unauthorized customer emails', () => {
      expect(service.isEmailAuthorizedForAdmin('customer@gmail.com')).toBe(false);
      expect(service.isEmailAuthorizedForAdmin('hacker@evil.com')).toBe(false);
      expect(service.isEmailAuthorizedForAdmin('admin@otherdomain.com')).toBe(false);
    });
  });

  // ===========================================================================
  // 3. GOOGLE SIGN-IN & USER ROLE ASSIGNMENT TESTS
  // ===========================================================================
  describe('Google User Sign-In & Role Enforcement', () => {
    beforeEach(() => {
      mockConfigValues.GOOGLE_CLIENT_ID = '9876543210-abcdef.apps.googleusercontent.com';
      mockConfigValues.GOOGLE_CLIENT_SECRET = 'GOCSPX-real-secret-12345678';
    });

    it('should create a new CUSTOMER user when a normal user signs in with Google', async () => {
      jest.spyOn(service, 'verifyGoogleIdentity').mockResolvedValue({
        googleId: 'google-sub-1001',
        email: 'customer.kolkata@gmail.com',
        firstName: 'Sourav',
        lastName: 'Ganguly',
        avatarUrl: 'https://lh3.googleusercontent.com/photo.jpg',
        emailVerified: true,
      });

      prisma.user.findUnique.mockResolvedValue(null);
      prisma.user.create.mockResolvedValue({
        id: 'usr-google-1',
        email: 'customer.kolkata@gmail.com',
        googleId: 'google-sub-1001',
        role: UserRole.CUSTOMER,
        isActive: true,
        isVerified: true,
        customer: {
          id: 'cust-1',
          firstName: 'Sourav',
          lastName: 'Ganguly',
          avatarUrl: 'https://lh3.googleusercontent.com/photo.jpg',
        },
      });

      const result = await service.loginWithGoogle('valid-google-token', false);

      expect(result.accessToken).toBe('mock-jwt-token-xyz');
      expect(result.user.role).toBe(UserRole.CUSTOMER);
      expect(result.user.email).toBe('customer.kolkata@gmail.com');
      expect(prisma.user.create).toHaveBeenCalledWith(
        expect.objectContaining({
          data: expect.objectContaining({
            email: 'customer.kolkata@gmail.com',
            role: UserRole.CUSTOMER,
            googleId: 'google-sub-1001',
          }),
        }),
      );
    });

    it('should REJECT admin login if Google email is NOT in ADMIN_GOOGLE_EMAILS allowlist with 403 Forbidden', async () => {
      jest.spyOn(service, 'verifyGoogleIdentity').mockResolvedValue({
        googleId: 'google-sub-9999',
        email: 'unauthorized.user@gmail.com',
        firstName: 'Impostor',
        lastName: 'User',
        avatarUrl: null,
        emailVerified: true,
      });

      await expect(service.loginWithGoogle('valid-google-token', true)).rejects.toThrow(
        ForbiddenException,
      );
    });

    it('should GRANT ADMIN role when an allowlisted Google email logs in with admin flag', async () => {
      jest.spyOn(service, 'verifyGoogleIdentity').mockResolvedValue({
        googleId: 'google-sub-admin-1',
        email: 'admin@shisharent.com',
        firstName: 'Aniket',
        lastName: 'Banerjee',
        avatarUrl: 'https://lh3.googleusercontent.com/admin.jpg',
        emailVerified: true,
      });

      prisma.user.findUnique.mockResolvedValue(null);
      prisma.user.create.mockResolvedValue({
        id: 'usr-admin-1',
        email: 'admin@shisharent.com',
        googleId: 'google-sub-admin-1',
        role: UserRole.ADMIN,
        isActive: true,
        isVerified: true,
        admin: {
          id: 'adm-1',
          fullName: 'Aniket Banerjee',
        },
      });

      const result = await service.loginWithGoogle('valid-admin-token', true);

      expect(result.accessToken).toBe('mock-jwt-token-xyz');
      expect(result.user.role).toBe(UserRole.ADMIN);
      expect(prisma.user.create).toHaveBeenCalledWith(
        expect.objectContaining({
          data: expect.objectContaining({
            email: 'admin@shisharent.com',
            role: UserRole.ADMIN,
          }),
        }),
      );
    });

    it('should SAFE LINK Google ID to existing account with same verified email without duplicate creation', async () => {
      jest.spyOn(service, 'verifyGoogleIdentity').mockResolvedValue({
        googleId: 'google-sub-2002',
        email: 'existing.customer@kolkata.in',
        firstName: 'Debashis',
        lastName: 'Roy',
        avatarUrl: null,
        emailVerified: true,
      });

      // 1st lookup by googleId -> null
      // 2nd lookup by email -> found existing user
      prisma.user.findUnique
        .mockResolvedValueOnce(null)
        .mockResolvedValueOnce({
          id: 'usr-existing-1',
          email: 'existing.customer@kolkata.in',
          googleId: null,
          role: UserRole.CUSTOMER,
          isActive: true,
          isVerified: false,
          customer: { id: 'cust-2', firstName: 'Debashis', lastName: 'Roy' },
        });

      prisma.user.update.mockResolvedValue({
        id: 'usr-existing-1',
        email: 'existing.customer@kolkata.in',
        googleId: 'google-sub-2002',
        role: UserRole.CUSTOMER,
        isActive: true,
        isVerified: true,
        customer: { id: 'cust-2', firstName: 'Debashis', lastName: 'Roy' },
      });

      const result = await service.loginWithGoogle('valid-token', false);

      expect(result.user.id).toBe('usr-existing-1');
      expect(prisma.user.update).toHaveBeenCalledWith(
        expect.objectContaining({
          where: { id: 'usr-existing-1' },
          data: { googleId: 'google-sub-2002', isVerified: true },
        }),
      );
      expect(prisma.user.create).not.toHaveBeenCalled();
    });
  });

  // ===========================================================================
  // 4. EXISTING PASSWORD-BASED AUTHENTICATION VERIFICATION
  // ===========================================================================
  describe('Standard Password Authentication Regression Tests', () => {
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
    });

    it('should reject login with wrong password with 401 Unauthorized', async () => {
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
  });
});
