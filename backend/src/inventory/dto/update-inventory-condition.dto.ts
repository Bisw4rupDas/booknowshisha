import { IsEnum, IsNotEmpty, IsOptional, IsString } from 'class-validator';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { HookahCondition } from '@prisma/client';

export class UpdateInventoryConditionDto {
  @ApiProperty({ enum: HookahCondition, description: 'Target physical condition assessment' })
  @IsEnum(HookahCondition)
  @IsNotEmpty()
  condition!: HookahCondition;

  @ApiPropertyOptional({ description: 'Inspection or damage observation notes' })
  @IsString()
  @IsOptional()
  notes?: string;
}
