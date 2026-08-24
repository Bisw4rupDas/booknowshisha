import { Controller, Get, Post, Body, Param, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiBearerAuth } from '@nestjs/swagger';
import { AdminService } from './admin.service';
import { CollectCodDto } from './dto/collect-cod.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { CurrentUser } from '../auth/decorators/current-user.decorator';
import { User, UserRole } from '@prisma/client';

@ApiTags('Admin & Field Operations')
@Controller('admin')
@UseGuards(JwtAuthGuard, RolesGuard)
@ApiBearerAuth()
export class AdminController {
  constructor(private readonly adminService: AdminService) {}

  @Get('cod/pending')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'List all COD orders pending staff collection' })
  @ApiResponse({ status: 200, description: 'Pending COD payments' })
  async getPendingCodPayments() {
    return this.adminService.getPendingCodPayments();
  }

  @Post('cod/:paymentId/collect')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Record and reconcile Cash on Delivery collected by courier' })
  @ApiResponse({ status: 200, description: 'Cash collected and rental activated' })
  async collectCod(
    @CurrentUser() user: User,
    @Param('paymentId') paymentId: string,
    @Body() dto: CollectCodDto,
  ) {
    return this.adminService.collectCod(user.id, paymentId, dto);
  }

  @Get('metrics')
  @Roles(UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Get operational KPIs, active rentals and revenue metrics' })
  @ApiResponse({ status: 200, description: 'Platform metrics' })
  async getPlatformMetrics() {
    return this.adminService.getPlatformMetrics();
  }
}
