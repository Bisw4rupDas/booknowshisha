import { Injectable, NotFoundException, BadRequestException, Logger } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { ReturnInspectionDto } from './dto/return-inspection.dto';
import { DamageReportDto } from './dto/damage-report.dto';
import { RentalStatus, HookahInventoryStatus, InspectionStatus } from '@prisma/client';

@Injectable()
export class ReturnsService {
  private readonly logger = new Logger(ReturnsService.name);

  constructor(private readonly prisma: PrismaService) {}

  async processReturn(staffUserId: string, rentalId: string, dto: ReturnInspectionDto) {
    const user = await this.prisma.user.findUnique({
      where: { id: staffUserId },
      include: { staff: true },
    });

    if (!user || !user.staff) {
      throw new BadRequestException('Staff profile is required to process return inspections');
    }

    const rental = await this.prisma.rental.findUnique({
      where: { id: rentalId },
      include: {
        securityDeposit: true,
        items: {
          include: { hookahInventory: true },
        },
      },
    });

    if (!rental) {
      throw new NotFoundException(`Rental #${rentalId} not found`);
    }

    const result = await this.prisma.$transaction(async (tx) => {
      // 1. Create or update inspection
      const inspection = await tx.returnInspection.upsert({
        where: { rentalId: rental.id },
        update: {
          status: dto.status,
          isClean: dto.isClean,
          allPartsPresent: dto.allPartsPresent,
          notes: dto.notes,
        },
        create: {
          rentalId: rental.id,
          inspectedBy: user.staff!.id,
          status: dto.status,
          isClean: dto.isClean,
          allPartsPresent: dto.allPartsPresent,
          notes: dto.notes,
        },
      });

      // 2. Release inventory unit or set to maintenance
      for (const item of rental.items) {
        if (item.hookahInventoryId) {
          await tx.hookahInventory.update({
            where: { id: item.hookahInventoryId },
            data: {
              status:
                dto.status === InspectionStatus.PASSED
                  ? HookahInventoryStatus.AVAILABLE
                  : HookahInventoryStatus.IN_MAINTENANCE,
            },
          });
        }
      }

      // 3. Update Rental Status
      const updatedRental = await tx.rental.update({
        where: { id: rental.id },
        data: {
          status:
            dto.status === InspectionStatus.PASSED
              ? RentalStatus.COMPLETED
              : RentalStatus.INSPECTED,
          actualReturn: new Date(),
        },
      });

      // 4. Handle Security Deposit Refund if Passed
      if (dto.status === InspectionStatus.PASSED && rental.securityDeposit) {
        await tx.securityDeposit.update({
          where: { id: rental.securityDeposit.id },
          data: {
            isRefunded: true,
            refundAmount: rental.securityDeposit.amount,
            refundedAt: new Date(),
            notes: 'Full security deposit released after passed quality inspection',
          },
        });
      }

      // 5. Audit Log
      await tx.auditLog.create({
        data: {
          userId: staffUserId,
          action: 'RENTAL_RETURN_INSPECTED',
          entity: 'Rental',
          entityId: rental.id,
          details: {
            inspectionStatus: dto.status,
            inspector: user.staff!.fullName,
          },
        },
      });

      return { inspection, rental: updatedRental };
    });

    this.logger.log(
      `Rental #${rental.rentalNumber} returned and inspected (${dto.status}) by ${user.staff.fullName}`,
    );
    return {
      success: true,
      message: `Return processed. Rental status is now ${result.rental.status}`,
      data: result,
    };
  }

  async reportDamage(staffUserId: string, rentalId: string, dto: DamageReportDto) {
    const user = await this.prisma.user.findUnique({
      where: { id: staffUserId },
      include: { staff: true },
    });

    if (!user || !user.staff) {
      throw new BadRequestException('Staff profile required');
    }

    const rental = await this.prisma.rental.findUnique({
      where: { id: rentalId },
      include: { securityDeposit: true, inspection: true },
    });

    if (!rental) {
      throw new NotFoundException(`Rental #${rentalId} not found`);
    }

    const depositAmount = rental.securityDeposit ? Number(rental.securityDeposit.amount) : 0;
    const damageCost = dto.damageCost;
    const deductionAmount = Math.min(depositAmount, damageCost);
    const refundAmount = Math.max(0, depositAmount - damageCost);

    const result = await this.prisma.$transaction(async (tx) => {
      // 1. Create damage report
      const damageReport = await tx.damageReport.create({
        data: {
          rentalId: rental.id,
          inspectionId: rental.inspection?.id,
          description: dto.description,
          damageCost: dto.damageCost,
          photos: dto.photos || [],
        },
      });

      // 2. Adjust security deposit
      if (rental.securityDeposit) {
        await tx.securityDeposit.update({
          where: { id: rental.securityDeposit.id },
          data: {
            isRefunded: true,
            deductionAmount,
            refundAmount,
            refundedAt: new Date(),
            notes: `Deducted ₹${deductionAmount} for damage: ${dto.description}${damageCost > depositAmount ? ` (Excess damage: ₹${damageCost - depositAmount})` : ''}`,
          },
        });
      }

      // 3. Mark Rental as DISPUTED
      await tx.rental.update({
        where: { id: rental.id },
        data: { status: RentalStatus.DISPUTED },
      });

      return damageReport;
    });

    return {
      success: true,
      message: `Damage report logged. ₹${deductionAmount} deducted from deposit. Net refund: ₹${refundAmount}`,
      damageReport: result,
    };
  }

  async getInspection(rentalId: string) {
    const inspection = await this.prisma.returnInspection.findUnique({
      where: { rentalId },
      include: {
        staff: true,
        damageReport: true,
        rental: {
          include: { securityDeposit: true },
        },
      },
    });

    if (!inspection) {
      throw new NotFoundException(`No inspection record found for rental #${rentalId}`);
    }

    return inspection;
  }

  async findAllRentals() {
    return this.prisma.rental.findMany({
      include: {
        customer: true,
        package: true,
        items: {
          include: {
            hookahInventory: { include: { hookahModel: true } },
            flavour: true,
          },
        },
        securityDeposit: true,
        inspection: { include: { damageReport: true } },
        deliveries: true,
      },
      orderBy: { createdAt: 'desc' },
    });
  }

  async findOneRental(rentalId: string) {
    const rental = await this.prisma.rental.findUnique({
      where: { id: rentalId },
      include: {
        customer: true,
        package: true,
        items: {
          include: {
            hookahInventory: { include: { hookahModel: true } },
            flavour: true,
          },
        },
        securityDeposit: true,
        inspection: { include: { damageReport: true } },
        deliveries: true,
      },
    });

    if (!rental) {
      throw new NotFoundException(`Rental #${rentalId} not found`);
    }

    return rental;
  }
}
