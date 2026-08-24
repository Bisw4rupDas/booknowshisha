/* eslint-disable @typescript-eslint/no-explicit-any */
import { Test, TestingModule } from '@nestjs/testing';
import { ReturnsService } from './returns.service';
import { PrismaService } from '../prisma/prisma.service';
import { InspectionStatus, RentalStatus, HookahInventoryStatus } from '@prisma/client';

describe('ReturnsService', () => {
  let service: ReturnsService;
  let mockPrisma: any;

  const mockStaffUser = {
    id: 'user-staff-1',
    staff: { id: 'staff-1', fullName: 'Vikram Singh' },
  };

  const mockRental = {
    id: 'rental-uuid-1',
    rentalNumber: 'SR-RN-1234',
    status: RentalStatus.ACTIVE,
    securityDeposit: {
      id: 'deposit-1',
      amount: 1500.0,
    },
    items: [{ hookahInventoryId: 'inv-unit-1' }],
  };

  beforeEach(async () => {
    mockPrisma = {
      user: { findUnique: jest.fn().mockResolvedValue(mockStaffUser) },
      rental: {
        findUnique: jest.fn().mockResolvedValue(mockRental),
        findMany: jest.fn().mockResolvedValue([mockRental]),
        update: jest.fn().mockResolvedValue({ ...mockRental, status: RentalStatus.COMPLETED }),
      },
      returnInspection: {
        upsert: jest.fn().mockResolvedValue({ id: 'insp-1', status: InspectionStatus.PASSED }),
        findUnique: jest.fn().mockResolvedValue({ id: 'insp-1', status: InspectionStatus.PASSED }),
      },
      damageReport: {
        create: jest.fn().mockResolvedValue({ id: 'dmg-1', damageCost: 500.0 }),
      },
      hookahInventory: {
        update: jest
          .fn()
          .mockResolvedValue({ id: 'inv-unit-1', status: HookahInventoryStatus.AVAILABLE }),
      },
      securityDeposit: {
        update: jest
          .fn()
          .mockResolvedValue({ id: 'deposit-1', isRefunded: true, refundAmount: 1500.0 }),
      },
      auditLog: {
        create: jest.fn().mockResolvedValue({ id: 'audit-1' }),
      },
      $transaction: jest.fn().mockImplementation(async (callback) => {
        return callback(mockPrisma);
      }),
    };

    const module: TestingModule = await Test.createTestingModule({
      providers: [ReturnsService, { provide: PrismaService, useValue: mockPrisma }],
    }).compile();

    service = module.get<ReturnsService>(ReturnsService);
  });

  it('should process passed return inspection and release inventory & full deposit', async () => {
    const result = await service.processReturn('user-staff-1', 'rental-uuid-1', {
      status: InspectionStatus.PASSED,
      isClean: true,
      allPartsPresent: true,
      notes: 'Returned in mint condition',
    });

    expect(result.success).toBe(true);
    expect(mockPrisma.hookahInventory.update).toHaveBeenCalledWith({
      where: { id: 'inv-unit-1' },
      data: { status: HookahInventoryStatus.AVAILABLE },
    });
    expect(mockPrisma.securityDeposit.update).toHaveBeenCalledWith({
      where: { id: 'deposit-1' },
      data: expect.objectContaining({
        isRefunded: true,
        refundAmount: 1500.0,
      }),
    });
  });

  it('should flag inventory for maintenance if return inspection fails/damaged', async () => {
    const result = await service.processReturn('user-staff-1', 'rental-uuid-1', {
      status: InspectionStatus.DAMAGED,
      isClean: false,
      allPartsPresent: false,
      notes: 'Base bowl cracked and tongs missing',
    });

    expect(result.success).toBe(true);
    expect(mockPrisma.hookahInventory.update).toHaveBeenCalledWith({
      where: { id: 'inv-unit-1' },
      data: { status: HookahInventoryStatus.IN_MAINTENANCE },
    });
  });

  it('should report damage, deduct damage amount, and release partial deposit', async () => {
    const result = await service.reportDamage('user-staff-1', 'rental-uuid-1', {
      description: 'Borosilicate vase bowl cracked',
      damageCost: 600.0,
    });

    expect(result.success).toBe(true);
    expect(mockPrisma.securityDeposit.update).toHaveBeenCalledWith({
      where: { id: 'deposit-1' },
      data: expect.objectContaining({
        isRefunded: true,
        deductionAmount: 600.0,
        refundAmount: 900.0, // 1500 - 600
      }),
    });
  });

  it('should handle excess damage exceeding deposit amount without negative refund', async () => {
    const result = await service.reportDamage('user-staff-1', 'rental-uuid-1', {
      description: 'Entire premium stem and vase destroyed',
      damageCost: 2500.0, // deposit is 1500
    });

    expect(result.success).toBe(true);
    expect(mockPrisma.securityDeposit.update).toHaveBeenCalledWith({
      where: { id: 'deposit-1' },
      data: expect.objectContaining({
        isRefunded: true,
        deductionAmount: 1500.0,
        refundAmount: 0.0, // Math.max(0, 1500 - 2500)
      }),
    });
  });
});
