import { IsBoolean, IsOptional, IsString, IsEnum } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';
import { InspectionStatus } from '@prisma/client';

export class ReturnInspectionDto {
  @ApiProperty({ enum: InspectionStatus, default: InspectionStatus.PASSED })
  @IsEnum(InspectionStatus)
  status!: InspectionStatus;

  @ApiProperty({ example: true, description: 'Hookah glass and stem returned clean' })
  @IsBoolean()
  isClean!: boolean;

  @ApiProperty({ example: true, description: 'All accessories, tongs, tray, bowl present' })
  @IsBoolean()
  allPartsPresent!: boolean;

  @ApiProperty({ example: 'Returned in pristine condition', required: false })
  @IsString()
  @IsOptional()
  notes?: string;
}
