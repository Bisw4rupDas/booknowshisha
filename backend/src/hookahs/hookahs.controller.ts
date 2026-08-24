import { Controller, Get, Param } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse } from '@nestjs/swagger';
import { HookahsService } from './hookahs.service';

@ApiTags('Hookahs')
@Controller('hookahs')
export class HookahsController {
  constructor(private readonly hookahsService: HookahsService) {}

  @Get()
  @ApiOperation({ summary: 'List all available hookah models' })
  @ApiResponse({ status: 200, description: 'List of hookah models' })
  async findAll() {
    return this.hookahsService.findAll();
  }

  @Get('slug/:slug')
  @ApiOperation({ summary: 'Get hookah model by unique slug' })
  @ApiResponse({ status: 200, description: 'Hookah details' })
  @ApiResponse({ status: 404, description: 'Hookah not found' })
  async findBySlug(@Param('slug') slug: string) {
    return this.hookahsService.findBySlug(slug);
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get hookah model by ID' })
  @ApiResponse({ status: 200, description: 'Hookah details' })
  @ApiResponse({ status: 404, description: 'Hookah not found' })
  async findOne(@Param('id') id: string) {
    return this.hookahsService.findOne(id);
  }
}
