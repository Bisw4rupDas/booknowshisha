import { Test, TestingModule } from '@nestjs/testing';
import { CustomersService } from './customers.service';
import { PrismaService } from '../prisma/prisma.service';
import { NotFoundException } from '@nestjs/common';

describe('CustomersService', () => {
  let service: CustomersService;
  let prisma: PrismaService;

  const mockCustomer = {
    id: 'cust-uuid-1',
    userId: 'user-uuid-1',
    firstName: 'Rahul',
    lastName: 'Sharma',
    phone: '+919903556825',
    city: 'Kolkata',
    postalCode: '700091',
  };

  const mockPrismaService = {
    customer: {
      findUnique: jest.fn(),
      findMany: jest.fn(),
      count: jest.fn(),
      update: jest.fn(),
    },
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        CustomersService,
        { provide: PrismaService, useValue: mockPrismaService },
      ],
    }).compile();

    service = module.get<CustomersService>(CustomersService);
    prisma = module.get<PrismaService>(PrismaService);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  describe('getProfile', () => {
    it('should return customer profile', async () => {
      mockPrismaService.customer.findUnique.mockResolvedValue(mockCustomer);

      const result = await service.getProfile('user-uuid-1');
      expect(result).toBeDefined();
      expect(result.firstName).toBe('Rahul');
    });

    it('should throw NotFoundException if profile does not exist', async () => {
      mockPrismaService.customer.findUnique.mockResolvedValue(null);

      await expect(service.getProfile('non-existent')).rejects.toThrow(
        NotFoundException,
      );
    });
  });
});
