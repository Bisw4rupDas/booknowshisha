import { Injectable, OnModuleInit, OnModuleDestroy, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import Redis from 'ioredis';

@Injectable()
export class RedisService implements OnModuleInit, OnModuleDestroy {
  private readonly logger = new Logger(RedisService.name);
  private client!: Redis;

  constructor(private readonly configService: ConfigService) {}

  onModuleInit() {
    const redisUrl = this.configService.get<string>('REDIS_URL', 'redis://localhost:6379');
    this.client = new Redis(redisUrl, {
      maxRetriesPerRequest: 3,
      retryStrategy: (times) => Math.min(times * 100, 3000),
    });

    this.client.on('connect', () => {
      this.logger.log('Connected to Redis server');
    });

    this.client.on('error', (err) => {
      this.logger.error(`Redis connection error: ${err.message}`);
    });
  }

  onModuleDestroy() {
    this.client.disconnect();
  }

  getClient(): Redis {
    return this.client;
  }

  /**
   * Acquire a distributed lock with TTL (in milliseconds)
   * Locking Strategy:
   * 1. Uses SET key token NX PX ttlMs to atomically acquire exclusive lock if not already set.
   * 2. Returns a unique lockIdentifier (nonce) needed to release the lock.
   * 3. Prevents stale locks via auto-expiration TTL.
   */
  async acquireLock(key: string, ttlMs = 10000): Promise<string | null> {
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
   * Ensures that a process only releases its own lock and never deletes a lock
   * acquired by another process after TTL expiration.
   */
  async releaseLock(key: string, identifier: string): Promise<boolean> {
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
    try {
      return await this.client.get(key);
    } catch (err: unknown) {
      const errorMsg = err instanceof Error ? err.message : String(err);
      this.logger.error(`Redis GET error for key "${key}": ${errorMsg}`);
      return null;
    }
  }

  async set(key: string, value: string, ttlSeconds?: number): Promise<'OK' | null> {
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
    try {
      return await this.client.del(key);
    } catch (err: unknown) {
      const errorMsg = err instanceof Error ? err.message : String(err);
      this.logger.error(`Redis DEL error for key "${key}": ${errorMsg}`);
      return 0;
    }
  }
}
