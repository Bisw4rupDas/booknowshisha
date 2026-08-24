import { Test, TestingModule } from '@nestjs/testing';
import { OrdersService } from './orders.service';
import { PrismaService } from '../prisma/prisma.service';
import { PinServiceabilityService } from '../delivery/serviceability/pin-serviceability.service';
import { NotFoundException } from '@nestjs/common';
import { OrderStatus } from '@prisma/client';

describe('OrdersService', () => {
  let service: OrdersService;
  let prisma: PrismaService;

  const mockCustomer = {
    id: 'cust-uuid-1',
    userId: 'user-uuid-1',
    firstName: 'Rahul',
    lastName: 'Sharma',
    phone: '+919903556825',
    postalCode: '700091',
  };

  const mockOrder = {
    id: 'order-uuid-1',
    orderNumber: 'ORD-20260822-ABCD',
    customerId: 'cust-uuid-1',
    status: OrderStatus.CONFIRMED,
    subtotal: 1499,
    totalAmount: 1499,
    items: [],
  };

  const mockPrismaService: any = {
    customer: {
      findUnique: jest.fn(),
      create: jest.fn(),
    },
    user: {
      findUnique: jest.fn(),
      create: jest.fn(),
    },
    order: {
      create: jest.fn(),
      findUnique: jest.fn(),
      findMany: jest.fn(),
      count: jest.fn(),
      update: jest.fn(),
    },
    orderItem: {
      create: jest.fn(),
    },
    auditLog: {
      create: jest.fn(),
    },
    $transaction: jest.fn((callback: any) => callback(mockPrismaService)),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        OrdersService,
        PinServiceabilityService,
        { provide: PrismaService, useValue: mockPrismaService },
      ],
    }).compile();

    service = module.get<OrdersService>(OrdersService);
    prisma = module.get<PrismaService>(PrismaService);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  describe('create', () => {
    it('should create an order successfully with line items', async () => {
      mockPrismaService.customer.findUnique.mockResolvedValue(mockCustomer);
      mockPrismaService.order.create.mockResolvedValue(mockOrder);
      mockPrismaService.order.findUnique.mockResolvedValue(mockOrder);

      const result = await service.create({
        customerId: 'cust-uuid-1',
        totalAmount: 1499,
        items: [
          { name: 'Solo Standard 24H Package', quantity: 1, unitPrice: 1499 },
        ],
      });

      expect(result).toBeDefined();
      expect(mockPrismaService.order.create).toHaveBeenCalled();
    });

    it('should throw NotFoundException if customer does not exist', async () => {
      mockPrismaService.customer.findUnique.mockResolvedValue(null);

      await expect(
        service.create({
          customerId: 'non-existent',
          totalAmount: 1499,
          items: [],
        }),
      ).rejects.toThrow(NotFoundException);
    });
  });
});
