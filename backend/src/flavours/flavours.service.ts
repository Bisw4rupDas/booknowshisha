import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class FlavoursService {
  constructor(private readonly prisma: PrismaService) {}

  async findAll(categoryId?: string) {
    return this.prisma.flavour.findMany({
      where: {
        isActive: true,
        ...(categoryId ? { categoryId } : {}),
      },
      include: {
        category: true,
        stock: true,
      },
      orderBy: { name: 'asc' },
    });
  }

  async findCategories() {
    return this.prisma.flavourCategory.findMany({
      include: {
        flavours: {
          where: { isActive: true },
          include: {
            stock: true,
          },
        },
      },
      orderBy: { name: 'asc' },
    });
  }
}
