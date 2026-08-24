import { IsNotEmpty, IsUUID, IsOptional, IsEnum, IsNumber, Min } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';
import { PaymentMethod } from '@prisma/client';

export class InitiatePaymentDto {
  @ApiProperty({ description: 'Associated Booking ID' })
  @IsUUID()
  @IsNotEmpty()
  bookingId!: string;

  @ApiProperty({ enum: PaymentMethod, default: PaymentMethod.UPI })
  @IsEnum(PaymentMethod)
  method!: PaymentMethod;

  @ApiProperty({ example: 1649.0, description: 'Payment amount to authorize' })
  @IsNumber()
  @Min(1)
  amount!: number;

  @ApiProperty({ example: 'ShishaRent Solo Rental', required: false })
  @IsOptional()
  notes?: string;
}

export class InitiateUpiDto {
  @ApiProperty({ description: 'Associated Booking ID' })
  @IsUUID()
  @IsNotEmpty()
  bookingId!: string;

  @ApiProperty({ example: 1649.0, description: 'Payment amount to authorize' })
  @IsNumber()
  @Min(1)
  amount!: number;

  @ApiProperty({ example: 'ShishaRent Solo Rental', required: false })
  @IsOptional()
  notes?: string;
}

export class ConfirmPaymentDto {
  @ApiProperty({ example: 'UPI-TXN-9847291834', description: 'UPI Gateway Transaction Reference' })
  @IsNotEmpty()
  gatewayTxnId!: string;
}

export class UpiWebhookDto {
  @ApiProperty({ example: 'SR-PAY-123456' })
  @IsNotEmpty()
  paymentNumber!: string;

  @ApiProperty({ example: 'UPI-TXN-9847291834' })
  @IsNotEmpty()
  gatewayTxnId!: string;

  @ApiProperty({ example: 'SUCCESS', enum: ['SUCCESS', 'FAILED'] })
  @IsNotEmpty()
  status!: 'SUCCESS' | 'FAILED';

  @ApiProperty({ example: 1649.0 })
  @IsNumber()
  amount!: number;

  @ApiProperty({ required: false })
  @IsOptional()
  gatewayRaw?: Record<string, unknown>;
}
