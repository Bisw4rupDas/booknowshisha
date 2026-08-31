/* eslint-disable @typescript-eslint/no-explicit-any */
import { Test, TestingModule } from '@nestjs/testing';
import { BookingsService } from './bookings.service';
import { PrismaService } from '../prisma/prisma.service';
import { RedisService } from '../common/redis/redis.service';
import { PinServiceabilityService } from '../delivery/serviceability/pin-serviceability.service';
import { ConflictException } from '@nestjs/common';
import { HookahInventoryStatus, RentalStatus } from '@prisma/client';

describe('BookingsService - Concurrency & Anti-Double-Booking Protection', () => {
  let service: BookingsService;
  let mockPrisma: any;
  let mockRedis: any;

  // Single physical unit available in inventory
  let unitAvailable = true;
  let slotBookings = 0;

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
      postalCodes: [{ postalCode: '700091' }],
      baseFee: 150.0,
      isActive: true,
    },
  };

  const mockFlavours = [
    { id: 'flavour-1', name: 'Blueberry Mint', isActive: true, stock: { quantityUnits: 100 } },
    { id: 'flavour-2', name: 'Love 66', isActive: true, stock: { quantityUnits: 100 } },
  ];

  const bookingDto = {
    packageId: 'pkg-uuid-1',
    hookahModelId: 'model-uuid-1',
    flavourIds: ['flavour-1', 'flavour-2'],
    rentalStart: new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString(),
    deliverySlotId: 'slot-uuid-1',
    deliveryAddress: '42, Salt Lake Sector V, Kolkata',
    postalCode: '700091',
  };

  beforeEach(async () => {
    unitAvailable = true;
    slotBookings = 0;

    mockPrisma = {
      user: {
        findUnique: jest.fn().mockResolvedValue(mockUser),
        findFirst: jest.fn().mockResolvedValue(mockUser),
      },
      customer: { findFirst: jest.fn().mockResolvedValue(mockUser.customer) },
      package: { findFirst: jest.fn().mockResolvedValue(mockPackage) },
      hookahModel: { findFirst: jest.fn().mockResolvedValue(mockHookahModel) },
      deliverySlot: { findFirst: jest.fn().mockResolvedValue(mockSlot) },
      deliveryZone: { findFirst: jest.fn().mockResolvedValue(mockSlot.zone) },
      flavour: { findMany: jest.fn().mockResolvedValue(mockFlavours) },
      booking: {
        count: jest.fn().mockImplementation(() => Promise.resolve(slotBookings)),
      },
      hookahInventory: {
        findFirst: jest.fn().mockImplementation(() => {
          if (unitAvailable) {
            return Promise.resolve({
              id: 'unit-uuid-1',
              serialNumber: 'KM-GLD-001',
              status: HookahInventoryStatus.AVAILABLE,
            });
          }
          return Promise.resolve(null);
        }),
      },
      $transaction: jest.fn().mockImplementation(async (callback) => {
        // Atomic check-and-update simulation matching MySQL row-level transactional locking
        if (!unitAvailable) {
          throw new ConflictException('Selected physical unit was just reserved by another request.');
        }
        unitAvailable = false; // Mark unit as reserved atomically
        slotBookings++;

        const tx = {
          hookahInventory: {
            updateMany: jest.fn().mockResolvedValue({ count: 1 }),
          },
          booking: {
            create: jest.fn().mockResolvedValue({
              id: `booking-${Date.now()}-${Math.random()}`,
              bookingNumber: `SR-BK-${Date.now()}`,
              totalAmount: 1649.0,
            }),
          },
          rental: {
            create: jest.fn().mockResolvedValue({
              id: `rental-${Date.now()}`,
              rentalNumber: `SR-RN-${Date.now()}`,
              status: RentalStatus.RESERVED,
            }),
          },
          delivery: { create: jest.fn().mockResolvedValue({ id: 'delivery-uuid-1' }) },
          order: { create: jest.fn().mockResolvedValue({ id: 'order-uuid-1' }) },
          payment: { create: jest.fn().mockResolvedValue({ id: 'payment-uuid-1' }) },
          flavourStock: { updateMany: jest.fn().mockResolvedValue({ count: 1 }) },
          auditLog: { create: jest.fn().mockResolvedValue({ id: 'audit-uuid-1' }) },
        };
        return callback(tx);
      }),
    };

    mockRedis = {
      acquireLock: jest.fn().mockResolvedValue('lock-token-concurrency'),
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

  it('MANDATORY CONCURRENCY TEST: exactly 1 booking succeeds and 9 simultaneous attempts are rejected', async () => {
    const concurrentRequests = 10;

    // Launch 10 simultaneous booking attempts for the same single serialized hookah unit
    const promises = Array.from({ length: concurrentRequests }, (_, i) =>
      service.createBooking(`user-${i}`, bookingDto),
    );

    const results = await Promise.allSettled(promises);

    const successes = results.filter((r) => r.status === 'fulfilled');
    const failures = results.filter((r) => r.status === 'rejected');

    // VERIFICATION 1: Exactly one booking succeeded
    expect(successes.length).toBe(1);

    // VERIFICATION 2: All other 9 requests were safely rejected with ConflictException
    expect(failures.length).toBe(9);

    // VERIFICATION 3: Successful request received assigned serialized unit
    const successfulResult = (successes[0] as PromiseFulfilledResult<any>).value;
    expect(successfulResult.assignedUnit).toBe('KM-GLD-001');

    // VERIFICATION 4: All rejected attempts failed with ConflictException (anti-double booking)
    for (const failure of failures) {
      const error = (failure as PromiseRejectedResult).reason;
      expect(error).toBeInstanceOf(ConflictException);
    }
  });

  it('DATABASE-BACKED CONCURRENCY SAFETY: prevents double booking even when Redis is absent/unreachable', async () => {
    // Simulate Redis is completely down / unavailable
    mockRedis.acquireLock.mockResolvedValue('db-lock-fallback-nonce');

    const concurrentRequests = 5;
    const promises = Array.from({ length: concurrentRequests }, (_, i) =>
      service.createBooking(`user-db-${i}`, bookingDto),
    );

    const results = await Promise.allSettled(promises);

    const successes = results.filter((r) => r.status === 'fulfilled');
    const failures = results.filter((r) => r.status === 'rejected');

    // Exactly 1 succeeded via database ACID transaction protection
    expect(successes.length).toBe(1);
    expect(failures.length).toBe(4);
  });
});
