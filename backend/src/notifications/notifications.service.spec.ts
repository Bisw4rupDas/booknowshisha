import { Test, TestingModule } from '@nestjs/testing';
import { NotificationsService } from './notifications.service';
import { PrismaService } from '../prisma/prisma.service';
import { NotFoundException, ForbiddenException } from '@nestjs/common';

describe('NotificationsService', () => {
  let service: NotificationsService;
  let prisma: PrismaService;

  const mockUser = {
    id: 'user-uuid-1',
    email: 'customer@shisharent.com',
  };

  const mockNotification = {
    id: 'notif-uuid-1',
    userId: 'user-uuid-1',
    title: 'Rental Booking Confirmed',
    message: 'Your hookah rental setup is scheduled for delivery today.',
    type: 'RENTAL',
    isRead: false,
  };

  const mockPrismaService = {
    user: {
      findUnique: jest.fn(),
    },
    notification: {
      create: jest.fn(),
      findMany: jest.fn(),
      findUnique: jest.fn(),
      update: jest.fn(),
      updateMany: jest.fn(),
    },
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        NotificationsService,
        { provide: PrismaService, useValue: mockPrismaService },
      ],
    }).compile();

    service = module.get<NotificationsService>(NotificationsService);
    prisma = module.get<PrismaService>(PrismaService);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  describe('sendNotification', () => {
    it('should create and dispatch notification', async () => {
      mockPrismaService.user.findUnique.mockResolvedValue(mockUser);
      mockPrismaService.notification.create.mockResolvedValue(mockNotification);

      const result = await service.sendNotification({
        userId: 'user-uuid-1',
        title: 'Rental Booking Confirmed',
        message: 'Your hookah rental setup is scheduled for delivery today.',
      });

      expect(result).toBeDefined();
      expect(result.title).toBe('Rental Booking Confirmed');
    });

    it('should throw NotFoundException if user does not exist', async () => {
      mockPrismaService.user.findUnique.mockResolvedValue(null);

      await expect(
        service.sendNotification({
          userId: 'non-existent',
          title: 'Test',
          message: 'Test message',
        }),
      ).rejects.toThrow(NotFoundException);
    });
  });

  describe('markAsRead', () => {
    it('should mark user notification as read', async () => {
      mockPrismaService.notification.findUnique.mockResolvedValue(mockNotification);
      mockPrismaService.notification.update.mockResolvedValue({
        ...mockNotification,
        isRead: true,
      });

      const result = await service.markAsRead('notif-uuid-1', 'user-uuid-1');
      expect(result.isRead).toBe(true);
    });

    it('should throw ForbiddenException if user does not own the notification', async () => {
      mockPrismaService.notification.findUnique.mockResolvedValue(mockNotification);

      await expect(
        service.markAsRead('notif-uuid-1', 'other-user-uuid'),
      ).rejects.toThrow(ForbiddenException);
    });
  });
});
