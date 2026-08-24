import { Controller, Post, Get, Body, Param, Headers, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiBearerAuth, ApiHeader } from '@nestjs/swagger';
import { PaymentsService } from './payments.service';
import {
  InitiatePaymentDto,
  InitiateUpiDto,
  ConfirmPaymentDto,
  UpiWebhookDto,
} from './dto/initiate-upi.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { CurrentUser } from '../auth/decorators/current-user.decorator';
import { User } from '@prisma/client';

@ApiTags('Payments & Transactions')
@Controller('payments')
export class PaymentsController {
  constructor(private readonly paymentsService: PaymentsService) {}

  @Post('initiate')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: 'Initiate payment (UPI dynamic QR/Intent or COD confirmation)' })
  @ApiResponse({ status: 201, description: 'Payment initialized with intent string' })
  async initiatePayment(@CurrentUser() user: User, @Body() dto: InitiatePaymentDto) {
    return this.paymentsService.initiatePayment(user.id, dto);
  }

  @Post('upi/initiate')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: 'Initiate UPI payment and receive dynamic UPI intent string & QR data' })
  @ApiResponse({ status: 201, description: 'UPI Payment initiated' })
  async initiateUpi(@CurrentUser() user: User, @Body() dto: InitiateUpiDto) {
    return this.paymentsService.initiateUpi(user.id, dto);
  }

  @Post('upi/webhook')
  @ApiOperation({ summary: 'Webhook endpoint for UPI payment aggregator callbacks (idempotent)' })
  @ApiHeader({ name: 'x-webhook-signature', required: false })
  @ApiResponse({ status: 200, description: 'Webhook processed' })
  async handleUpiWebhook(
    @Body() dto: UpiWebhookDto,
    @Headers('x-webhook-signature') signature?: string,
  ) {
    return this.paymentsService.processWebhook(dto, signature);
  }

  @Post('webhook')
  @ApiOperation({ summary: 'General payment webhook aggregator callback' })
  @ApiResponse({ status: 200, description: 'Webhook processed' })
  async handleWebhook(
    @Body() dto: UpiWebhookDto,
    @Headers('x-webhook-signature') signature?: string,
  ) {
    return this.paymentsService.processWebhook(dto, signature);
  }

  @Post(':paymentId/confirm')
  @ApiOperation({ summary: 'Confirm UPI payment completion (manual or gateway webhook)' })
  @ApiResponse({ status: 200, description: 'Payment confirmed and rental activated' })
  async confirmPayment(@Param('paymentId') paymentId: string, @Body() dto: ConfirmPaymentDto) {
    return this.paymentsService.confirmPayment(paymentId, dto);
  }

  @Get('order/:orderId/status')
  @ApiOperation({ summary: 'Check payment status for given Order' })
  @ApiResponse({ status: 200, description: 'Current payment state' })
  async getPaymentStatus(@Param('orderId') orderId: string) {
    return this.paymentsService.getPaymentStatus(orderId);
  }
}
