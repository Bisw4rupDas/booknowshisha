import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class PackagesService {
  constructor(private readonly prisma: PrismaService) {}

  async findAll() {
    return this.prisma.package.findMany({
      where: { isActive: true },
      include: {
        items: {
          include: {
            hookahModel: true,
          },
        },
      },
      orderBy: { price: 'asc' },
    });
  }

  async findBySlug(slug: string) {
    const pkg = await this.prisma.package.findUnique({
      where: { slug },
      include: {
        items: {
          include: {
            hookahModel: true,
          },
        },
      },
    });

    if (!pkg) {
      throw new NotFoundException(`Rental package with slug '${slug}' not found`);
    }

    return pkg;
  }

  async findOne(id: string) {
    const pkg = await this.prisma.package.findUnique({
      where: { id },
      include: {
        items: {
          include: {
            hookahModel: true,
          },
        },
      },
    });

    if (!pkg) {
      throw new NotFoundException(`Rental package with ID '${id}' not found`);
    }

    return pkg;
  }
}
