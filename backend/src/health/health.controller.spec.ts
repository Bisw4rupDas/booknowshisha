import { Test, TestingModule } from '@nestjs/testing';
import { HealthController } from './health.controller';
import { HealthService, HealthCheckResult } from './health.service';

describe('HealthController', () => {
  let healthController: HealthController;
  let healthService: HealthService;

  beforeEach(async () => {
    const mockHealthResult: HealthCheckResult = {
      status: 'ok',
      timestamp: new Date().toISOString(),
      uptime: 120,
      environment: 'test',
      services: {
        database: { status: 'connected', latencyMs: 5 },
        redis: { status: 'connected', latencyMs: 2 },
      },
    };

    const module: TestingModule = await Test.createTestingModule({
      controllers: [HealthController],
      providers: [
        {
          provide: HealthService,
          useValue: {
            check: jest.fn().mockResolvedValue(mockHealthResult),
          },
        },
      ],
    }).compile();

    healthController = module.get<HealthController>(HealthController);
    healthService = module.get<HealthService>(HealthService);
  });

  it('should be defined', () => {
    expect(healthController).toBeDefined();
  });

  it('should return health status', async () => {
    const result = await healthController.getHealth();
    expect(result.status).toBe('ok');
    expect(result.services.database.status).toBe('connected');
    expect(healthService.check).toHaveBeenCalled();
  });
});
