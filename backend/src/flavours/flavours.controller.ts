import { Controller, Get, Query } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiQuery } from '@nestjs/swagger';
import { FlavoursService } from './flavours.service';

@ApiTags('Flavours')
@Controller('flavours')
export class FlavoursController {
  constructor(private readonly flavoursService: FlavoursService) {}

  @Get()
  @ApiOperation({ summary: 'List all active hookah flavours with stock levels' })
  @ApiQuery({ name: 'categoryId', required: false })
  @ApiResponse({ status: 200, description: 'List of flavours' })
  async findAll(@Query('categoryId') categoryId?: string) {
    return this.flavoursService.findAll(categoryId);
  }

  @Get('categories')
  @ApiOperation({ summary: 'List all flavour categories with nested flavours' })
  @ApiResponse({ status: 200, description: 'List of flavour categories' })
  async findCategories() {
    return this.flavoursService.findCategories();
  }
}
