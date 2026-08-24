import { Injectable, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { PrismaService } from '../prisma/prisma.service';
import Redis from 'ioredis';

export interface HealthCheckResult {
  status: 'ok' | 'degraded' | 'error';
  timestamp: string;
  uptime: number;
  environment: string;
  services: {
    database: {
      status: 'connected' | 'disconnected' | 'unreachable';
      latencyMs?: number;
    };
    redis: {
      status: 'connected' | 'disconnected' | 'unreachable' | 'disabled';
      latencyMs?: number;
    };
  };
}

@Injectable()
export class HealthService {
  private readonly logger = new Logger(HealthService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly configService: ConfigService,
  ) {}

  async check(): Promise<HealthCheckResult> {
    const env = this.configService.get<string>('NODE_ENV', 'development');

    // Database check
    let dbStatus: 'connected' | 'disconnected' | 'unreachable' = 'disconnected';
    let dbLatency: number | undefined;
    try {
      const dbStart = Date.now();
      await this.prisma.$queryRaw`SELECT 1`;
      dbLatency = Date.now() - dbStart;
      dbStatus = 'connected';
    } catch (err) {
      this.logger.debug(`Health check: Database ping failed: ${err}`);
      dbStatus = 'unreachable';
    }

    // Redis check
    let redisStatus: 'connected' | 'disconnected' | 'unreachable' | 'disabled' = 'disabled';
    let redisLatency: number | undefined;
    const redisUrl = this.configService.get<string>('REDIS_URL');

    if (redisUrl) {
      const redis = new Redis(redisUrl, {
        connectTimeout: 1000,
        maxRetriesPerRequest: 1,
        lazyConnect: true,
      });

      try {
        const redisStart = Date.now();
        await redis.connect();
        await redis.ping();
        redisLatency = Date.now() - redisStart;
        redisStatus = 'connected';
        await redis.disconnect();
      } catch (err) {
        this.logger.debug(`Health check: Redis ping failed: ${err}`);
        redisStatus = 'unreachable';
        redis.disconnect();
      }
    }

    const isHealthy =
      dbStatus === 'connected' && (redisStatus === 'connected' || redisStatus === 'disabled');

    return {
      status: isHealthy ? 'ok' : 'degraded',
      timestamp: new Date().toISOString(),
      uptime: process.uptime(),
      environment: env,
      services: {
        database: {
          status: dbStatus,
          latencyMs: dbLatency,
        },
        redis: {
          status: redisStatus,
          latencyMs: redisLatency,
        },
      },
    };
  }
}
