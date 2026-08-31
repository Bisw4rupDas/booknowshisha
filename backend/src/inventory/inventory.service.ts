import {
  Injectable,
  NotFoundException,
  ConflictException,
  Logger,
} from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateInventoryUnitDto } from './dto/create-inventory-unit.dto';
import { UpdateInventoryStatusDto } from './dto/update-inventory-status.dto';
import { UpdateInventoryConditionDto } from './dto/update-inventory-condition.dto';
import { InventoryFilterDto } from './dto/inventory-filter.dto';
import { Prisma, HookahInventoryStatus, HookahCondition } from '@prisma/client';

@Injectable()
export class InventoryService {
  private readonly logger = new Logger(InventoryService.name);

  constructor(private readonly prisma: PrismaService) {}

  /**
   * Register a new physical serialized hookah unit
   */
  async createUnit(dto: CreateInventoryUnitDto, user?: any) {
    const model = await this.prisma.hookahModel.findUnique({
      where: { id: dto.hookahModelId },
    });

    if (!model) {
      throw new NotFoundException(`Hookah model with ID ${dto.hookahModelId} not found.`);
    }

    // Check unique serial number
    const existingSerial = await this.prisma.hookahInventory.findUnique({
      where: { serialNumber: dto.serialNumber },
    });

    if (existingSerial) {
      throw new ConflictException(
        `Hookah unit with serial number '${dto.serialNumber}' already exists.`,
      );
    }

    if (dto.barcode) {
      const existingBarcode = await this.prisma.hookahInventory.findUnique({
        where: { barcode: dto.barcode },
      });
      if (existingBarcode) {
        throw new ConflictException(
          `Hookah unit with barcode '${dto.barcode}' already exists.`,
        );
      }
    }

    const unit = await this.prisma.hookahInventory.create({
      data: {
        hookahModelId: dto.hookahModelId,
        serialNumber: dto.serialNumber,
        barcode: dto.barcode || `BAR-${dto.serialNumber}`,
        condition: dto.condition || HookahCondition.EXCELLENT,
        status: dto.status || HookahInventoryStatus.AVAILABLE,
        notes: dto.notes,
      },
      include: {
        hookahModel: true,
      },
    });

    // Log action
    await this.prisma.auditLog.create({
      data: {
        userId: user?.id || null,
        action: 'INVENTORY_UNIT_CREATED',
        entity: 'HookahInventory',
        entityId: unit.id,
        details: {
          serialNumber: unit.serialNumber,
          model: model.name,
        },
      },
    });

    this.logger.log(`Created inventory unit ${unit.serialNumber} (Model: ${model.name})`);
    return unit;
  }

  /**
   * List inventory units with filters and pagination
   */
  async findAll(filter: InventoryFilterDto) {
    const { hookahModelId, status, condition, search, page = 1, limit = 20 } = filter;
    const skip = (page - 1) * limit;

    const where: Prisma.HookahInventoryWhereInput = {};

    if (hookahModelId) {
      where.hookahModelId = hookahModelId;
    }
    if (status) {
      where.status = status;
    }
    if (condition) {
      where.condition = condition;
    }
    if (search) {
      where.OR = [
        { serialNumber: { contains: search } },
        { barcode: { contains: search } },
        { hookahModel: { name: { contains: search } } },
      ];
    }

    const [total, items] = await Promise.all([
      this.prisma.hookahInventory.count({ where }),
      this.prisma.hookahInventory.findMany({
        where,
        skip,
        take: limit,
        orderBy: { serialNumber: 'asc' },
        include: {
          hookahModel: {
            select: {
              id: true,
              name: true,
              slug: true,
              basePrice: true,
              depositFee: true,
              imageUrl: true,
            },
          },
        },
      }),
    ]);

    return {
      items,
      meta: {
        total,
        page,
        limit,
        totalPages: Math.ceil(total / limit),
      },
    };
  }

  /**
   * Find single unit by UUID
   */
  async findOne(id: string) {
    const unit = await this.prisma.hookahInventory.findUnique({
      where: { id },
      include: {
        hookahModel: true,
        rentalItems: {
          take: 5,
          orderBy: { rental: { createdAt: 'desc' } },
          include: {
            rental: {
              select: {
                id: true,
                rentalNumber: true,
                status: true,
                startDate: true,
                endDate: true,
              },
            },
          },
        },
      },
    });

    if (!unit) {
      throw new NotFoundException(`Hookah inventory unit with ID ${id} not found.`);
    }

    return unit;
  }

  /**
   * Find single unit by Barcode
   */
  async findByBarcode(barcode: string) {
    const unit = await this.prisma.hookahInventory.findUnique({
      where: { barcode },
      include: {
        hookahModel: true,
      },
    });

    if (!unit) {
      throw new NotFoundException(`Hookah unit with barcode '${barcode}' not found.`);
    }

    return unit;
  }

  /**
   * Find single unit by Serial Number
   */
  async findBySerialNumber(serialNumber: string) {
    const unit = await this.prisma.hookahInventory.findUnique({
      where: { serialNumber },
      include: {
        hookahModel: true,
      },
    });

    if (!unit) {
      throw new NotFoundException(`Hookah unit with serial '${serialNumber}' not found.`);
    }

    return unit;
  }

  /**
   * Update status of physical unit
   */
  async updateStatus(id: string, dto: UpdateInventoryStatusDto, user?: any) {
    const unit = await this.findOne(id);

    const updated = await this.prisma.hookahInventory.update({
      where: { id },
      data: {
        status: dto.status,
        notes: dto.notes ? `${unit.notes || ''}\n[${new Date().toISOString()}] ${dto.notes}`.trim() : unit.notes,
      },
      include: {
        hookahModel: true,
      },
    });

    await this.prisma.auditLog.create({
      data: {
        userId: user?.id || null,
        action: 'INVENTORY_STATUS_UPDATED',
        entity: 'HookahInventory',
        entityId: id,
        details: {
          from: unit.status,
          to: dto.status,
          notes: dto.notes,
        },
      },
    });

    return updated;
  }

  /**
   * Update condition assessment of physical unit
   */
  async updateCondition(id: string, dto: UpdateInventoryConditionDto, user?: any) {
    const unit = await this.findOne(id);

    const updated = await this.prisma.hookahInventory.update({
      where: { id },
      data: {
        condition: dto.condition,
        notes: dto.notes ? `${unit.notes || ''}\n[${new Date().toISOString()}] Condition: ${dto.condition}. ${dto.notes}`.trim() : unit.notes,
      },
      include: {
        hookahModel: true,
      },
    });

    await this.prisma.auditLog.create({
      data: {
        userId: user?.id || null,
        action: 'INVENTORY_CONDITION_UPDATED',
        entity: 'HookahInventory',
        entityId: id,
        details: {
          from: unit.condition,
          to: dto.condition,
          notes: dto.notes,
        },
      },
    });

    return updated;
  }

  /**
   * Aggregate fleet metrics for dashboard
   */
  async getMetrics() {
    const [total, available, reserved, rented, inMaintenance, decommissioned] = await Promise.all([
      this.prisma.hookahInventory.count(),
      this.prisma.hookahInventory.count({ where: { status: HookahInventoryStatus.AVAILABLE } }),
      this.prisma.hookahInventory.count({ where: { status: HookahInventoryStatus.RESERVED } }),
      this.prisma.hookahInventory.count({ where: { status: HookahInventoryStatus.RENTED } }),
      this.prisma.hookahInventory.count({ where: { status: HookahInventoryStatus.IN_MAINTENANCE } }),
      this.prisma.hookahInventory.count({ where: { status: HookahInventoryStatus.DECOMMISSIONED } }),
    ]);

    return {
      totalUnits: total,
      available,
      reserved,
      rented,
      inMaintenance,
      decommissioned,
      utilizationRate: total > 0 ? Number(((rented / total) * 100).toFixed(1)) : 0,
    };
  }
}
