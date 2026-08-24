import { IsString, IsNotEmpty, IsOptional, IsArray, IsUUID } from 'class-validator';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';

export class CreateRentalDto {
  @ApiProperty({ description: 'UUID of the confirmed booking to activate as a rental' })
  @IsUUID()
  @IsNotEmpty()
  bookingId!: string;

  @ApiPropertyOptional({ description: 'Optional customer ID override for admin-created rentals' })
  @IsUUID()
  @IsOptional()
  customerId?: string;

  @ApiPropertyOptional({ description: 'Array of physical HookahInventory unit UUIDs assigned to this rental', type: [String] })
  @IsArray()
  @IsOptional()
  hookahInventoryIds?: string[];

  @ApiPropertyOptional({ description: 'Array of Flavour UUIDs included in this rental', type: [String] })
  @IsArray()
  @IsOptional()
  flavourIds?: string[];

  @ApiPropertyOptional({ description: 'Special operational or delivery notes' })
  @IsString()
  @IsOptional()
  notes?: string;
}
