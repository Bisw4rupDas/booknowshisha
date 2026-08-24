/* eslint-disable @typescript-eslint/no-explicit-any */
import { Test, TestingModule } from '@nestjs/testing';
import { AdminService } from './admin.service';
import { PrismaService } from '../prisma/prisma.service';
import { PinServiceabilityService } from '../delivery/serviceability/pin-serviceability.service';
import { PaymentMethod, PaymentStatus, RentalStatus } from '@prisma/client';
import { BadRequestException } from '@nestjs/common';

describe('AdminService', () => {
  let service: AdminService;
  let mockPrisma: any;

  const mockStaffUser = {
    id: 'user-staff-1',
    staff: { id: 'staff-1', fullName: 'Vikram Singh' },
  };

  const mockPayment = {
    id: 'pay-uuid-1',
    paymentNumber: 'SR-PAY-1234',
    amount: 1649.0,
    status: PaymentStatus.PENDING,
    method: PaymentMethod.COD,
    orderId: 'order-1',
    order: {
      booking: {
        rental: { id: 'rental-1' },
      },
    },
  };

  beforeEach(async () => {
    mockPrisma = {
      user: { findUnique: jest.fn().mockResolvedValue(mockStaffUser) },
      payment: {
        findMany: jest.fn().mockResolvedValue([mockPayment]),
        findUnique: jest.fn().mockResolvedValue(mockPayment),
        update: jest.fn().mockResolvedValue({ ...mockPayment, status: PaymentStatus.SUCCESS }),
        aggregate: jest.fn().mockResolvedValue({ _sum: { amount: 50000.0 } }),
      },
      cashCollection: {
        create: jest.fn().mockResolvedValue({ id: 'cash-1', amount: 1649.0, reconciled: true }),
      },
      rental: {
        update: jest.fn().mockResolvedValue({ id: 'rental-1', status: RentalStatus.ACTIVE }),
        count: jest.fn().mockResolvedValue(12),
      },
      booking: { count: jest.fn().mockResolvedValue(45) },
      hookahInventory: { count: jest.fn().mockResolvedValue(18) },
      flavour: { count: jest.fn().mockResolvedValue(24) },
      auditLog: { create: jest.fn().mockResolvedValue({ id: 'audit-1' }) },
      $transaction: jest.fn().mockImplementation(async (callback) => {
        return callback(mockPrisma);
      }),
    };

    const module: TestingModule = await Test.createTestingModule({
      providers: [
        AdminService,
        PinServiceabilityService,
        { provide: PrismaService, useValue: mockPrisma },
      ],
    }).compile();

    service = module.get<AdminService>(AdminService);
  });

  it('should list pending COD payments', async () => {
    const list = await service.getPendingCodPayments();
    expect(list).toHaveLength(1);
    expect(list[0].method).toBe(PaymentMethod.COD);
  });

  it('should successfully record full COD collection and activate rental', async () => {
    const result = await service.collectCod('user-staff-1', 'pay-uuid-1', {
      amount: 1649.0,
      notes: 'Collected full cash at customer doorstep',
    });

    expect(result.success).toBe(true);
    expect(result.data.isFullyCollected).toBe(true);
    expect(mockPrisma.rental.update).toHaveBeenCalledWith({
      where: { id: 'rental-1' },
      data: { status: RentalStatus.ACTIVE },
    });
  });

  it('should record partial COD collection without marking payment fully reconciled', async () => {
    const result = await service.collectCod('user-staff-1', 'pay-uuid-1', {
      amount: 1000.0,
      isPartial: true,
      notes: 'Customer paid ₹1000, remaining balance ₹649 pending',
    });

    expect(result.success).toBe(true);
    expect(result.data.isFullyCollected).toBe(false);
  });

  it('should reject COD collection if payment is already collected', async () => {
    mockPrisma.payment.findUnique.mockResolvedValue({
      ...mockPayment,
      status: PaymentStatus.SUCCESS,
    });

    await expect(
      service.collectCod('user-staff-1', 'pay-uuid-1', { amount: 1649.0 }),
    ).rejects.toThrow(BadRequestException);
  });

  it('should return platform operational KPIs and revenue metrics', async () => {
    const metrics = await service.getPlatformMetrics();
    expect(metrics).toBeDefined();
    expect(metrics.totalBookings).toBe(45);
    expect(metrics.activeRentals).toBe(12);
    expect(metrics.availableHookahs).toBe(18);
    expect(metrics.totalRevenue).toBe(50000.0);
  });
});
