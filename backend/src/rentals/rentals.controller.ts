import {
  Controller,
  Get,
  Post,
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
import { RentalsService } from './rentals.service';
import { CreateRentalDto } from './dto/create-rental.dto';
import { UpdateRentalStatusDto } from './dto/update-rental-status.dto';
import { RentalFilterDto } from './dto/rental-filter.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { CurrentUser } from '../auth/decorators/current-user.decorator';
import { UserRole } from '@prisma/client';

@ApiTags('Rentals & Lifecycle')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard, RolesGuard)
@Controller('rentals')
export class RentalsController {
  constructor(private readonly rentalsService: RentalsService) {}

  @Post()
  @Roles(UserRole.CUSTOMER, UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Initialize a new rental from a confirmed booking' })
  @ApiResponse({ status: 201, description: 'Rental initialized successfully' })
  async createRental(
    @Body() dto: CreateRentalDto,
    @CurrentUser() user: any,
  ) {
    return this.rentalsService.createRental(dto, user);
  }

  @Get()
  @Roles(UserRole.CUSTOMER, UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'List rentals with status, customer, and search filters' })
  @ApiResponse({ status: 200, description: 'Paginated list of rentals' })
  async findAll(
    @Query() filter: RentalFilterDto,
    @CurrentUser() user: any,
  ) {
    return this.rentalsService.findAll(filter, user);
  }

  @Get(':id')
  @Roles(UserRole.CUSTOMER, UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Get complete rental details by UUID' })
  @ApiResponse({ status: 200, description: 'Detailed rental record' })
  async findOne(
    @Param('id', ParseUUIDPipe) id: string,
    @CurrentUser() user: any,
  ) {
    return this.rentalsService.findOne(id, user);
  }

  @Patch(':id/status')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Transition rental lifecycle status' })
  @ApiResponse({ status: 200, description: 'Updated rental record' })
  async updateStatus(
    @Param('id', ParseUUIDPipe) id: string,
    @Body() dto: UpdateRentalStatusDto,
    @CurrentUser() user: any,
  ) {
    return this.rentalsService.updateStatus(id, dto, user);
  }

  @Post(':id/cancel')
  @Roles(UserRole.CUSTOMER, UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Cancel an active or pending rental' })
  @ApiResponse({ status: 200, description: 'Rental cancelled and resources released' })
  async cancelRental(
    @Param('id', ParseUUIDPipe) id: string,
    @CurrentUser() user: any,
    @Body('reason') reason?: string,
  ) {
    return this.rentalsService.cancelRental(id, user, reason);
  }

  @Post(':id/return')
  @Roles(UserRole.CUSTOMER, UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Request return collection for an active rental' })
  @ApiResponse({ status: 200, description: 'Return collection requested' })
  async requestReturn(
    @Param('id', ParseUUIDPipe) id: string,
    @CurrentUser() user: any,
  ) {
    return this.rentalsService.requestReturn(id, user);
  }
}
