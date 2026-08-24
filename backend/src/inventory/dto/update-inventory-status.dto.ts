import { IsEnum, IsNotEmpty, IsOptional, IsString } from 'class-validator';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { HookahInventoryStatus } from '@prisma/client';

export class UpdateInventoryStatusDto {
  @ApiProperty({ enum: HookahInventoryStatus, description: 'Target inventory unit status' })
  @IsEnum(HookahInventoryStatus)
  @IsNotEmpty()
  status!: HookahInventoryStatus;

  @ApiPropertyOptional({ description: 'Optional reason or maintenance notes' })
  @IsString()
  @IsOptional()
  notes?: string;
}
