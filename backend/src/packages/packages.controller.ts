import { Controller, Get, Param } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse } from '@nestjs/swagger';
import { PackagesService } from './packages.service';

@ApiTags('Packages')
@Controller('packages')
export class PackagesController {
  constructor(private readonly packagesService: PackagesService) {}

  @Get()
  @ApiOperation({ summary: 'List all active rental packages and bundled specs' })
  @ApiResponse({ status: 200, description: 'List of packages' })
  async findAll() {
    return this.packagesService.findAll();
  }

  @Get('slug/:slug')
  @ApiOperation({ summary: 'Get rental package by unique slug' })
  @ApiResponse({ status: 200, description: 'Package details' })
  @ApiResponse({ status: 404, description: 'Package not found' })
  async findBySlug(@Param('slug') slug: string) {
    return this.packagesService.findBySlug(slug);
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get rental package by ID' })
  @ApiResponse({ status: 200, description: 'Package details' })
  @ApiResponse({ status: 404, description: 'Package not found' })
  async findOne(@Param('id') id: string) {
    return this.packagesService.findOne(id);
  }
}
