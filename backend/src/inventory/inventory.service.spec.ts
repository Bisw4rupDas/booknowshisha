import { Test, TestingModule } from '@nestjs/testing';
import { InventoryService } from './inventory.service';
import { PrismaService } from '../prisma/prisma.service';
import { NotFoundException, ConflictException } from '@nestjs/common';
import { HookahInventoryStatus, HookahCondition } from '@prisma/client';

describe('InventoryService', () => {
  let service: InventoryService;
  let prisma: PrismaService;

  const mockModel = {
    id: 'model-uuid-1',
    name: 'Khalil Mamoon Gold Classic',
    slug: 'km-gold-classic',
    basePrice: 999,
  };

  const mockUnit = {
    id: 'unit-uuid-1',
    hookahModelId: 'model-uuid-1',
    serialNumber: 'KM-GLD-001',
    barcode: 'BAR-KM-GLD-001',
    condition: HookahCondition.EXCELLENT,
    status: HookahInventoryStatus.AVAILABLE,
    notes: 'Brand new unit',
    hookahModel: mockModel,
  };

  const mockPrismaService = {
    hookahModel: {
      findUnique: jest.fn(),
    },
    hookahInventory: {
      findUnique: jest.fn(),
      findMany: jest.fn(),
      count: jest.fn(),
      create: jest.fn(),
      update: jest.fn(),
    },
    auditLog: {
      create: jest.fn(),
    },
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        InventoryService,
        { provide: PrismaService, useValue: mockPrismaService },
      ],
    }).compile();

    service = module.get<InventoryService>(InventoryService);
    prisma = module.get<PrismaService>(PrismaService);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  describe('createUnit', () => {
    it('should create an inventory unit successfully', async () => {
      mockPrismaService.hookahModel.findUnique.mockResolvedValue(mockModel);
      mockPrismaService.hookahInventory.findUnique.mockResolvedValue(null);
      mockPrismaService.hookahInventory.create.mockResolvedValue(mockUnit);

      const result = await service.createUnit({
        hookahModelId: 'model-uuid-1',
        serialNumber: 'KM-GLD-001',
      });

      expect(result).toBeDefined();
      expect(result.serialNumber).toBe('KM-GLD-001');
    });

    it('should throw ConflictException if serial number exists', async () => {
      mockPrismaService.hookahModel.findUnique.mockResolvedValue(mockModel);
      mockPrismaService.hookahInventory.findUnique.mockResolvedValue(mockUnit);

      await expect(
        service.createUnit({
          hookahModelId: 'model-uuid-1',
          serialNumber: 'KM-GLD-001',
        }),
      ).rejects.toThrow(ConflictException);
    });
  });

  describe('updateStatus', () => {
    it('should update inventory unit status', async () => {
      mockPrismaService.hookahInventory.findUnique.mockResolvedValue(mockUnit);
      mockPrismaService.hookahInventory.update.mockResolvedValue({
        ...mockUnit,
        status: HookahInventoryStatus.RENTED,
      });

      const result = await service.updateStatus('unit-uuid-1', {
        status: HookahInventoryStatus.RENTED,
        notes: 'Dispatched to customer',
      });

      expect(result.status).toBe(HookahInventoryStatus.RENTED);
    });
  });
});
