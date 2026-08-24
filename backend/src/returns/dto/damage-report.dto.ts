import { IsNotEmpty, IsNumber, Min, IsArray, IsString, IsOptional } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';

export class DamageReportDto {
  @ApiProperty({ example: 'Crack found in borosilicate base glass bowl' })
  @IsString()
  @IsNotEmpty()
  description!: string;

  @ApiProperty({ example: 800.0, description: 'Assessed damage repair/replacement fee' })
  @IsNumber()
  @Min(1)
  damageCost!: number;

  @ApiProperty({
    example: ['https://storage.shisharent.com/damage-001.jpg'],
    type: [String],
    required: false,
  })
  @IsArray()
  @IsOptional()
  photos?: string[];
}
