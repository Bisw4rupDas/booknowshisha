import { IsEnum, IsOptional, IsString, IsInt, Min, Max, IsUUID } from 'class-validator';
import { Type } from 'class-transformer';
import { ApiPropertyOptional } from '@nestjs/swagger';
import { HookahCondition, HookahInventoryStatus } from '@prisma/client';

export class InventoryFilterDto {
  @ApiPropertyOptional({ description: 'Filter by HookahModel UUID' })
  @IsUUID()
  @IsOptional()
  hookahModelId?: string;

  @ApiPropertyOptional({ enum: HookahInventoryStatus })
  @IsEnum(HookahInventoryStatus)
  @IsOptional()
  status?: HookahInventoryStatus;

  @ApiPropertyOptional({ enum: HookahCondition })
  @IsEnum(HookahCondition)
  @IsOptional()
  condition?: HookahCondition;

  @ApiPropertyOptional({ description: 'Search serial number, barcode, or model name' })
  @IsString()
  @IsOptional()
  search?: string;

  @ApiPropertyOptional({ default: 1, minimum: 1 })
  @Type(() => Number)
  @IsInt()
  @Min(1)
  @IsOptional()
  page?: number = 1;

  @ApiPropertyOptional({ default: 20, minimum: 1, maximum: 100 })
  @Type(() => Number)
  @IsInt()
  @Min(1)
  @Max(100)
  @IsOptional()
  limit?: number = 20;
}
