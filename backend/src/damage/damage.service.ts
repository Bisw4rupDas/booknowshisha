import {
  Injectable,
  NotFoundException,
  BadRequestException,
  Logger,
} from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateDamageReportDto } from './dto/create-damage-report.dto';
import { HookahCondition, HookahInventoryStatus, RentalStatus } from '@prisma/client';

@Injectable()
export class DamageService {
  private readonly logger = new Logger(DamageService.name);

  constructor(private readonly prisma: PrismaService) {}

  /**
   * Assess damage, create report, and reconcile deposit deductions
   */
  async createDamageReport(dto: CreateDamageReportDto, user?: any) {
    const rental = await this.prisma.rental.findUnique({
      where: { id: dto.rentalId },
      include: {
        securityDeposit: true,
        items: true,
      },
    });

    if (!rental) {
      throw new NotFoundException(`Rental with ID ${dto.rentalId} not found.`);
    }

    const damageReport = await this.prisma.$transaction(async (tx) => {
      // 1. Create Damage Report
      const report = await tx.damageReport.create({
        data: {
          rentalId: dto.rentalId,
          inspectionId: dto.inspectionId,
          description: dto.description,
          damageCost: dto.damageCost,
          photos: {
            create: (dto.photos || []).map((url) => ({ url })),
          },
        },
        include: {
          photos: true,
        },
      });

      // 2. Deposit Deduction Calculation
      if (dto.autoDeductFromDeposit && rental.securityDeposit) {
        const deposit = rental.securityDeposit;
        const currentDeduction = Number(deposit.deductionAmount || 0);
        const newTotalDeduction = currentDeduction + dto.damageCost;
        const originalDeposit = Number(deposit.amount);
        const refundRemaining = Math.max(0, originalDeposit - newTotalDeduction);

        await tx.securityDeposit.update({
          where: { id: deposit.id },
          data: {
            deductionAmount: newTotalDeduction,
            refundAmount: refundRemaining,
            notes: `Damage deduction: ₹${dto.damageCost} (${dto.description}). Remaining refund: ₹${refundRemaining}`,
          },
        });
      }

      // 3. Update assigned Hookah Inventory Unit conditions to MAINTENANCE
      const inventoryIds = rental.items
        .map((i) => i.hookahInventoryId)
        .filter((i): i is string => Boolean(i));

      if (inventoryIds.length > 0) {
        await tx.hookahInventory.updateMany({
          where: { id: { in: inventoryIds } },
          data: {
            condition: HookahCondition.MAINTENANCE,
            status: HookahInventoryStatus.IN_MAINTENANCE,
            notes: `[${new Date().toISOString()}] Flagged for damage repair: ${dto.description}`,
          },
        });
      }

      // 4. Update Rental status to DISPUTED or INSPECTED
      await tx.rental.update({
        where: { id: rental.id },
        data: {
          status: RentalStatus.DISPUTED,
        },
      });

      // 5. Audit Log
      await tx.auditLog.create({
        data: {
          userId: user?.id || null,
          action: 'DAMAGE_REPORT_FILED',
          entity: 'DamageReport',
          entityId: report.id,
          details: {
            rentalId: rental.id,
            damageCost: dto.damageCost,
            description: dto.description,
          },
        },
      });

      return report;
    });

    this.logger.warn(
      `Damage report filed for rental ${rental.rentalNumber} (Cost: ₹${dto.damageCost})`,
    );

    return damageReport;
  }

  /**
   * Find all damage reports for a specific rental
   */
  async findByRental(rentalId: string) {
    const reports = await this.prisma.damageReport.findMany({
      where: { rentalId },
      include: {
        photos: true,
        inspection: {
          include: { staff: true },
        },
      },
      orderBy: { createdAt: 'desc' },
    });

    return reports;
  }

  /**
   * List all damage reports system-wide
   */
  async findAll(page = 1, limit = 20) {
    const skip = (page - 1) * limit;

    const [total, items] = await Promise.all([
      this.prisma.damageReport.count(),
      this.prisma.damageReport.findMany({
        skip,
        take: limit,
        orderBy: { createdAt: 'desc' },
        include: {
          photos: true,
          rental: {
            select: {
              id: true,
              rentalNumber: true,
              customer: {
                select: {
                  id: true,
                  firstName: true,
                  lastName: true,
                  phone: true,
                },
              },
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
   * Find single damage report
   */
  async findOne(id: string) {
    const report = await this.prisma.damageReport.findUnique({
      where: { id },
      include: {
        photos: true,
        rental: {
          include: {
            customer: true,
            package: true,
            securityDeposit: true,
          },
        },
        inspection: {
          include: { staff: true },
        },
      },
    });

    if (!report) {
      throw new NotFoundException(`Damage report with ID ${id} not found.`);
    }

    return report;
  }
}
