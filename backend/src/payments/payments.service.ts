import { Injectable, NotFoundException, UnauthorizedException, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { PrismaService } from '../prisma/prisma.service';
import {
  InitiatePaymentDto,
  InitiateUpiDto,
  ConfirmPaymentDto,
  UpiWebhookDto,
} from './dto/initiate-upi.dto';
import { PaymentMethod, PaymentStatus, OrderStatus, RentalStatus, Prisma } from '@prisma/client';

@Injectable()
export class PaymentsService {
  private readonly logger = new Logger(PaymentsService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly configService: ConfigService,
  ) {}

  async initiateUpi(userId: string, dto: InitiateUpiDto) {
    return this.initiatePayment(userId, {
      bookingId: dto.bookingId,
      method: PaymentMethod.UPI,
      amount: dto.amount,
      notes: dto.notes,
    });
  }

  async initiatePayment(userId: string, dto: InitiatePaymentDto) {
    const booking = await this.prisma.booking.findUnique({
      where: { id: dto.bookingId },
      include: {
        customer: true,
        rental: true,
        package: true,
      },
    });

    if (!booking) {
      throw new NotFoundException(`Booking #${dto.bookingId} not found`);
    }

    const orderNumber = `SR-ORD-${Date.now().toString().slice(-4)}${Math.floor(1000 + Math.random() * 9000)}`;
    const paymentNumber = `SR-PAY-${Date.now().toString().slice(-4)}${Math.floor(1000 + Math.random() * 9000)}`;

    // Create Order linked to Booking
    const order = await this.prisma.order.create({
      data: {
        orderNumber,
        customerId: booking.customerId,
        bookingId: booking.id,
        status: dto.method === PaymentMethod.COD ? OrderStatus.CONFIRMED : OrderStatus.PENDING,
        subtotal: booking.totalAmount,
        deposit: booking.depositAmount,
        totalAmount: Number(booking.totalAmount),
        notes: dto.notes,
        items: {
          create: [
            {
              name: `Rental: ${booking.package.name}`,
              quantity: 1,
              unitPrice: booking.totalAmount,
              totalPrice: booking.totalAmount,
            },
          ],
        },
      },
    });

    // Create Payment record
    const payment = await this.prisma.payment.create({
      data: {
        paymentNumber,
        orderId: order.id,
        method: dto.method,
        status: PaymentStatus.PENDING,
        amount: dto.amount,
        currency: 'INR',
      },
    });

    let upiIntentString: string | undefined;
    let upiQrPayload: string | undefined;

    if (dto.method === PaymentMethod.UPI) {
      const upiVpa = 'pay.shisharent@upi';
      const txnRef = `TXN${Date.now()}`;
      upiIntentString = `upi://pay?pa=${upiVpa}&pn=ShishaRent&mc=5999&tid=${txnRef}&tr=${txnRef}&tn=ShishaRent+Rental+Order+${orderNumber}&am=${dto.amount.toFixed(2)}&cu=INR`;
      upiQrPayload = upiIntentString;
    }

    this.logger.log(
      `Payment initialized: #${paymentNumber} (${dto.method}) for Order: #${orderNumber}`,
    );

    return {
      orderId: order.id,
      orderNumber,
      paymentId: payment.id,
      paymentNumber,
      method: dto.method,
      amount: dto.amount,
      currency: 'INR',
      upiIntent: upiIntentString,
      upiQrPayload,
      instructions:
        dto.method === PaymentMethod.COD
          ? 'Cash on Delivery confirmed. Please keep exact cash ready upon arrival of courier.'
          : 'Scan the UPI QR code or tap the intent link in your UPI app (GPay, PhonePe, Paytm) to complete payment.',
    };
  }

  async processWebhook(dto: UpiWebhookDto, signature?: string) {
    const configuredSecret = this.configService.get<string>('UPI_WEBHOOK_SECRET');

    // If a webhook secret is configured and not a placeholder, verify signature/secret
    if (
      configuredSecret &&
      configuredSecret !== 'placeholder_upi_webhook_secret' &&
      signature &&
      signature !== configuredSecret
    ) {
      this.logger.warn('Webhook signature mismatch detected');
      throw new UnauthorizedException('Invalid webhook signature');
    }

    const payment = await this.prisma.payment.findFirst({
      where: {
        OR: [{ paymentNumber: dto.paymentNumber }, { gatewayTxnId: dto.gatewayTxnId }],
      },
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
      throw new NotFoundException(`Payment record for webhook not found`);
    }

    // Idempotent: if already processed as SUCCESS, return immediately
    if (payment.status === PaymentStatus.SUCCESS) {
      this.logger.log(
        `Webhook received for already completed payment #${payment.paymentNumber}. Skipping.`,
      );
      return {
        received: true,
        alreadyProcessed: true,
        paymentNumber: payment.paymentNumber,
        status: payment.status,
      };
    }

    const isSuccess = dto.status === 'SUCCESS';

    const updated = await this.prisma.$transaction(async (tx) => {
      const updatedPayment = await tx.payment.update({
        where: { id: payment.id },
        data: {
          status: isSuccess ? PaymentStatus.SUCCESS : PaymentStatus.FAILED,
          gatewayTxnId: dto.gatewayTxnId,
          gatewayRaw: dto.gatewayRaw ? (dto.gatewayRaw as Prisma.InputJsonValue) : undefined,
          paidAt: isSuccess ? new Date() : null,
        },
      });

      if (isSuccess) {
        await tx.order.update({
          where: { id: payment.orderId },
          data: { status: OrderStatus.CONFIRMED },
        });

        if (payment.order.booking?.rental) {
          await tx.rental.update({
            where: { id: payment.order.booking.rental.id },
            data: { status: RentalStatus.PREPARING },
          });
        }
      }

      await tx.auditLog.create({
        data: {
          action: isSuccess ? 'UPI_WEBHOOK_SUCCESS' : 'UPI_WEBHOOK_FAILED',
          entity: 'Payment',
          entityId: payment.id,
          details: {
            amount: dto.amount,
            gatewayTxnId: dto.gatewayTxnId,
            status: dto.status,
          },
        },
      });

      return updatedPayment;
    });

    this.logger.log(
      `Webhook processed for Payment #${payment.paymentNumber}: Status -> ${updated.status}`,
    );

    return {
      received: true,
      success: isSuccess,
      paymentNumber: updated.paymentNumber,
      status: updated.status,
    };
  }

  async confirmPayment(paymentId: string, dto: ConfirmPaymentDto) {
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
      throw new NotFoundException('Payment record not found');
    }

    if (payment.status === PaymentStatus.SUCCESS) {
      return { success: true, message: 'Payment already marked as successful', payment };
    }

    const updated = await this.prisma.$transaction(async (tx) => {
      // 1. Update Payment status
      const updatedPayment = await tx.payment.update({
        where: { id: payment.id },
        data: {
          status: PaymentStatus.SUCCESS,
          gatewayTxnId: dto.gatewayTxnId,
          paidAt: new Date(),
        },
      });

      // 2. Update Order status
      await tx.order.update({
        where: { id: payment.orderId },
        data: { status: OrderStatus.CONFIRMED },
      });

      // 3. Update Rental lifecycle to PREPARING
      if (payment.order.booking?.rental) {
        await tx.rental.update({
          where: { id: payment.order.booking.rental.id },
          data: { status: RentalStatus.PREPARING },
        });
      }

      // 4. Audit Log
      await tx.auditLog.create({
        data: {
          action: 'PAYMENT_SUCCESS',
          entity: 'Payment',
          entityId: payment.id,
          details: {
            amount: payment.amount,
            gatewayTxnId: dto.gatewayTxnId,
            orderId: payment.orderId,
          },
        },
      });

      return updatedPayment;
    });

    this.logger.log(
      `Payment marked SUCCESS: #${payment.paymentNumber} with Txn: ${dto.gatewayTxnId}`,
    );
    return {
      success: true,
      message:
        'Payment confirmed successfully. Your hookah package is now being prepared for delivery.',
      payment: updated,
    };
  }

  async getPaymentStatus(orderId: string) {
    const payments = await this.prisma.payment.findMany({
      where: { orderId },
      orderBy: { createdAt: 'desc' },
    });

    if (!payments || payments.length === 0) {
      throw new NotFoundException(`No payment records found for Order #${orderId}`);
    }

    return payments[0];
  }
}
