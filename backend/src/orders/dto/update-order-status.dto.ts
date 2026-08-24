import { IsEnum, IsNotEmpty } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';
import { OrderStatus } from '@prisma/client';

export class UpdateOrderStatusDto {
  @ApiProperty({ enum: OrderStatus, description: 'Updated order status' })
  @IsEnum(OrderStatus)
  @IsNotEmpty()
  status!: OrderStatus;
}
