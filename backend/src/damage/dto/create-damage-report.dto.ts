import { IsString, IsNotEmpty, IsNumber, IsOptional, IsArray, Min, IsUUID } from 'class-validator';
import { Type } from 'class-transformer';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';

export class CreateDamageReportDto {
  @ApiProperty({ description: 'UUID of the rental associated with the damage' })
  @IsUUID()
  @IsNotEmpty()
  rentalId!: string;

  @ApiPropertyOptional({ description: 'Optional UUID of the ReturnInspection record' })
  @IsUUID()
  @IsOptional()
  inspectionId?: string;

  @ApiProperty({ description: 'Detailed damage description (e.g. cracked glass base, warped bowl, burnt hose)' })
  @IsString()
  @IsNotEmpty()
  description!: string;

  @ApiProperty({ description: 'Assessed repair or replacement cost in INR', example: 1200 })
  @Type(() => Number)
  @IsNumber()
  @Min(0)
  damageCost!: number;

  @ApiPropertyOptional({ description: 'Array of photo evidence URLs', type: [String] })
  @IsArray()
  @IsOptional()
  photos?: string[] = [];

  @ApiPropertyOptional({ description: 'Whether to automatically deduct damage cost from security deposit', default: true })
  @IsOptional()
  autoDeductFromDeposit?: boolean = true;
}
