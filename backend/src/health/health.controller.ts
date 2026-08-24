import { Controller, Get } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse } from '@nestjs/swagger';
import { HealthService, HealthCheckResult } from './health.service';

@ApiTags('Health')
@Controller('health')
export class HealthController {
  constructor(private readonly healthService: HealthService) {}

  @Get()
  @ApiOperation({ summary: 'Check API and infrastructure health status' })
  @ApiResponse({
    status: 200,
    description: 'System health diagnostic status',
  })
  async getHealth(): Promise<HealthCheckResult> {
    return this.healthService.check();
  }
}
