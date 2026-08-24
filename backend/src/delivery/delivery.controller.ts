import { Controller, Get, Post, Body, Query } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiQuery } from '@nestjs/swagger';
import { DeliveryService } from './delivery.service';
import { CheckZoneDto } from './dto/check-zone.dto';

@ApiTags('Delivery & Availability')
@Controller('delivery')
export class DeliveryController {
  constructor(private readonly deliveryService: DeliveryService) {}

  @Post('check-zone')
  @ApiOperation({
    summary: 'Check if a postal code is serviceable and retrieve available slots & fee',
  })
  @ApiResponse({ status: 200, description: 'Serviceability status' })
  async checkZone(@Body() dto: CheckZoneDto) {
    return this.deliveryService.checkZone(dto);
  }

  @Get('zones')
  @ApiOperation({ summary: 'List all active delivery zones' })
  @ApiResponse({ status: 200, description: 'List of delivery zones' })
  async getZones() {
    return this.deliveryService.getZones();
  }

  @Get('slots')
  @ApiOperation({ summary: 'Get delivery time slots (optionally filtered by postal code)' })
  @ApiQuery({ name: 'postalCode', required: false })
  @ApiResponse({ status: 200, description: 'Delivery slots' })
  async getSlots(@Query('postalCode') postalCode?: string) {
    return this.deliveryService.getSlots(postalCode);
  }
}
