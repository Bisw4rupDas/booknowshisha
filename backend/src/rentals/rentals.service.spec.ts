import { Test, TestingModule } from '@nestjs/testing';
import { RentalsService } from './rentals.service';
import { PrismaService } from '../prisma/prisma.service';
import { PinServiceabilityService } from '../delivery/serviceability/pin-serviceability.service';
import { NotFoundException, BadRequestException } from '@nestjs/common';
import { RentalStatus } from '@prisma/client';

describe('RentalsService', () => {
  let service: RentalsService;
  let prisma: PrismaService;

  const mockBooking = {
    id: 'booking-uuid-1',
    bookingNumber: 'BKG-2026-001',
    customerId: 'customer-uuid-1',
    packageId: 'package-uuid-1',
    rentalStart: new Date(),
    rentalEnd: new Date(Date.now() + 86400000),
    depositAmount: 1500,
    rental: null,
    package: {
      id: 'package-uuid-1',
      name: 'Solo Standard',
      items: [],
    },
    customer: {
      id: 'customer-uuid-1',
      firstName: 'Rahul',
      lastName: 'Sharma',
    },
  };

  const mockRental = {
    id: 'rental-uuid-1',
    rentalNumber: 'RNT-20260822-ABCD',
    bookingId: 'booking-uuid-1',
    customerId: 'customer-uuid-1',
    packageId: 'package-uuid-1',
    status: RentalStatus.RESERVED,
    startDate: new Date(),
    endDate: new Date(Date.now() + 86400000),
    items: [],
  };

  const mockPrismaService: any = {
    booking: {
      findUnique: jest.fn(),
    },
    rental: {
      findUnique: jest.fn(),
      findMany: jest.fn(),
      count: jest.fn(),
      create: jest.fn(),
      update: jest.fn(),
    },
    rentalItem: {
      create: jest.fn(),
    },
    hookahInventory: {
      findUnique: jest.fn(),
      update: jest.fn(),
      updateMany: jest.fn(),
    },
    securityDeposit: {
      create: jest.fn(),
    },
    auditLog: {
      create: jest.fn(),
    },
    customer: {
      findUnique: jest.fn(),
    },
    $transaction: jest.fn((callback: any) => callback(mockPrismaService)),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        RentalsService,
        PinServiceabilityService,
        { provide: PrismaService, useValue: mockPrismaService },
      ],
    }).compile();

    service = module.get<RentalsService>(RentalsService);
    prisma = module.get<PrismaService>(PrismaService);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  describe('createRental', () => {
    it('should create a rental successfully from a booking', async () => {
      mockPrismaService.booking.findUnique.mockResolvedValue(mockBooking);
      mockPrismaService.rental.create.mockResolvedValue(mockRental);
      mockPrismaService.rental.findUnique.mockResolvedValue(mockRental);

      const result = await service.createRental({
        bookingId: 'booking-uuid-1',
      });

      expect(result).toBeDefined();
      expect(mockPrismaService.rental.create).toHaveBeenCalled();
    });

    it('should throw NotFoundException if booking does not exist', async () => {
      mockPrismaService.booking.findUnique.mockResolvedValue(null);

      await expect(
        service.createRental({ bookingId: 'non-existent' }),
      ).rejects.toThrow(NotFoundException);
    });

    it('should throw BadRequestException if rental already exists for booking', async () => {
      mockPrismaService.booking.findUnique.mockResolvedValue({
        ...mockBooking,
        rental: mockRental,
      });

      await expect(
        service.createRental({ bookingId: 'booking-uuid-1' }),
      ).rejects.toThrow(BadRequestException);
    });
  });

  describe('updateStatus', () => {
    it('should update rental status through valid transition (RESERVED -> PREPARING)', async () => {
      mockPrismaService.rental.findUnique.mockResolvedValue(mockRental);
      mockPrismaService.rental.update.mockResolvedValue({
        ...mockRental,
        status: RentalStatus.PREPARING,
      });

      const result = await service.updateStatus('rental-uuid-1', {
        status: RentalStatus.PREPARING,
      });

      expect(result.status).toBe(RentalStatus.PREPARING);
    });

    it('should reject invalid transition (RESERVED -> COMPLETED)', async () => {
      mockPrismaService.rental.findUnique.mockResolvedValue(mockRental);

      await expect(
        service.updateStatus('rental-uuid-1', {
          status: RentalStatus.COMPLETED,
        }),
      ).rejects.toThrow(BadRequestException);
    });
  });
});
