import { Controller, Post, Get, Body, Param, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiBearerAuth } from '@nestjs/swagger';
import { ReturnsService } from './returns.service';
import { ReturnInspectionDto } from './dto/return-inspection.dto';
import { DamageReportDto } from './dto/damage-report.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { CurrentUser } from '../auth/decorators/current-user.decorator';
import { User, UserRole } from '@prisma/client';

@ApiTags('Returns, Inspections & Damage')
@Controller('returns')
@UseGuards(JwtAuthGuard, RolesGuard)
@ApiBearerAuth()
export class ReturnsController {
  constructor(private readonly returnsService: ReturnsService) {}

  @Get()
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'List all rentals and active deployment statuses' })
  @ApiResponse({ status: 200, description: 'List of all rentals' })
  async findAll() {
    return this.returnsService.findAllRentals();
  }

  @Get(':id')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN, UserRole.CUSTOMER)
  @ApiOperation({ summary: 'Get rental record by ID' })
  @ApiResponse({ status: 200, description: 'Rental details' })
  async findOne(@Param('id') id: string) {
    return this.returnsService.findOneRental(id);
  }

  @Post(':id/return')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({
    summary: 'Submit return inspection checklist and release unit back to inventory',
  })
  @ApiResponse({ status: 200, description: 'Return inspection recorded and deposit processed' })
  async processReturn(
    @CurrentUser() user: User,
    @Param('id') rentalId: string,
    @Body() dto: ReturnInspectionDto,
  ) {
    return this.returnsService.processReturn(user.id, rentalId, dto);
  }

  @Post(':id/damage')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({
    summary: 'Report damaged/missing items and calculate security deposit deduction',
  })
  @ApiResponse({ status: 200, description: 'Damage report created and deposit adjusted' })
  async reportDamage(
    @CurrentUser() user: User,
    @Param('id') rentalId: string,
    @Body() dto: DamageReportDto,
  ) {
    return this.returnsService.reportDamage(user.id, rentalId, dto);
  }

  @Get(':id/inspection')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN, UserRole.CUSTOMER)
  @ApiOperation({ summary: 'Get digital return inspection report and deposit breakdown' })
  @ApiResponse({ status: 200, description: 'Inspection report' })
  async getInspection(@Param('id') rentalId: string) {
    return this.returnsService.getInspection(rentalId);
  }
}
