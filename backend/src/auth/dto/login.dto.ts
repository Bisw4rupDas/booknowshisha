import { IsEmail, IsNotEmpty, IsString } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';

export class LoginDto {
  @ApiProperty({ example: 'customer@shisharent.com' })
  @IsEmail()
  email!: string;

  @ApiProperty({ example: 'ShishaRent@2026' })
  @IsString()
  @IsNotEmpty()
  password!: string;
}
