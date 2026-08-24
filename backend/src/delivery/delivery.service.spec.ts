/* eslint-disable @typescript-eslint/no-explicit-any */
import { Test, TestingModule } from '@nestjs/testing';
import { DeliveryService } from './delivery.service';
import { PrismaService } from '../prisma/prisma.service';
import { PinServiceabilityService } from './serviceability/pin-serviceability.service';
import { BadRequestException } from '@nestjs/common';

describe('DeliveryService', () => {
  let service: DeliveryService;
  let mockPrisma: any;

  const mockZones = [
    {
      id: 'zone-1',
      name: 'Salt Lake & New Town Hub',
      postalCodes: ['700064', '700091', '700156', '700160'],
      baseFee: 150.0,
      isActive: true,
      slots: [
        { id: 'slot-1', startTime: '14:00', endTime: '16:00', isActive: true },
        { id: 'slot-2', startTime: '18:00', endTime: '20:00', isActive: true },
      ],
    },
    {
      id: 'zone-2',
      name: 'Central & South Kolkata Hub',
      postalCodes: ['700001', '700016', '700019', '700027'],
      baseFee: 150.0,
      isActive: true,
      slots: [
        { id: 'slot-3', startTime: '14:00', endTime: '16:00', isActive: true },
        { id: 'slot-4', startTime: '18:00', endTime: '20:00', isActive: true },
      ],
    },
  ];

  beforeEach(async () => {
    mockPrisma = {
      deliveryZone: {
        findMany: jest.fn().mockResolvedValue(mockZones),
        findFirst: jest.fn().mockImplementation((query: any) => {
          if (query?.where?.postalCodes?.has) {
            const pin = query.where.postalCodes.has;
            return Promise.resolve(mockZones.find((z) => z.postalCodes.includes(pin)) || null);
          }
          return Promise.resolve(mockZones[0]);
        }),
      },
      deliverySlot: {
        findMany: jest.fn().mockResolvedValue(mockZones.flatMap((z) => z.slots)),
      },
    };

    const module: TestingModule = await Test.createTestingModule({
      providers: [
        DeliveryService,
        PinServiceabilityService,
        { provide: PrismaService, useValue: mockPrisma },
      ],
    }).compile();

    service = module.get<DeliveryService>(DeliveryService);
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  describe('checkZone (3-District Validation)', () => {
    it('should return serviceable: true for Kolkata district PIN 700019', async () => {
      const result = await service.checkZone({ postalCode: '700019' });
      expect(result.serviceable).toBe(true);
      expect(result.deliverable).toBe(true);
      expect(result.district).toBe('Kolkata');
      expect(result.state).toBe('West Bengal');
      expect(result.availableSlots).toBeDefined();
      expect(result.message).toContain('Delivery available in Kolkata');
    });

    it('should return serviceable: true for North 24 Parganas district PIN 700091', async () => {
      const result = await service.checkZone({ postalCode: '700091' });
      expect(result.serviceable).toBe(true);
      expect(result.deliverable).toBe(true);
      expect(result.district).toBe('North 24 Parganas');
      expect(result.state).toBe('West Bengal');
      expect(result.message).toContain('Delivery available in North 24 Parganas');
    });

    it('should return serviceable: true for South 24 Parganas district PIN 700027', async () => {
      const result = await service.checkZone({ postalCode: '700027' });
      expect(result.serviceable).toBe(true);
      expect(result.deliverable).toBe(true);
      expect(result.district).toBe('South 24 Parganas');
      expect(result.state).toBe('West Bengal');
      expect(result.message).toContain('Delivery available in South 24 Parganas');
    });

    it('should return serviceable: false for Howrah PIN 711101', async () => {
      const result = await service.checkZone({ postalCode: '711101' });
      expect(result.serviceable).toBe(false);
      expect(result.deliverable).toBe(false);
      expect(result.district).toBe('Howrah');
      expect(result.message).toContain('Delivery not available in Howrah');
    });

    it('should return serviceable: false for Delhi PIN 110001', async () => {
      const result = await service.checkZone({ postalCode: '110001' });
      expect(result.serviceable).toBe(false);
      expect(result.deliverable).toBe(false);
      expect(result.district).toBe('New Delhi');
      expect(result.message).toContain('Delivery not available');
    });

    it('should return serviceable: false for invalid PIN 999999', async () => {
      const result = await service.checkZone({ postalCode: '999999' });
      expect(result.serviceable).toBe(false);
      expect(result.deliverable).toBe(false);
      expect(result.district).toBeNull();
    });
  });

  describe('getSlots (Serviceability Protected)', () => {
    it('should return slots for serviceable PIN 700091', async () => {
      const result: any = await service.getSlots('700091');
      expect(result).toBeDefined();
      expect(result.district).toBe('North 24 Parganas');
      expect(result.slots.length).toBeGreaterThan(0);
    });

    it('should throw BadRequestException when requesting slots for unserviceable PIN 711101 (Howrah)', async () => {
      await expect(service.getSlots('711101')).rejects.toThrow(BadRequestException);
    });
  });
});
