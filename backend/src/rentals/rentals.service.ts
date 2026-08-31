import {
  Injectable,
  NotFoundException,
  BadRequestException,
  ForbiddenException,
  Logger,
} from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateRentalDto } from './dto/create-rental.dto';
import { UpdateRentalStatusDto } from './dto/update-rental-status.dto';
import { RentalFilterDto } from './dto/rental-filter.dto';
import {
  RentalStatus,
  UserRole,
  HookahInventoryStatus,
  Prisma,
} from '@prisma/client';
import { PinServiceabilityService } from '../delivery/serviceability/pin-serviceability.service';

@Injectable()
export class RentalsService {
  private readonly logger = new Logger(RentalsService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly pinServiceability: PinServiceabilityService,
  ) {}

  /**
   * Initialize a new rental from a confirmed booking
   */
  async createRental(dto: CreateRentalDto, user?: any) {
    const booking = await this.prisma.booking.findUnique({
      where: { id: dto.bookingId },
      include: {
        package: {
          include: {
            items: {
              include: { hookahModel: true },
            },
          },
        },
        customer: true,
        rental: true,
      },
    });

    if (!booking) {
      throw new NotFoundException(`Booking with ID ${dto.bookingId} not found.`);
    }

    if (booking.rental) {
      throw new BadRequestException(
        `Rental already initialized for this booking (Rental ID: ${booking.rental.id}).`,
      );
    }

    // Generate formatted unique rental number e.g. RNT-20260822-ABCD
    const dateStr = new Date().toISOString().slice(0, 10).replace(/-/g, '');
    const randomSuffix = Math.random().toString(36).substring(2, 6).toUpperCase();
    const rentalNumber = `RNT-${dateStr}-${randomSuffix}`;

    const customerId = dto.customerId || booking.customerId;

    // Execute in transaction
    const rental = await this.prisma.$transaction(async (tx) => {
      // 1. Create Rental Record
      const newRental = await tx.rental.create({
        data: {
          rentalNumber,
          bookingId: booking.id,
          customerId,
          packageId: booking.packageId,
          status: RentalStatus.RESERVED,
          startDate: booking.rentalStart,
          endDate: booking.rentalEnd,
        },
      });

      // 2. Assign Hookah Inventory Units if provided
      if (dto.hookahInventoryIds && dto.hookahInventoryIds.length > 0) {
        for (const unitId of dto.hookahInventoryIds) {
          const unit = await tx.hookahInventory.findUnique({ where: { id: unitId } });
          if (!unit) {
            throw new NotFoundException(`Hookah inventory unit ${unitId} not found.`);
          }
          if (unit.status !== HookahInventoryStatus.AVAILABLE) {
            throw new BadRequestException(
              `Unit ${unit.serialNumber} is currently not available (Status: ${unit.status}).`,
            );
          }

          // Create RentalItem
          await tx.rentalItem.create({
            data: {
              rentalId: newRental.id,
              hookahInventoryId: unit.id,
            },
          });

          // Reserve unit
          await tx.hookahInventory.update({
            where: { id: unit.id },
            data: { status: HookahInventoryStatus.RESERVED },
          });
        }
      }

      // 3. Assign Flavours if provided
      if (dto.flavourIds && dto.flavourIds.length > 0) {
        for (const flavourId of dto.flavourIds) {
          await tx.rentalItem.create({
            data: {
              rentalId: newRental.id,
              flavourId,
            },
          });
        }
      }

      // 4. Initialize Security Deposit Record
      if (booking.depositAmount && Number(booking.depositAmount) > 0) {
        await tx.securityDeposit.create({
          data: {
            rentalId: newRental.id,
            amount: booking.depositAmount,
          },
        });
      }

      // 5. Audit Log
      await tx.auditLog.create({
        data: {
          userId: user?.id || null,
          action: 'RENTAL_CREATED',
          entity: 'Rental',
          entityId: newRental.id,
          details: {
            rentalNumber,
            bookingId: booking.id,
            customerId,
          },
        },
      });

      return newRental;
    });

    this.logger.log(`Rental created successfully: ${rental.rentalNumber} (ID: ${rental.id})`);
    return this.findOne(rental.id, user);
  }

  /**
   * List rentals with filtering and pagination
   */
  async findAll(filter: RentalFilterDto, user?: any) {
    const { status, customerId, search, page = 1, limit = 10 } = filter;
    const skip = (page - 1) * limit;

    const where: Prisma.RentalWhereInput = {};

    // Customer role restriction
    if (user && user.role === UserRole.CUSTOMER) {
      const customer = await this.prisma.customer.findUnique({
        where: { userId: user.id },
      });
      if (customer) {
        where.customerId = customer.id;
      }
    } else if (customerId) {
      where.customerId = customerId;
    }

    if (status) {
      where.status = status;
    }

    if (search) {
      where.OR = [
        { rentalNumber: { contains: search } },
        {
          customer: {
            OR: [
              { firstName: { contains: search } },
              { lastName: { contains: search } },
              { phone: { contains: search } },
            ],
          },
        },
      ];
    }

    const [total, rawItems] = await Promise.all([
      this.prisma.rental.count({ where }),
      this.prisma.rental.findMany({
        where,
        skip,
        take: limit,
        orderBy: { createdAt: 'desc' },
        include: {
          customer: {
            select: {
              id: true,
              firstName: true,
              lastName: true,
              phone: true,
              city: true,
              postalCode: true,
            },
          },
          package: {
            select: {
              id: true,
              name: true,
              price: true,
              durationHrs: true,
            },
          },
          securityDeposit: true,
        },
      }),
    ]);

    const items = rawItems.map((rental) => {
      const pin = rental.customer?.postalCode || '';
      const serviceability = this.pinServiceability.checkPinServiceability(pin);
      return {
        ...rental,
        resolvedDistrict: serviceability.district || 'Unserviceable Area',
        isServiceable: serviceability.deliverable,
      };
    });

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
   * Retrieve single rental details with full relations
   */
  async findOne(id: string, user?: any) {
    const rental = await this.prisma.rental.findUnique({
      where: { id },
      include: {
        customer: true,
        package: {
          include: {
            items: {
              include: { hookahModel: true },
            },
          },
        },
        booking: {
          include: {
            deliverySlot: {
              include: { zone: true },
            },
          },
        },
        items: {
          include: {
            hookahInventory: {
              include: { hookahModel: true },
            },
            flavour: {
              include: { category: true },
            },
          },
        },
        deliveries: {
          include: {
            staff: true,
            slot: true,
          },
        },
        inspection: {
          include: {
            staff: true,
            damageReport: true,
          },
        },
        damageReports: true,
        securityDeposit: true,
      },
    });

    if (!rental) {
      throw new NotFoundException(`Rental with ID ${id} not found.`);
    }

    // Role safety check
    if (user && user.role === UserRole.CUSTOMER) {
      const customer = await this.prisma.customer.findUnique({
        where: { userId: user.id },
      });
      if (!customer || rental.customerId !== customer.id) {
        throw new ForbiddenException('You do not have permission to view this rental.');
      }
    }

    const pin = rental.customer?.postalCode || '';
    const serviceability = this.pinServiceability.checkPinServiceability(pin);

    return {
      ...rental,
      resolvedDistrict: serviceability.district || 'Unserviceable Area',
      isServiceable: serviceability.deliverable,
    };
  }

  /**
   * Transition rental status through state machine
   */
  async updateStatus(id: string, dto: UpdateRentalStatusDto, user?: any) {
    const rental = await this.prisma.rental.findUnique({
      where: { id },
      include: {
        items: true,
      },
    });

    if (!rental) {
      throw new NotFoundException(`Rental with ID ${id} not found.`);
    }

    const prevStatus = rental.status;
    const newStatus = dto.status;

    // Validate transition
    this.validateStatusTransition(prevStatus, newStatus);

    return this.prisma.$transaction(async (tx) => {
      const updateData: Prisma.RentalUpdateInput = {
        status: newStatus,
      };

      // Set actualReturn timestamp when marked returned
      if (newStatus === RentalStatus.RETURNED && !rental.actualReturn) {
        updateData.actualReturn = new Date();
      }

      // Update physical inventory units if applicable
      const inventoryIds = rental.items
        .map((i) => i.hookahInventoryId)
        .filter((i): i is string => Boolean(i));

      if (inventoryIds.length > 0) {
        if (newStatus === RentalStatus.DELIVERED || newStatus === RentalStatus.ACTIVE) {
          await tx.hookahInventory.updateMany({
            where: { id: { in: inventoryIds } },
            data: { status: HookahInventoryStatus.RENTED },
          });
        } else if (newStatus === RentalStatus.COMPLETED) {
          await tx.hookahInventory.updateMany({
            where: { id: { in: inventoryIds } },
            data: { status: HookahInventoryStatus.AVAILABLE },
          });
        } else if (newStatus === RentalStatus.CANCELLED) {
          await tx.hookahInventory.updateMany({
            where: { id: { in: inventoryIds } },
            data: { status: HookahInventoryStatus.AVAILABLE },
          });
        }
      }

      const updatedRental = await tx.rental.update({
        where: { id },
        data: updateData,
      });

      // Audit Log
      await tx.auditLog.create({
        data: {
          userId: user?.id || null,
          action: 'RENTAL_STATUS_UPDATE',
          entity: 'Rental',
          entityId: id,
          details: {
            from: prevStatus,
            to: newStatus,
            notes: dto.notes,
          },
        },
      });

      return updatedRental;
    });
  }

  /**
   * Cancel an active or pending rental
   */
  async cancelRental(id: string, user?: any, reason?: string) {
    const rental = await this.prisma.rental.findUnique({
      where: { id },
    });

    if (!rental) {
      throw new NotFoundException(`Rental with ID ${id} not found.`);
    }

    if (
      rental.status !== RentalStatus.RESERVED &&
      rental.status !== RentalStatus.PREPARING
    ) {
      throw new BadRequestException(
        `Cannot cancel rental in '${rental.status}' status. Only RESERVED or PREPARING rentals can be cancelled.`,
      );
    }

    return this.updateStatus(
      id,
      {
        status: RentalStatus.CANCELLED,
        notes: reason || 'Cancelled by user or administrator.',
      },
      user,
    );
  }

  /**
   * Customer / Staff triggers return ready
   */
  async requestReturn(id: string, user?: any) {
    const rental = await this.prisma.rental.findUnique({
      where: { id },
    });

    if (!rental) {
      throw new NotFoundException(`Rental with ID ${id} not found.`);
    }

    if (rental.status !== RentalStatus.ACTIVE && rental.status !== RentalStatus.DELIVERED) {
      throw new BadRequestException(
        `Cannot request return for rental in '${rental.status}' status. Must be ACTIVE or DELIVERED.`,
      );
    }

    return this.updateStatus(
      id,
      {
        status: RentalStatus.RETURN_PENDING,
        notes: 'Customer notified ready for return pickup.',
      },
      user,
    );
  }

  /**
   * State Machine transition validator
   */
  private validateStatusTransition(current: RentalStatus, target: RentalStatus) {
    if (current === target) {
      return;
    }

    // Terminal states cannot transition
    if (current === RentalStatus.COMPLETED || current === RentalStatus.CANCELLED) {
      throw new BadRequestException(
        `Cannot transition from terminal status '${current}' to '${target}'.`,
      );
    }

    const validTransitions: Record<RentalStatus, RentalStatus[]> = {
      [RentalStatus.RESERVED]: [RentalStatus.PREPARING, RentalStatus.CANCELLED],
      [RentalStatus.PREPARING]: [RentalStatus.OUT_FOR_DELIVERY, RentalStatus.CANCELLED],
      [RentalStatus.OUT_FOR_DELIVERY]: [RentalStatus.DELIVERED, RentalStatus.RESERVED],
      [RentalStatus.DELIVERED]: [RentalStatus.ACTIVE, RentalStatus.RETURN_PENDING],
      [RentalStatus.ACTIVE]: [RentalStatus.RETURN_PENDING, RentalStatus.DISPUTED],
      [RentalStatus.RETURN_PENDING]: [RentalStatus.RETURNED, RentalStatus.DISPUTED],
      [RentalStatus.RETURNED]: [RentalStatus.INSPECTED, RentalStatus.COMPLETED, RentalStatus.DISPUTED],
      [RentalStatus.INSPECTED]: [RentalStatus.COMPLETED, RentalStatus.DISPUTED],
      [RentalStatus.DISPUTED]: [RentalStatus.COMPLETED, RentalStatus.CANCELLED],
      [RentalStatus.COMPLETED]: [],
      [RentalStatus.CANCELLED]: [],
    };

    const allowed = validTransitions[current] || [];
    if (!allowed.includes(target)) {
      throw new BadRequestException(
        `Invalid status transition from '${current}' to '${target}'. Allowed transitions: ${allowed.join(', ') || 'None'}.`,
      );
    }
  }
}
