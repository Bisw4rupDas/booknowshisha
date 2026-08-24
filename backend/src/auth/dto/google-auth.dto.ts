import { IsString, IsNotEmpty, IsOptional, IsBoolean } from 'class-validator';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';

export class GoogleAuthTokenDto {
  @ApiProperty({
    description: 'Google ID Token or Authorization Code received from Google Sign-In SDK / Frontend',
    example: 'eyJhbGciOiJSUzI1NiIsImtpZCI6Ij...',
  })
  @IsString()
  @IsNotEmpty()
  token!: string;

  @ApiPropertyOptional({
    description: 'Flag indicating whether this authentication request is for the Administrator Operations Portal',
    default: false,
  })
  @IsOptional()
  @IsBoolean()
  isAdminLogin?: boolean;
}

export class GoogleOAuthCallbackQueryDto {
  @ApiProperty({
    description: 'OAuth2 Authorization Code returned by Google OAuth Consent redirect',
  })
  @IsString()
  @IsNotEmpty()
  code!: string;

  @ApiPropertyOptional({
    description: 'OAuth2 State Token ensuring CSRF protection and tracking login context (customer vs admin)',
  })
  @IsString()
  @IsOptional()
  state?: string;
}

export class GoogleOAuthUrlQueryDto {
  @ApiPropertyOptional({
    description: 'Indicates whether to initiate an Administrator login flow checking allowlist credentials',
    default: false,
  })
  @IsOptional()
  @IsBoolean()
  isAdmin?: boolean;
}
