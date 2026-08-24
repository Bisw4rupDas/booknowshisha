import { IsEmail, IsNotEmpty, IsString, MinLength, IsOptional } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';

export class RegisterDto {
  @ApiProperty({ example: 'customer@shisharent.com' })
  @IsEmail()
  email!: string;

  @ApiProperty({ example: 'SecurePassword123!', minLength: 8 })
  @IsString()
  @MinLength(8)
  password!: string;

  @ApiProperty({ example: 'Rahul' })
  @IsString()
  @IsNotEmpty()
  firstName!: string;

  @ApiProperty({ example: 'Sharma' })
  @IsString()
  @IsNotEmpty()
  lastName!: string;

  @ApiProperty({ example: '+919903556825' })
  @IsString()
  @IsNotEmpty()
  phone!: string;

  @ApiProperty({ example: '42, Salt Lake Sector V', required: false })
  @IsString()
  @IsOptional()
  addressLine1?: string;

  @ApiProperty({ example: 'Kolkata', required: false })
  @IsString()
  @IsOptional()
  city?: string;

  @ApiProperty({ example: '700091', required: false })
  @IsString()
  @IsOptional()
  postalCode?: string;
}
