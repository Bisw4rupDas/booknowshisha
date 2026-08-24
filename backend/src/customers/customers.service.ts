import {
  Injectable,
  NotFoundException,
  ConflictException,
  Logger,
} from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { UpdateCustomerProfileDto } from './dto/update-customer-profile.dto';

@Injectable()
export class CustomersService {
  private readonly logger = new Logger(CustomersService.name);

  constructor(private readonly prisma: PrismaService) {}

  /**
   * Get current authenticated customer profile
   */
  async getProfile(userId: string) {
    const customer = await this.prisma.customer.findUnique({
      where: { userId },
      include: {
        user: {
          select: {
            id: true,
            email: true,
            role: true,
            isVerified: true,
            createdAt: true,
          },
        },
        _count: {
          select: {
            bookings: true,
            rentals: true,
            orders: true,
          },
        },
      },
    });

    if (!customer) {
      throw new NotFoundException('Customer profile not found for this account.');
    }

    return customer;
  }

  /**
   * Update customer profile details
   */
  async updateProfile(userId: string, dto: UpdateCustomerProfileDto) {
    const customer = await this.prisma.customer.findUnique({
      where: { userId },
    });

    if (!customer) {
      throw new NotFoundException('Customer profile not found.');
    }

    // If phone is changing, ensure uniqueness
    if (dto.phone && dto.phone !== customer.phone) {
      const existingPhone = await this.prisma.customer.findUnique({
        where: { phone: dto.phone },
      });
      if (existingPhone && existingPhone.id !== customer.id) {
        throw new ConflictException('Phone number is already associated with another customer account.');
      }
    }

    return this.prisma.customer.update({
      where: { id: customer.id },
      data: dto,
    });
  }

  /**
   * List customers for admin view
   */
  async findAll(search?: string, page = 1, limit = 20) {
    const skip = (page - 1) * limit;

    const where: any = {};
    if (search) {
      where.OR = [
        { firstName: { contains: search, mode: 'insensitive' } },
        { lastName: { contains: search, mode: 'insensitive' } },
        { phone: { contains: search } },
        { user: { email: { contains: search, mode: 'insensitive' } } },
      ];
    }

    const [total, items] = await Promise.all([
      this.prisma.customer.count({ where }),
      this.prisma.customer.findMany({
        where,
        skip,
        take: limit,
        orderBy: { createdAt: 'desc' },
        include: {
          user: {
            select: {
              email: true,
              role: true,
              isVerified: true,
            },
          },
        },
      }),
    ]);

    return {
      items,
      meta: {
        total,
        page,
        limit,
        totalPages: Math.ceil(total / limit),
      },
    };
  }

  /**
   * Get single customer by ID
   */
  async findById(id: string) {
    const customer = await this.prisma.customer.findUnique({
      where: { id },
      include: {
        user: {
          select: { email: true, isVerified: true, createdAt: true },
        },
        bookings: {
          take: 5,
          orderBy: { createdAt: 'desc' },
        },
        rentals: {
          take: 5,
          orderBy: { createdAt: 'desc' },
        },
      },
    });

    if (!customer) {
      throw new NotFoundException(`Customer with ID ${id} not found.`);
    }

    return customer;
  }
}
