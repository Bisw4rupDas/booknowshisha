import { IsNotEmpty, IsString, Length, Matches } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';

export class CheckZoneDto {
  @ApiProperty({ example: '700091', description: '6-digit Indian PIN/Postal code' })
  @IsString()
  @IsNotEmpty()
  @Length(6, 6, { message: 'Postal code must be exactly 6 digits' })
  @Matches(/^[1-9][0-9]{5}$/, { message: 'Postal code must be a valid 6-digit numeric PIN' })
  postalCode!: string;
}
