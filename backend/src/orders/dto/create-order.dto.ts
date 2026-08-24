import {
  IsString,
  IsNotEmpty,
  IsNumber,
  IsOptional,
  IsArray,
  ValidateNested,
  Min,
  IsUUID,
} from 'class-validator';
import { Type } from 'class-transformer';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';

export class OrderItemDto {
  @ApiProperty({ description: 'Product or Package name' })
  @IsString()
  @IsNotEmpty()
  name!: string;

  @ApiPropertyOptional({ description: 'WooCommerce Product ID' })
  @IsOptional()
  wpProductId?: number;

  @ApiProperty({ description: 'Item quantity', default: 1 })
  @Type(() => Number)
  @IsNumber()
  @Min(1)
  quantity!: number;

  @ApiProperty({ description: 'Price per unit in INR' })
  @Type(() => Number)
  @IsNumber()
  @Min(0)
  unitPrice!: number;
}

export class CreateOrderDto {
  @ApiProperty({ description: 'Customer UUID' })
  @IsUUID()
  @IsNotEmpty()
  customerId!: string;

  @ApiPropertyOptional({ description: 'Optional associated Booking UUID' })
  @IsUUID()
  @IsOptional()
  bookingId?: string;

  @ApiPropertyOptional({ description: 'WooCommerce Order ID if synced from WordPress' })
  @IsOptional()
  wpOrderId?: number;

  @ApiPropertyOptional({ description: 'Subtotal amount in INR' })
  @Type(() => Number)
  @IsNumber()
  @Min(0)
  @IsOptional()
  subtotal?: number;

  @ApiPropertyOptional({ description: 'Delivery fee amount in INR', default: 0 })
  @Type(() => Number)
  @IsNumber()
  @Min(0)
  @IsOptional()
  deliveryFee?: number = 0;

  @ApiPropertyOptional({ description: 'Deposit amount in INR', default: 0 })
  @Type(() => Number)
  @IsNumber()
  @Min(0)
  @IsOptional()
  deposit?: number = 0;

  @ApiPropertyOptional({ description: 'Discount applied in INR', default: 0 })
  @Type(() => Number)
  @IsNumber()
  @Min(0)
  @IsOptional()
  discount?: number = 0;

  @ApiProperty({ description: 'Total order amount in INR' })
  @Type(() => Number)
  @IsNumber()
  @Min(0)
  totalAmount!: number;

  @ApiProperty({ description: 'Itemized product lines', type: [OrderItemDto] })
  @IsArray()
  @ValidateNested({ each: true })
  @Type(() => OrderItemDto)
  items!: OrderItemDto[];

  @ApiPropertyOptional({ description: 'Order delivery notes or instructions' })
  @IsString()
  @IsOptional()
  notes?: string;
}
