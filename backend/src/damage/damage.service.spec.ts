import { Test, TestingModule } from '@nestjs/testing';
import { DamageService } from './damage.service';
import { PrismaService } from '../prisma/prisma.service';
import { NotFoundException } from '@nestjs/common';
import { RentalStatus } from '@prisma/client';

describe('DamageService', () => {
  let service: DamageService;
  let prisma: PrismaService;

  const mockRental = {
    id: 'rental-uuid-1',
    rentalNumber: 'RNT-20260822-ABCD',
    status: RentalStatus.ACTIVE,
    securityDeposit: {
      id: 'deposit-uuid-1',
      rentalId: 'rental-uuid-1',
      amount: 2000,
      deductionAmount: 0,
      refundAmount: 2000,
    },
    items: [
      { id: 'item-1', hookahInventoryId: 'inv-unit-1' },
    ],
  };

  const mockDamageReport = {
    id: 'damage-uuid-1',
    rentalId: 'rental-uuid-1',
    description: 'Cracked crystal glass base',
    damageCost: 1200,
    photos: ['https://example.com/photo.jpg'],
  };

  const mockPrismaService: any = {
    rental: {
      findUnique: jest.fn(),
      update: jest.fn(),
    },
    damageReport: {
      create: jest.fn(),
      findMany: jest.fn(),
      findUnique: jest.fn(),
      count: jest.fn(),
    },
    securityDeposit: {
      update: jest.fn(),
    },
    hookahInventory: {
      updateMany: jest.fn(),
    },
    auditLog: {
      create: jest.fn(),
    },
    $transaction: jest.fn((callback: any) => callback(mockPrismaService)),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        DamageService,
        { provide: PrismaService, useValue: mockPrismaService },
      ],
    }).compile();

    service = module.get<DamageService>(DamageService);
    prisma = module.get<PrismaService>(PrismaService);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  describe('createDamageReport', () => {
    it('should create damage report and deduct from security deposit', async () => {
      mockPrismaService.rental.findUnique.mockResolvedValue(mockRental);
      mockPrismaService.damageReport.create.mockResolvedValue(mockDamageReport);

      const result = await service.createDamageReport({
        rentalId: 'rental-uuid-1',
        description: 'Cracked crystal glass base',
        damageCost: 1200,
        autoDeductFromDeposit: true,
      });

      expect(result).toBeDefined();
      expect(mockPrismaService.securityDeposit.update).toHaveBeenCalledWith({
        where: { id: 'deposit-uuid-1' },
        data: expect.objectContaining({
          deductionAmount: 1200,
          refundAmount: 800,
        }),
      });
    });

    it('should throw NotFoundException if rental does not exist', async () => {
      mockPrismaService.rental.findUnique.mockResolvedValue(null);

      await expect(
        service.createDamageReport({
          rentalId: 'non-existent',
          description: 'Broken hose',
          damageCost: 500,
        }),
      ).rejects.toThrow(NotFoundException);
    });
  });
});
