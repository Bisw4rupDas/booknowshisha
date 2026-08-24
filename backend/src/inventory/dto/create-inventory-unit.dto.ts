import { IsString, IsNotEmpty, IsOptional, IsEnum, IsUUID } from 'class-validator';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { HookahCondition, HookahInventoryStatus } from '@prisma/client';

export class CreateInventoryUnitDto {
  @ApiProperty({ description: 'UUID of the HookahModel catalog item' })
  @IsUUID()
  @IsNotEmpty()
  hookahModelId!: string;

  @ApiProperty({ description: 'Unique physical serial number (e.g. KM-GLD-007)' })
  @IsString()
  @IsNotEmpty()
  serialNumber!: string;

  @ApiPropertyOptional({ description: 'Scannable barcode string' })
  @IsString()
  @IsOptional()
  barcode?: string;

  @ApiPropertyOptional({ enum: HookahCondition, default: HookahCondition.EXCELLENT })
  @IsEnum(HookahCondition)
  @IsOptional()
  condition?: HookahCondition = HookahCondition.EXCELLENT;

  @ApiPropertyOptional({ enum: HookahInventoryStatus, default: HookahInventoryStatus.AVAILABLE })
  @IsEnum(HookahInventoryStatus)
  @IsOptional()
  status?: HookahInventoryStatus = HookahInventoryStatus.AVAILABLE;

  @ApiPropertyOptional({ description: 'Initial unit notes' })
  @IsString()
  @IsOptional()
  notes?: string;
}
