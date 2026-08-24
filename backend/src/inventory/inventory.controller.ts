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
import { InventoryService } from './inventory.service';
import { CreateInventoryUnitDto } from './dto/create-inventory-unit.dto';
import { UpdateInventoryStatusDto } from './dto/update-inventory-status.dto';
import { UpdateInventoryConditionDto } from './dto/update-inventory-condition.dto';
import { InventoryFilterDto } from './dto/inventory-filter.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { CurrentUser } from '../auth/decorators/current-user.decorator';
import { UserRole } from '@prisma/client';

@ApiTags('Physical Hookah Inventory & Serials')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard, RolesGuard)
@Controller('inventory')
export class InventoryController {
  constructor(private readonly inventoryService: InventoryService) {}

  @Post()
  @Roles(UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Register a new serialized physical hookah unit' })
  @ApiResponse({ status: 201, description: 'Hookah inventory unit registered' })
  async createUnit(
    @Body() dto: CreateInventoryUnitDto,
    @CurrentUser() user: any,
  ) {
    return this.inventoryService.createUnit(dto, user);
  }

  @Get()
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'List serialized inventory units with filters' })
  @ApiResponse({ status: 200, description: 'Paginated list of inventory units' })
  async findAll(@Query() filter: InventoryFilterDto) {
    return this.inventoryService.findAll(filter);
  }

  @Get('metrics')
  @Roles(UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Get aggregated fleet inventory metrics and utilization rate' })
  @ApiResponse({ status: 200, description: 'Fleet metrics breakdown' })
  async getMetrics() {
    return this.inventoryService.getMetrics();
  }

  @Get('barcode/:barcode')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Find inventory unit by scannable barcode' })
  @ApiResponse({ status: 200, description: 'Inventory unit details' })
  async findByBarcode(@Param('barcode') barcode: string) {
    return this.inventoryService.findByBarcode(barcode);
  }

  @Get('serial/:serial')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Find inventory unit by serial number' })
  @ApiResponse({ status: 200, description: 'Inventory unit details' })
  async findBySerialNumber(@Param('serial') serial: string) {
    return this.inventoryService.findBySerialNumber(serial);
  }

  @Get(':id')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Get inventory unit details by UUID' })
  @ApiResponse({ status: 200, description: 'Detailed inventory record' })
  async findOne(@Param('id', ParseUUIDPipe) id: string) {
    return this.inventoryService.findOne(id);
  }

  @Patch(':id/status')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Update physical unit status' })
  @ApiResponse({ status: 200, description: 'Updated inventory unit' })
  async updateStatus(
    @Param('id', ParseUUIDPipe) id: string,
    @Body() dto: UpdateInventoryStatusDto,
    @CurrentUser() user: any,
  ) {
    return this.inventoryService.updateStatus(id, dto, user);
  }

  @Patch(':id/condition')
  @Roles(UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Update physical unit condition metrics' })
  @ApiResponse({ status: 200, description: 'Updated condition assessment' })
  async updateCondition(
    @Param('id', ParseUUIDPipe) id: string,
    @Body() dto: UpdateInventoryConditionDto,
    @CurrentUser() user: any,
  ) {
    return this.inventoryService.updateCondition(id, dto, user);
  }
}
