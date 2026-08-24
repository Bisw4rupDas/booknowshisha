import {
  IsNotEmpty,
  IsString,
  IsArray,
  IsOptional,
  IsDateString,
  ArrayMinSize,
} from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';

export class CreateBookingDto {
  @ApiProperty({ description: 'Package ID or Slug to rent' })
  @IsString()
  @IsNotEmpty()
  packageId!: string;

  @ApiProperty({
    description: 'Specific Hookah Model ID or Slug (optional if package has default)',
  })
  @IsString()
  @IsOptional()
  hookahModelId?: string;

  @ApiProperty({ description: 'Array of selected Flavour IDs or Slugs', type: [String] })
  @IsArray()
  @ArrayMinSize(1, { message: 'At least one flavour must be selected' })
  @IsString({ each: true })
  flavourIds!: string[];

  @ApiProperty({ example: '2026-08-25T14:00:00.000Z', description: 'Desired start date and time' })
  @IsDateString()
  @IsNotEmpty()
  rentalStart!: string;

  @ApiProperty({ description: 'Selected delivery slot ID or Time Window (e.g. 18:00-20:00)' })
  @IsString()
  @IsNotEmpty()
  deliverySlotId!: string;

  @ApiProperty({ example: '42, Salt Lake Sector V, Kolkata', description: 'Complete delivery address' })
  @IsString()
  @IsNotEmpty()
  deliveryAddress!: string;

  @ApiProperty({ example: '700091', description: '6-digit Postal PIN Code' })
  @IsString()
  @IsNotEmpty()
  postalCode!: string;

  @ApiProperty({ example: 'Ring the side doorbell on arrival', required: false })
  @IsString()
  @IsOptional()
  notes?: string;

  @ApiProperty({ description: 'Customer Email (for Bridge/Guest bookings)', required: false })
  @IsString()
  @IsOptional()
  customerEmail?: string;

  @ApiProperty({ description: 'Customer Phone (for Bridge/Guest bookings)', required: false })
  @IsString()
  @IsOptional()
  customerPhone?: string;

  @ApiProperty({ description: 'Customer Name (for Bridge/Guest bookings)', required: false })
  @IsString()
  @IsOptional()
  customerName?: string;

  @ApiProperty({ description: 'WooCommerce Order ID mapping', required: false })
  @IsOptional()
  wpOrderId?: number;

  @ApiProperty({ enum: ['COD', 'UPI'], description: 'Initial payment method', required: false })
  @IsString()
  @IsOptional()
  paymentMethod?: 'COD' | 'UPI';

  @ApiProperty({
    enum: ['standard', 'ice', 'milk', 'both', 'ice_milk'],
    description: 'Hookah Base Upgrade Option (standard, ice, milk, both)',
    example: 'both',
    required: false,
  })
  @IsString()
  @IsOptional()
  hookahBase?: 'standard' | 'ice' | 'milk' | 'both' | 'ice_milk' | string;
}
