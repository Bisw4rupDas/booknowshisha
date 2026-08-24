import { IsNotEmpty, IsOptional, IsString, IsObject } from 'class-validator';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';

export class WooCommerceOrderWebhookDto {
  @ApiProperty({ description: 'WooCommerce Webhook raw payload', type: Object })
  @IsObject()
  @IsNotEmpty()
  orderData!: Record<string, any>;

  @ApiPropertyOptional({ description: 'Webhook topic header (e.g. order.created, order.updated)' })
  @IsString()
  @IsOptional()
  topic?: string;
}
