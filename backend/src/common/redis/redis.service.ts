import { Injectable, OnModuleInit, OnModuleDestroy, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import Redis from 'ioredis';

@Injectable()
export class RedisService implements OnModuleInit, OnModuleDestroy {
  private readonly logger = new Logger(RedisService.name);
  private client: Redis | null = null;

  constructor(private readonly configService: ConfigService) {}

  onModuleInit() {
    const redisUrl = this.configService.get<string>('REDIS_URL');

    if (!redisUrl || redisUrl === 'disabled' || redisUrl === 'none' || redisUrl.trim() === '') {
      this.logger.log('Redis is disabled or REDIS_URL is not set. Operating in pure database-backed mode.');
      this.client = null;
      return;
    }

    try {
      this.client = new Redis(redisUrl, {
        maxRetriesPerRequest: 2,
        retryStrategy: (times) => {
          if (times > 3) {
            return null; // Stop retrying after 3 attempts if Redis is down
          }
          return Math.min(times * 200, 1000);
        },
        lazyConnect: true,
        connectTimeout: 3000,
        enableOfflineQueue: false,
      });

      this.client.on('connect', () => {
        this.logger.log('Connected to Redis server');
      });

      this.client.on('error', (err) => {
        this.logger.debug(`Redis connection notice: ${err.message}`);
      });

      this.client.connect().catch((err) => {
        this.logger.warn(`Redis initial connect failed: ${err.message}. Fallback to database concurrency active.`);
      });
    } catch (err: unknown) {
      this.client = null;
      const errorMsg = err instanceof Error ? err.message : String(err);
      this.logger.warn(`Redis initialization skipped: ${errorMsg}`);
    }
  }

  onModuleDestroy() {
    if (this.client) {
      try {
        this.client.disconnect();
      } catch {
        // Safe tear down
      }
    }
  }

  getClient(): Redis | null {
    return this.client;
  }

  isAvailable(): boolean {
    return Boolean(this.client);
  }

  /**
   * Acquire a distributed lock with TTL (in milliseconds)
   * Locking Strategy:
   * 1. If Redis is available: Uses SET key token NX PX ttlMs for atomic exclusive lock.
   * 2. If Redis is unavailable/disabled: Returns a fallback identifier. Prisma $transaction
   *    with atomic row-level conditional updates provides ACID concurrency safety.
   */
  async acquireLock(key: string, ttlMs = 10000): Promise<string | null> {
    if (!this.client) {
      return `db-lock-${Date.now()}-${Math.random().toString(36).substring(2, 9)}`;
    }

    try {
      const lockIdentifier = `${Date.now()}-${Math.random().toString(36).substring(2, 9)}`;
      const lockKey = `lock:${key}`;
      const result = await this.client.set(lockKey, lockIdentifier, 'PX', ttlMs, 'NX');

      return result === 'OK' ? lockIdentifier : null;
    } catch (err: unknown) {
      const errorMsg = err instanceof Error ? err.message : String(err);
      this.logger.error(`Failed to acquire lock for key "${key}": ${errorMsg}`);
      return null;
    }
  }

  /**
   * Release a distributed lock safely using Lua script (atomic check-and-delete)
   */
  async releaseLock(key: string, identifier: string): Promise<boolean> {
    if (!this.client || identifier.startsWith('db-lock-')) {
      return true;
    }

    try {
      const lockKey = `lock:${key}`;
      const luaScript = `
        if redis.call("get", KEYS[1]) == ARGV[1] then
          return redis.call("del", KEYS[1])
        else
          return 0
        end
      `;

      const result = await this.client.eval(luaScript, 1, lockKey, identifier);
      return result === 1;
    } catch (err: unknown) {
      const errorMsg = err instanceof Error ? err.message : String(err);
      this.logger.error(`Failed to release lock for key "${key}": ${errorMsg}`);
      return false;
    }
  }

  /**
   * Check if a distributed lock is currently held
   */
  async isLocked(key: string): Promise<boolean> {
    if (!this.client) {
      return false;
    }

    try {
      const lockKey = `lock:${key}`;
      const val = await this.client.get(lockKey);
      return val !== null;
    } catch (err: unknown) {
      const errorMsg = err instanceof Error ? err.message : String(err);
      this.logger.error(`Failed to check lock status for key "${key}": ${errorMsg}`);
      return false;
    }
  }

  async get(key: string): Promise<string | null> {
    if (!this.client) {
      return null;
    }

    try {
      return await this.client.get(key);
    } catch (err: unknown) {
      const errorMsg = err instanceof Error ? err.message : String(err);
      this.logger.error(`Redis GET error for key "${key}": ${errorMsg}`);
      return null;
    }
  }

  async set(key: string, value: string, ttlSeconds?: number): Promise<'OK' | null> {
    if (!this.client) {
      return null;
    }

    try {
      if (ttlSeconds) {
        return await this.client.set(key, value, 'EX', ttlSeconds);
      }
      return await this.client.set(key, value);
    } catch (err: unknown) {
      const errorMsg = err instanceof Error ? err.message : String(err);
      this.logger.error(`Redis SET error for key "${key}": ${errorMsg}`);
      return null;
    }
  }

  async del(key: string): Promise<number> {
    if (!this.client) {
      return 0;
    }

    try {
      return await this.client.del(key);
    } catch (err: unknown) {
      const errorMsg = err instanceof Error ? err.message : String(err);
      this.logger.error(`Redis DEL error for key "${key}": ${errorMsg}`);
      return 0;
    }
  }

  async ttl(key: string): Promise<number> {
    if (!this.client) {
      return -2;
    }

    try {
      return await this.client.ttl(key);
    } catch (err: unknown) {
      const errorMsg = err instanceof Error ? err.message : String(err);
      this.logger.error(`Redis TTL error for key "${key}": ${errorMsg}`);
      return -2;
    }
  }
}
