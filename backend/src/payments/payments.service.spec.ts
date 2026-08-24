/* eslint-disable @typescript-eslint/no-explicit-any */
import { Test, TestingModule } from '@nestjs/testing';
import { PaymentsService } from './payments.service';
import { PrismaService } from '../prisma/prisma.service';
import { ConfigService } from '@nestjs/config';
import { PaymentMethod, PaymentStatus, OrderStatus, RentalStatus } from '@prisma/client';

describe('PaymentsService', () => {
  let service: PaymentsService;
  let mockPrisma: any;
  let mockConfig: any;

  const mockBooking = {
    id: 'booking-uuid-1',
    customerId: 'cust-uuid-1',
    totalAmount: 1649.0,
    depositAmount: 1500.0,
    package: { name: 'Solo Standard 24H Package' },
    rental: { id: 'rental-uuid-1' },
  };

  beforeEach(async () => {
    mockPrisma = {
      booking: { findUnique: jest.fn().mockResolvedValue(mockBooking) },
      order: {
        create: jest.fn().mockResolvedValue({
          id: 'order-uuid-1',
          orderNumber: 'SR-ORD-1234',
          totalAmount: 1649.0,
        }),
        update: jest.fn().mockResolvedValue({ id: 'order-uuid-1', status: OrderStatus.CONFIRMED }),
      },
      payment: {
        create: jest.fn().mockResolvedValue({
          id: 'pay-uuid-1',
          paymentNumber: 'SR-PAY-1234',
          amount: 1649.0,
          status: PaymentStatus.PENDING,
        }),
        findUnique: jest.fn(),
        findFirst: jest.fn(),
        findMany: jest.fn(),
        update: jest.fn(),
      },
      rental: {
        update: jest
          .fn()
          .mockResolvedValue({ id: 'rental-uuid-1', status: RentalStatus.PREPARING }),
      },
      auditLog: {
        create: jest.fn().mockResolvedValue({ id: 'audit-1' }),
      },
      $transaction: jest.fn().mockImplementation(async (callback) => {
        return callback(mockPrisma);
      }),
    };

    mockConfig = {
      get: jest.fn().mockReturnValue('placeholder_upi_webhook_secret'),
    };

    const module: TestingModule = await Test.createTestingModule({
      providers: [
        PaymentsService,
        { provide: PrismaService, useValue: mockPrisma },
        { provide: ConfigService, useValue: mockConfig },
      ],
    }).compile();

    service = module.get<PaymentsService>(PaymentsService);
  });

  it('should initiate COD payment successfully', async () => {
    const result = await service.initiatePayment('user-1', {
      bookingId: 'booking-uuid-1',
      method: PaymentMethod.COD,
      amount: 1649.0,
    });

    expect(result).toBeDefined();
    expect(result.method).toBe(PaymentMethod.COD);
    expect(result.instructions).toContain('Cash on Delivery');
  });

  it('should initiate UPI payment with dynamic UPI intent link and QR payload', async () => {
    const result = await service.initiateUpi('user-1', {
      bookingId: 'booking-uuid-1',
      amount: 1649.0,
    });

    expect(result).toBeDefined();
    expect(result.method).toBe(PaymentMethod.UPI);
    expect(result.upiIntent).toBeDefined();
    expect(result.upiIntent).toContain('upi://pay?pa=');
  });

  it('should process webhook and transition payment/order/rental to active state', async () => {
    const existingPayment = {
      id: 'pay-uuid-1',
      paymentNumber: 'SR-PAY-1234',
      orderId: 'order-uuid-1',
      status: PaymentStatus.PENDING,
      order: {
        booking: {
          rental: { id: 'rental-uuid-1' },
        },
      },
    };

    mockPrisma.payment.findFirst.mockResolvedValue(existingPayment);
    mockPrisma.payment.update.mockResolvedValue({
      ...existingPayment,
      status: PaymentStatus.SUCCESS,
    });

    const result = await service.processWebhook({
      paymentNumber: 'SR-PAY-1234',
      gatewayTxnId: 'UPI-TXN-9999',
      status: 'SUCCESS',
      amount: 1649.0,
    });

    expect(result.success).toBe(true);
    expect(result.status).toBe(PaymentStatus.SUCCESS);
  });

  it('should be idempotent for duplicate webhook callbacks', async () => {
    const alreadySuccessPayment = {
      id: 'pay-uuid-1',
      paymentNumber: 'SR-PAY-1234',
      status: PaymentStatus.SUCCESS,
    };

    mockPrisma.payment.findFirst.mockResolvedValue(alreadySuccessPayment);

    const result = await service.processWebhook({
      paymentNumber: 'SR-PAY-1234',
      gatewayTxnId: 'UPI-TXN-9999',
      status: 'SUCCESS',
      amount: 1649.0,
    });

    expect(result.received).toBe(true);
    expect(result.alreadyProcessed).toBe(true);
    expect(mockPrisma.payment.update).not.toHaveBeenCalled();
  });
});
