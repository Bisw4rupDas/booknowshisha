import { IsEnum, IsNotEmpty, IsOptional, IsString } from 'class-validator';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { RentalStatus } from '@prisma/client';

export class UpdateRentalStatusDto {
  @ApiProperty({ enum: RentalStatus, description: 'Target lifecycle status for the rental' })
  @IsEnum(RentalStatus)
  @IsNotEmpty()
  status!: RentalStatus;

  @ApiPropertyOptional({ description: 'Optional operational reason or notes for the status change' })
  @IsString()
  @IsOptional()
  notes?: string;
}
