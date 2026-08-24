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
import { OrdersService } from './orders.service';
import { CreateOrderDto } from './dto/create-order.dto';
import { OrderFilterDto } from './dto/order-filter.dto';
import { WooCommerceOrderWebhookDto } from './dto/woocommerce-webhook.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { CurrentUser } from '../auth/decorators/current-user.decorator';
import { UserRole, OrderStatus } from '@prisma/client';

@ApiTags('E-Commerce Orders & WooCommerce Sync')
@Controller('orders')
export class OrdersController {
  constructor(private readonly ordersService: OrdersService) {}

  @Post()
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles(UserRole.CUSTOMER, UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Create a new order with itemized products' })
  @ApiResponse({ status: 201, description: 'Order created successfully' })
  async createOrder(
    @Body() dto: CreateOrderDto,
    @CurrentUser() user: any,
  ) {
    return this.ordersService.createOrder(dto, user);
  }

  @Post('webhook/woocommerce')
  @ApiOperation({ summary: 'Inbound webhook receiver from WooCommerce for automatic order sync' })
  @ApiResponse({ status: 200, description: 'WooCommerce order synced' })
  async syncFromWooCommerce(@Body() dto: WooCommerceOrderWebhookDto) {
    return this.ordersService.syncFromWooCommerce(dto);
  }

  @Get()
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles(UserRole.CUSTOMER, UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'List customer or system orders with filters' })
  @ApiResponse({ status: 200, description: 'Paginated list of orders' })
  async findAll(
    @Query() filter: OrderFilterDto,
    @CurrentUser() user: any,
  ) {
    return this.ordersService.findAll(filter, user);
  }

  @Get(':id')
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles(UserRole.CUSTOMER, UserRole.STAFF, UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Get single order details by UUID' })
  @ApiResponse({ status: 200, description: 'Detailed order record' })
  async findOne(
    @Param('id', ParseUUIDPipe) id: string,
    @CurrentUser() user: any,
  ) {
    return this.ordersService.findOne(id, user);
  }

  @Patch(':id/status')
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles(UserRole.ADMIN, UserRole.SUPER_ADMIN)
  @ApiOperation({ summary: 'Update order fulfillment status' })
  @ApiResponse({ status: 200, description: 'Updated order record' })
  async updateStatus(
    @Param('id', ParseUUIDPipe) id: string,
    @Body('status') status: OrderStatus,
    @CurrentUser() user: any,
  ) {
    return this.ordersService.updateStatus(id, status, user);
  }
}
