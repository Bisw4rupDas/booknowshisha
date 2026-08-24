/* eslint-disable @typescript-eslint/no-explicit-any */
import { Test, TestingModule } from '@nestjs/testing';
import { BookingsService } from './bookings.service';
import { PrismaService } from '../prisma/prisma.service';
import { RedisService } from '../common/redis/redis.service';
import { PinServiceabilityService } from '../delivery/serviceability/pin-serviceability.service';
import { BadRequestException, ConflictException } from '@nestjs/common';
import { HookahInventoryStatus, RentalStatus } from '@prisma/client';

describe('BookingsService', () => {
  let service: BookingsService;
  let mockPrisma: any;
  let mockRedis: any;

  const mockUser = {
    id: 'user-uuid-1',
    customer: { id: 'cust-uuid-1', firstName: 'Rahul', lastName: 'Sharma' },
  };

  const mockPackage = {
    id: 'pkg-uuid-1',
    name: 'Solo Standard 24H Package',
    price: 1499.0,
    durationHrs: 24,
    maxFlavours: 2,
    isActive: true,
    items: [{ hookahModelId: 'model-uuid-1' }],
  };

  const mockHookahModel = {
    id: 'model-uuid-1',
    name: 'Khalil Mamoon Gold Classic',
    depositFee: 1500.0,
    isActive: true,
  };

  const mockSlot = {
    id: 'slot-uuid-1',
    startTime: '18:00',
    endTime: '20:00',
    maxCapacity: 5,
    isActive: true,
    zone: {
      id: 'zone-uuid-1',
      name: 'Central Kolkata',
      postalCodes: ['700091', '700016'],
      baseFee: 150.0,
      isActive: true,
    },
  };

  const mockFlavours = [
    {
      id: 'flavour-1',
      name: 'Blueberry Mint',
      isActive: true,
      stock: { quantityUnits: 10 },
    },
    {
      id: 'flavour-2',
      name: 'Love 66',
      isActive: true,
      stock: { quantityUnits: 5 },
    },
  ];

  const mockAvailableUnit = {
    id: 'unit-uuid-1',
    serialNumber: 'KM-GLD-001',
    status: HookahInventoryStatus.AVAILABLE,
  };

  const validDto = {
    packageId: 'pkg-uuid-1',
    hookahModelId: 'model-uuid-1',
    flavourIds: ['flavour-1', 'flavour-2'],
    rentalStart: new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString(),
    deliverySlotId: 'slot-uuid-1',
    deliveryAddress: '42, Salt Lake Sector V, Kolkata',
    postalCode: '700091',
  };

  beforeEach(async () => {
    mockPrisma = {
      user: {
        findUnique: jest.fn().mockResolvedValue(mockUser),
        findFirst: jest.fn().mockResolvedValue(mockUser),
        create: jest.fn().mockResolvedValue(mockUser),
      },
      customer: {
        findFirst: jest.fn().mockResolvedValue(mockUser.customer),
      },
      package: {
        findUnique: jest.fn().mockResolvedValue(mockPackage),
        findFirst: jest.fn().mockResolvedValue(mockPackage),
      },
      hookahModel: {
        findUnique: jest.fn().mockResolvedValue(mockHookahModel),
        findFirst: jest.fn().mockResolvedValue(mockHookahModel),
      },
      deliverySlot: {
        findUnique: jest.fn().mockResolvedValue(mockSlot),
        findFirst: jest.fn().mockResolvedValue(mockSlot),
      },
      deliveryZone: {
        findFirst: jest.fn().mockResolvedValue(mockSlot.zone),
      },
      flavour: { findMany: jest.fn().mockResolvedValue(mockFlavours) },
      booking: {
        count: jest.fn().mockResolvedValue(0),
        create: jest.fn(),
        findMany: jest.fn(),
        findUnique: jest.fn(),
      },
      hookahInventory: {
        findFirst: jest.fn().mockResolvedValue(mockAvailableUnit),
      },
      $transaction: jest.fn().mockImplementation(async (callback) => {
        const tx = {
          hookahInventory: {
            updateMany: jest.fn().mockResolvedValue({ count: 1 }),
          },
          booking: {
            create: jest.fn().mockResolvedValue({
              id: 'booking-uuid-1',
              bookingNumber: 'SR-BK-9999',
              totalAmount: 1649.0,
            }),
          },
          rental: {
            create: jest.fn().mockResolvedValue({
              id: 'rental-uuid-1',
              rentalNumber: 'SR-RN-9999',
              status: RentalStatus.RESERVED,
            }),
          },
          delivery: {
            create: jest.fn().mockResolvedValue({ id: 'delivery-uuid-1' }),
          },
          order: {
            create: jest.fn().mockResolvedValue({ id: 'order-uuid-1', orderNumber: 'SR-ORD-9999' }),
          },
          payment: {
            create: jest
              .fn()
              .mockResolvedValue({ id: 'payment-uuid-1', paymentNumber: 'SR-PAY-9999' }),
          },
          flavourStock: {
            updateMany: jest.fn().mockResolvedValue({ count: 1 }),
          },
          auditLog: {
            create: jest.fn().mockResolvedValue({ id: 'audit-uuid-1' }),
          },
        };
        return callback(tx);
      }),
    };

    mockRedis = {
      acquireLock: jest.fn().mockResolvedValue('lock-token-123'),
      releaseLock: jest.fn().mockResolvedValue(true),
    };

    const module: TestingModule = await Test.createTestingModule({
      providers: [
        BookingsService,
        PinServiceabilityService,
        { provide: PrismaService, useValue: mockPrisma },
        { provide: RedisService, useValue: mockRedis },
      ],
    }).compile();

    service = module.get<BookingsService>(BookingsService);
  });

  it('should successfully create booking and acquire/release Redis lock', async () => {
    const result = await service.createBooking('user-uuid-1', validDto);

    expect(result).toBeDefined();
    expect(result.assignedUnit).toBe('KM-GLD-001');
    expect(result.breakdown.totalToPay).toBe(1649.0);
    expect(mockRedis.acquireLock).toHaveBeenCalled();
    expect(mockRedis.releaseLock).toHaveBeenCalled();
  });

  it('should reject booking when rental start date is in the past', async () => {
    const pastDto = {
      ...validDto,
      rentalStart: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString(),
    };

    await expect(service.createBooking('user-uuid-1', pastDto)).rejects.toThrow(
      BadRequestException,
    );
  });

  it('should reject booking when selected flavours exceed package limit', async () => {
    const excessFlavoursDto = {
      ...validDto,
      flavourIds: ['f-1', 'f-2', 'f-3'],
    };

    await expect(service.createBooking('user-uuid-1', excessFlavoursDto)).rejects.toThrow(
      BadRequestException,
    );
  });

  it('should reject booking when flavour is out of stock', async () => {
    mockPrisma.flavour.findMany.mockResolvedValue([
      { id: 'flavour-1', name: 'Blueberry Mint', isActive: true, stock: { quantityUnits: 0 } },
      { id: 'flavour-2', name: 'Love 66', isActive: true, stock: { quantityUnits: 5 } },
    ]);

    await expect(service.createBooking('user-uuid-1', validDto)).rejects.toThrow(ConflictException);
  });

  it('should reject booking when postal PIN is not in delivery zone', async () => {
    const invalidPinDto = {
      ...validDto,
      postalCode: '999999',
    };

    await expect(service.createBooking('user-uuid-1', invalidPinDto)).rejects.toThrow(
      BadRequestException,
    );
  });

  it('should reject booking when delivery slot is at maximum capacity', async () => {
    mockPrisma.booking.count.mockResolvedValue(5); // equal to maxCapacity 5

    await expect(service.createBooking('user-uuid-1', validDto)).rejects.toThrow(ConflictException);
  });

  it('should reject with ConflictException if Redis lock cannot be acquired (concurrency protection)', async () => {
    mockRedis.acquireLock.mockResolvedValue(null);

    await expect(service.createBooking('user-uuid-1', validDto)).rejects.toThrow(ConflictException);
  });

  it('should reject when no physical serialized hookah units are available', async () => {
    mockPrisma.hookahInventory.findFirst.mockResolvedValue(null);

    await expect(service.createBooking('user-uuid-1', validDto)).rejects.toThrow(ConflictException);
    expect(mockRedis.releaseLock).toHaveBeenCalled();
  });

  it('should successfully create booking via WooCommerce bridge with UPI payment', async () => {
    const bridgeDto = {
      packageId: 'solo-standard-24h',
      flavourIds: ['flavour-1', 'flavour-2'],
      rentalStart: new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString(),
      deliverySlotId: '18:00-20:00',
      deliveryAddress: '42, Salt Lake Sector V, Kolkata',
      postalCode: '700091',
      customerEmail: 'guest@example.com',
      customerPhone: '+919903556825',
      customerName: 'Aarav Patel',
      wpOrderId: 1042,
      paymentMethod: 'UPI' as const,
    };

    const result = await service.createBooking('bridge-system-user', bridgeDto);

    expect(result).toBeDefined();
    expect(result.assignedUnit).toBe('KM-GLD-001');
    expect(result.order).toBeDefined();
    expect(result.payment).toBeDefined();
    expect(result.upiIntent).toContain('upi://pay?pa=');
    expect(mockRedis.acquireLock).toHaveBeenCalled();
    expect(mockRedis.releaseLock).toHaveBeenCalled();
  });

  it('should successfully create booking in South 24 Parganas (e.g. 700027 Alipore)', async () => {
    const south24Dto = {
      ...validDto,
      postalCode: '700027',
      deliveryAddress: '15, Alipore Park Road, Kolkata',
    };

    const result = await service.createBooking('user-uuid-1', south24Dto);
    expect(result).toBeDefined();
    expect(result.district).toBe('South 24 Parganas');
  });

  it('should REJECT booking in Howrah (711101) even if customer types city as Kolkata', async () => {
    const howrahDto = {
      ...validDto,
      postalCode: '711101',
      deliveryAddress: 'Howrah Station Road, Kolkata',
    };

    await expect(service.createBooking('user-uuid-1', howrahDto)).rejects.toThrow(
      BadRequestException,
    );
  });

  it('should REJECT booking in Delhi (110001) even if customer attempts to bypass validation', async () => {
    const delhiDto = {
      ...validDto,
      postalCode: '110001',
      deliveryAddress: 'Connaught Place, New Delhi',
    };

    await expect(service.createBooking('user-uuid-1', delhiDto)).rejects.toThrow(
      BadRequestException,
    );
  });
});
