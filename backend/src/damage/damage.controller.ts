import {
  Controller,
  Get,
  Post,
  Body,
  Param,
  Query,
  UseGuards,
  ParseUUIDPipe,
} from '@nestjs/common';
import {
  ApiTags,
  ApiOperation,
  ApiResponse,
  ApiBearerAuth,
} from '@nestjs/swagger';
import { DamageService } from './damage.service';
import { CreateDamageReportDto } from './dto/create-damage-report.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { CurrentUser } from '../auth/decorators/current-user.decorator';
import { UserRole } from '@prisma/client';

@ApiTags('Damage Assessment & Security Deposits')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard, RolesGuard)
@Controller('damage')
export class DamageController {
  constructor(private readonly damageService: DamageService) {}

  @Post()
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Submit a physical damage assessment and calculate deposit deductions' })
  @ApiResponse({ status: 201, description: 'Damage report created and deposit adjusted' })
  async createDamageReport(
    @Body() dto: CreateDamageReportDto,
    @CurrentUser() user: any,
  ) {
    return this.damageService.createDamageReport(dto, user);
  }

  @Get()
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'List all filed damage reports across the system' })
  @ApiResponse({ status: 200, description: 'Paginated list of damage reports' })
  async findAll(
    @Query('page') page?: number,
    @Query('limit') limit?: number,
  ) {
    return this.damageService.findAll(Number(page) || 1, Number(limit) || 20);
  }

  @Get('rental/:rentalId')
  @Roles(UserRole.CUSTOMER, UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Get damage reports associated with a specific rental' })
  @ApiResponse({ status: 200, description: 'List of damage reports for the rental' })
  async findByRental(@Param('rentalId', ParseUUIDPipe) rentalId: string) {
    return this.damageService.findByRental(rentalId);
  }

  @Get(':id')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Get single damage report details by UUID' })
  @ApiResponse({ status: 200, description: 'Damage report details' })
  async findOne(@Param('id', ParseUUIDPipe) id: string) {
    return this.damageService.findOne(id);
  }
}
