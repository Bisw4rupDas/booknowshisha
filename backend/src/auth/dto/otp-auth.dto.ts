import { IsNotEmpty, IsString, Matches, Length } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';

export class SendOtpDto {
  @ApiProperty({ example: '9830012345', description: '10-digit Indian mobile number' })
  @IsString()
  @IsNotEmpty()
  @Matches(/^[6-9]\d{9}$|^(\+91|91)?[6-9]\d{9}$/, {
    message: 'Please provide a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9',
  })
  phone!: string;
}

export class VerifyOtpDto {
  @ApiProperty({ example: '9830012345', description: '10-digit Indian mobile number' })
  @IsString()
  @IsNotEmpty()
  phone!: string;

  @ApiProperty({ example: '123456', description: '6-digit OTP code' })
  @IsString()
  @IsNotEmpty()
  @Length(6, 6, { message: 'OTP code must be exactly 6 digits' })
  @Matches(/^\d{6}$/, { message: 'OTP code must contain digits only' })
  otp!: string;
}
