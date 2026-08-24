import { IsNumber, Min, IsOptional, IsString, IsBoolean } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';

export class CollectCodDto {
  @ApiProperty({ example: 1649.0, description: 'Exact cash amount collected' })
  @IsNumber()
  @Min(1)
  amount!: number;

  @ApiProperty({
    example: false,
    description: 'Whether this is a partial collection',
    required: false,
  })
  @IsBoolean()
  @IsOptional()
  isPartial?: boolean;

  @ApiProperty({ example: 'Cash received by delivery courier Vikram', required: false })
  @IsString()
  @IsOptional()
  notes?: string;
}
