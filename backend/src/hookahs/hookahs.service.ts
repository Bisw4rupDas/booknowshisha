import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class HookahsService {
  constructor(private readonly prisma: PrismaService) {}

  async findAll() {
    return this.prisma.hookahModel.findMany({
      where: { isActive: true },
      include: {
        inventory: {
          select: {
            id: true,
            status: true,
            condition: true,
          },
        },
      },
      orderBy: { basePrice: 'asc' },
    });
  }

  async findBySlug(slug: string) {
    const hookah = await this.prisma.hookahModel.findUnique({
      where: { slug },
      include: {
        inventory: {
          select: {
            id: true,
            status: true,
            condition: true,
          },
        },
      },
    });

    if (!hookah) {
      throw new NotFoundException(`Hookah model with slug '${slug}' not found`);
    }

    return hookah;
  }

  async findOne(id: string) {
    const hookah = await this.prisma.hookahModel.findUnique({
      where: { id },
      include: {
        inventory: true,
      },
    });

    if (!hookah) {
      throw new NotFoundException(`Hookah model with ID '${id}' not found`);
    }

    return hookah;
  }
}
