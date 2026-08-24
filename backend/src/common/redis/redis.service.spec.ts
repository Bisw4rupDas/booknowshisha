/* eslint-disable @typescript-eslint/no-explicit-any */
import { Test, TestingModule } from '@nestjs/testing';
import { ConfigService } from '@nestjs/config';
import { RedisService } from './redis.service';

describe('RedisService', () => {
  let service: RedisService;
  let mockRedisClient: any;

  beforeEach(async () => {
    mockRedisClient = {
      set: jest.fn(),
      get: jest.fn(),
      del: jest.fn(),
      eval: jest.fn(),
      disconnect: jest.fn(),
      on: jest.fn(),
    };

    const module: TestingModule = await Test.createTestingModule({
      providers: [
        RedisService,
        {
          provide: ConfigService,
          useValue: {
            get: jest.fn().mockReturnValue('redis://localhost:6379'),
          },
        },
      ],
    }).compile();

    service = module.get<RedisService>(RedisService);
    (service as any).client = mockRedisClient;
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  describe('acquireLock', () => {
    it('should successfully acquire lock and return identifier token', async () => {
      mockRedisClient.set.mockResolvedValue('OK');

      const token = await service.acquireLock('hookah-123', 5000);
      expect(token).toBeDefined();
      expect(typeof token).toBe('string');
      expect(mockRedisClient.set).toHaveBeenCalledWith(
        'lock:hookah-123',
        expect.any(String),
        'PX',
        5000,
        'NX',
      );
    });

    it('should return null when lock cannot be acquired (already held)', async () => {
      mockRedisClient.set.mockResolvedValue(null);

      const token = await service.acquireLock('hookah-123', 5000);
      expect(token).toBeNull();
    });

    it('should handle Redis error gracefully and return null', async () => {
      mockRedisClient.set.mockRejectedValue(new Error('Connection lost'));

      const token = await service.acquireLock('hookah-123', 5000);
      expect(token).toBeNull();
    });
  });

  describe('releaseLock', () => {
    it('should release lock using Lua atomic script when token matches', async () => {
      mockRedisClient.eval.mockResolvedValue(1);

      const released = await service.releaseLock('hookah-123', 'valid-token-123');
      expect(released).toBe(true);
      expect(mockRedisClient.eval).toHaveBeenCalledWith(
        expect.stringContaining('if redis.call("get", KEYS[1]) == ARGV[1]'),
        1,
        'lock:hookah-123',
        'valid-token-123',
      );
    });

    it('should fail release when token does not match or expired', async () => {
      mockRedisClient.eval.mockResolvedValue(0);

      const released = await service.releaseLock('hookah-123', 'wrong-token');
      expect(released).toBe(false);
    });
  });

  describe('isLocked', () => {
    it('should return true if lock exists', async () => {
      mockRedisClient.get.mockResolvedValue('active-token');
      const locked = await service.isLocked('hookah-123');
      expect(locked).toBe(true);
    });

    it('should return false if lock does not exist', async () => {
      mockRedisClient.get.mockResolvedValue(null);
      const locked = await service.isLocked('hookah-123');
      expect(locked).toBe(false);
    });
  });
});
