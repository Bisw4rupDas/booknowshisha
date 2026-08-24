import { Injectable, NotFoundException, BadRequestException, Logger } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CollectCodDto } from './dto/collect-cod.dto';
import { PaymentMethod, PaymentStatus, RentalStatus } from '@prisma/client';
import { PinServiceabilityService } from '../delivery/serviceability/pin-serviceability.service';

@Injectable()
export class AdminService {
  private readonly logger = new Logger(AdminService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly pinServiceability: PinServiceabilityService,
  ) {}

  async getPendingCodPayments() {
    const rawPayments = await this.prisma.payment.findMany({
      where: {
        method: PaymentMethod.COD,
        status: PaymentStatus.PENDING,
      },
      include: {
        order: {
          include: {
            customer: true,
            booking: {
              include: {
                package: true,
                deliverySlot: { include: { zone: true } },
              },
            },
          },
        },
      },
      orderBy: { createdAt: 'desc' },
    });

    return rawPayments.map((payment) => {
      const pin = payment.order?.customer?.postalCode || '';
      const serviceability = this.pinServiceability.checkPinServiceability(pin);
      return {
        ...payment,
        resolvedDistrict: serviceability.district || 'Unserviceable Area',
        isServiceable: serviceability.deliverable,
      };
    });
  }

  async collectCod(staffUserId: string, paymentId: string, dto: CollectCodDto) {
    const user = await this.prisma.user.findUnique({
      where: { id: staffUserId },
      include: { staff: true },
    });

    if (!user || !user.staff) {
      throw new BadRequestException('Action requires a registered Staff/Admin profile');
    }

    const payment = await this.prisma.payment.findUnique({
      where: { id: paymentId },
      include: {
        order: {
          include: {
            booking: {
              include: { rental: true },
            },
          },
        },
      },
    });

    if (!payment) {
      throw new NotFoundException(`Payment record #${paymentId} not found`);
    }

    if (payment.status === PaymentStatus.SUCCESS) {
      throw new BadRequestException('Cash collection for this payment is already completed');
    }

    const paymentAmount = Number(payment.amount);
    const isPartial = dto.isPartial || dto.amount < paymentAmount;
    const isFullyCollected = !isPartial && dto.amount >= paymentAmount;

    const result = await this.prisma.$transaction(async (tx) => {
      // 1. Update Payment status
      const updatedPayment = await tx.payment.update({
        where: { id: payment.id },
        data: {
          status: isFullyCollected ? PaymentStatus.SUCCESS : PaymentStatus.PROCESSING,
          paidAt: isFullyCollected ? new Date() : undefined,
        },
      });

      // 2. Create CashCollection record
      const cashCollection = await tx.cashCollection.create({
        data: {
          paymentId: payment.id,
          collectedBy: user.staff!.id,
          amount: dto.amount,
          reconciled: isFullyCollected,
          reconciledAt: isFullyCollected ? new Date() : null,
          notes:
            dto.notes ||
            (isFullyCollected
              ? `Full cash collected by ${user.staff!.fullName}`
              : `Partial cash collected (₹${dto.amount} of ₹${paymentAmount}) by ${user.staff!.fullName}`),
        },
      });

      // 3. Update Rental lifecycle to DELIVERED / ACTIVE if full collection
      if (isFullyCollected && payment.order.booking?.rental) {
        await tx.rental.update({
          where: { id: payment.order.booking.rental.id },
          data: { status: RentalStatus.ACTIVE },
        });
      }

      // 4. Audit Log
      await tx.auditLog.create({
        data: {
          userId: staffUserId,
          action: isFullyCollected ? 'COD_CASH_COLLECTED' : 'COD_PARTIAL_COLLECTION',
          entity: 'CashCollection',
          entityId: cashCollection.id,
          details: {
            amountCollected: dto.amount,
            totalPaymentAmount: paymentAmount,
            isFullyCollected,
            collectedBy: user.staff!.fullName,
            orderId: payment.orderId,
          },
        },
      });

      return { updatedPayment, cashCollection, isFullyCollected };
    });

    this.logger.log(
      `COD ${result.isFullyCollected ? 'fully' : 'partially'} collected for Payment #${payment.paymentNumber} by Staff ${user.staff.fullName}`,
    );

    return {
      success: true,
      message: result.isFullyCollected
        ? 'Cash collected and reconciled successfully. Rental status is now ACTIVE.'
        : `Partial cash of ₹${dto.amount} recorded. Balance remaining: ₹${Math.max(0, paymentAmount - dto.amount)}`,
      data: result,
    };
  }

  async getPlatformMetrics() {
    const [totalBookings, activeRentals, availableHookahs, totalFlavours, totalRevenue] =
      await Promise.all([
        this.prisma.booking.count(),
        this.prisma.rental.count({
          where: {
            status: { in: [RentalStatus.ACTIVE, RentalStatus.DELIVERED, RentalStatus.PREPARING] },
          },
        }),
        this.prisma.hookahInventory.count({
          where: { status: 'AVAILABLE' },
        }),
        this.prisma.flavour.count({
          where: { isActive: true },
        }),
        this.prisma.payment.aggregate({
          where: { status: PaymentStatus.SUCCESS },
          _sum: { amount: true },
        }),
      ]);

    return {
      totalBookings,
      activeRentals,
      availableHookahs,
      totalFlavours,
      totalRevenue: totalRevenue._sum.amount || 0,
      timestamp: new Date().toISOString(),
    };
  }
}
