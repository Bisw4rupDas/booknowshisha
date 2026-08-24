import {
  Controller,
  Get,
  Patch,
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
import { CustomersService } from './customers.service';
import { UpdateCustomerProfileDto } from './dto/update-customer-profile.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { CurrentUser } from '../auth/decorators/current-user.decorator';
import { UserRole } from '@prisma/client';

@ApiTags('Customer Profiles & Address Book')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard, RolesGuard)
@Controller('customers')
export class CustomersController {
  constructor(private readonly customersService: CustomersService) {}

  @Get('me')
  @ApiOperation({ summary: 'Get current customer profile & stats' })
  @ApiResponse({ status: 200, description: 'Customer profile object' })
  async getMyProfile(@CurrentUser() user: any) {
    return this.customersService.getProfile(user.id);
  }

  @Patch('me')
  @ApiOperation({ summary: 'Update customer contact info and address' })
  @ApiResponse({ status: 200, description: 'Updated customer profile' })
  async updateMyProfile(
    @CurrentUser() user: any,
    @Body() dto: UpdateCustomerProfileDto,
  ) {
    return this.customersService.updateProfile(user.id, dto);
  }

  @Get()
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'List customer directory (Admin/Staff)' })
  @ApiResponse({ status: 200, description: 'Paginated customer list' })
  async findAll(
    @Query('search') search?: string,
    @Query('page') page?: number,
    @Query('limit') limit?: number,
  ) {
    return this.customersService.findAll(search, Number(page) || 1, Number(limit) || 20);
  }

  @Get(':id')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Get single customer details by UUID' })
  @ApiResponse({ status: 200, description: 'Detailed customer record' })
  async findById(@Param('id', ParseUUIDPipe) id: string) {
    return this.customersService.findById(id);
  }
}
