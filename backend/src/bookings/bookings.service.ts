import {
  Injectable,
  NotFoundException,
  BadRequestException,
  ConflictException,
  Logger,
  Optional,
} from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { RedisService } from '../common/redis/redis.service';
import { CreateBookingDto } from './dto/create-booking.dto';
import { BookingStatus, RentalStatus, HookahInventoryStatus } from '@prisma/client';
import { PinServiceabilityService } from '../delivery/serviceability/pin-serviceability.service';
import { NotificationsService } from '../notifications/notifications.service';

@Injectable()
export class BookingsService {
  private readonly logger = new Logger(BookingsService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly redis: RedisService,
    private readonly pinServiceability: PinServiceabilityService,
    @Optional() private readonly notificationsService?: NotificationsService,
  ) {}

  async createBooking(userId: string, dto: CreateBookingDto) {
    // ------------------------------------------------------------------------
    // 0. MANDATORY SERVER-SIDE 3-DISTRICT SERVICEABILITY ENFORCEMENT
    // ------------------------------------------------------------------------
    // The backend is the sole authority. Customer-provided city/district
    // is never trusted over the resolved district.
    const serviceability = this.pinServiceability.checkPinServiceability(dto.postalCode);
    if (!serviceability.deliverable) {
      this.logger.warn(
        `[Security] Rejected booking attempt for unserviceable PIN: ${dto.postalCode} (Resolved District: ${serviceability.district || 'Unknown'})`,
      );
      throw new BadRequestException(serviceability.message);
    }

    // 1. Resolve Customer Record
    let customer: {
      id: string;
      userId?: string | null;
      firstName?: string;
      lastName?: string;
    } | null = null;
    let effectiveUserId: string | null = userId;

    if (userId && userId !== 'bridge-system-user') {
      const user = await this.prisma.user.findUnique({
        where: { id: userId },
        include: { customer: true },
      });
      if (user && user.customer) {
        customer = user.customer;
      }
    }

    // If customer is still not resolved (Bridge request or new customer from checkout)
    if (!customer) {
      const customerEmail =
        dto.customerEmail ||
        (userId !== 'bridge-system-user'
          ? `${userId}@booknowshisha.local`
          : `guest_${Date.now()}@booknowshisha.local`);
      const customerPhone =
        dto.customerPhone || `+9198${Math.floor(10000000 + Math.random() * 90000000)}`;
      const fullName = dto.customerName || 'Customer';
      const nameParts = fullName.trim().split(' ');
      const firstName = nameParts[0] || 'Customer';
      const lastName = nameParts.slice(1).join(' ') || 'User';

      // Check existing customer by email or phone
      const existingUser = await this.prisma.user.findFirst({
        where: { OR: [{ email: customerEmail }, { customer: { phone: customerPhone } }] },
        include: { customer: true },
      });

      if (existingUser && existingUser.customer) {
        customer = existingUser.customer;
        effectiveUserId = existingUser.id;
      } else {
        const newUser = await this.prisma.user.create({
          data: {
            email: customerEmail,
            role: 'CUSTOMER',
            isVerified: true,
            customer: {
              create: {
                firstName,
                lastName,
                phone: customerPhone,
                addressLine1: dto.deliveryAddress,
                city: serviceability.district || 'Kolkata',
                postalCode: dto.postalCode,
              },
            },
          },
          include: { customer: true },
        });
        customer = newUser.customer!;
        effectiveUserId = newUser.id;
      }
    }

    if (!customer) {
      throw new BadRequestException('Unable to resolve or create customer record for booking');
    }

    // 2. Validate Rental Start Date
    const startDate = new Date(dto.rentalStart);
    if (isNaN(startDate.getTime())) {
      throw new BadRequestException('Invalid rental start date format');
    }
    const fiveMinutesAgo = new Date(Date.now() - 5 * 60 * 1000);
    if (startDate < fiveMinutesAgo) {
      throw new BadRequestException('Rental start date cannot be in the past');
    }

    // 3. Fetch Package & Items (support lookup by ID or slug)
    let pkg = await this.prisma.package.findFirst({
      where: {
        OR: [{ id: dto.packageId }, { slug: dto.packageId }],
        isActive: true,
      },
      include: {
        items: {
          include: { hookahModel: true },
        },
      },
    });

    if (!pkg) {
      pkg = await this.prisma.package.findFirst({
        where: { isActive: true },
        include: {
          items: {
            include: { hookahModel: true },
          },
        },
      });
    }

    if (!pkg || !pkg.isActive) {
      throw new NotFoundException('Selected rental package is not available');
    }

    if (!dto.flavourIds || dto.flavourIds.length === 0) {
      throw new BadRequestException('At least one flavour must be selected');
    }

    if (dto.flavourIds.length > pkg.maxFlavours) {
      throw new BadRequestException(
        `Package '${pkg.name}' allows a maximum of ${pkg.maxFlavours} flavours. You selected ${dto.flavourIds.length}.`,
      );
    }

    // 4. Validate Flavours and Stock (support lookup by ID or slug)
    const selectedFlavours = await this.prisma.flavour.findMany({
      where: {
        OR: [{ id: { in: dto.flavourIds } }, { slug: { in: dto.flavourIds } }],
        isActive: true,
      },
      include: { stock: true },
    });

    if (selectedFlavours.length !== dto.flavourIds.length) {
      throw new BadRequestException('One or more selected flavours are invalid or inactive');
    }

    for (const flavour of selectedFlavours) {
      if (!flavour.stock || flavour.stock.quantityUnits <= 0) {
        throw new ConflictException(`Flavour '${flavour.name}' is currently out of stock`);
      }
    }

    // 5. Resolve Hookah Model
    let hookahModel = null;
    if (dto.hookahModelId) {
      hookahModel = await this.prisma.hookahModel.findFirst({
        where: {
          OR: [{ id: dto.hookahModelId }, { slug: dto.hookahModelId }],
          isActive: true,
        },
      });
    }

    if (!hookahModel && pkg.items.length > 0) {
      hookahModel = pkg.items[0].hookahModel;
    }

    if (!hookahModel) {
      hookahModel = await this.prisma.hookahModel.findFirst({
        where: { isActive: true },
      });
    }

    if (!hookahModel || !hookahModel.isActive) {
      throw new NotFoundException('Selected hookah model is not available');
    }

    // 6. Validate Delivery Slot & Zone
    let slot: {
      id: string;
      startTime: string;
      endTime: string;
      maxCapacity: number;
      isActive: boolean;
      zone?: { id: string; name: string; baseFee: any; isActive: boolean } | null;
    } | null = null;

    if (dto.deliverySlotId && !dto.deliverySlotId.includes(':')) {
      slot = await this.prisma.deliverySlot.findFirst({
        where: { id: dto.deliverySlotId },
        include: { zone: true },
      });
    }

    if (!slot) {
      // Find matching zone for postalCode or district
      const zone = await this.prisma.deliveryZone.findFirst({
        where: {
          isActive: true,
          OR: [
            { postalCodes: { some: { postalCode: dto.postalCode } } },
            { name: { contains: serviceability.district || 'Kolkata' } },
          ],
        },
        include: { slots: { where: { isActive: true } } },
      });

      if (zone) {
        const slots = (zone as any).slots || [];
        if (slots.length > 0) {
          if (dto.deliverySlotId && dto.deliverySlotId.includes('-')) {
            const startTime = dto.deliverySlotId.split('-')[0]?.trim();
            const endTime = dto.deliverySlotId.split('-')[1]?.trim();
            const matched = slots.find(
              (s: any) => s.startTime === startTime && s.endTime === endTime,
            );
            slot = matched ? { ...matched, zone } : { ...slots[0], zone };
          } else {
            slot = { ...slots[0], zone };
          }
        }
      }

      if (!slot) {
        const fallbackSlot = await this.prisma.deliverySlot.findFirst({
          where: { isActive: true },
          include: { zone: true },
        });
        if (fallbackSlot) {
          slot = fallbackSlot;
        }
      }
    }

    if (!slot) {
      throw new BadRequestException(`Delivery slot could not be allocated for PIN ${dto.postalCode}`);
    }

    if (!slot.isActive) {
      throw new NotFoundException('Selected delivery time slot is not active');
    }

    // Check slot capacity for the specified date
    const dayStart = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
    const dayEnd = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + 1);

    const slotBookingsCount = await this.prisma.booking.count({
      where: {
        deliverySlotId: slot.id,
        rentalStart: { gte: dayStart, lt: dayEnd },
        status: { notIn: [BookingStatus.CANCELLED, BookingStatus.EXPIRED] },
      },
    });

    if (slotBookingsCount >= slot.maxCapacity) {
      throw new ConflictException(
        `Delivery slot '${slot.startTime} - ${slot.endTime}' on ${startDate.toISOString().slice(0, 10)} is fully booked. Please select another slot.`,
      );
    }

    // 7. Calculate End Date
    const endDate = new Date(startDate.getTime() + pkg.durationHrs * 60 * 60 * 1000);

    // 8. Distributed Lock on Model
    const lockKey = `lock:hookah_model:${hookahModel.id}`;
    const lockId = await this.redis.acquireLock(lockKey, 10000);

    if (!lockId) {
      throw new ConflictException(
        'System is processing another reservation for this hookah model. Please retry in a moment.',
      );
    }

    try {
      // 9. Find an AVAILABLE physical inventory unit for this model
      const availableUnit = await this.prisma.hookahInventory.findFirst({
        where: {
          hookahModelId: hookahModel.id,
          status: HookahInventoryStatus.AVAILABLE,
        },
      });

      if (!availableUnit) {
        throw new ConflictException(
          `All physical units of '${hookahModel.name}' are currently rented out for the requested period.`,
        );
      }

      const packagePrice = Number(pkg.price);
      const deliveryFee = slot.zone ? Number(slot.zone.baseFee) : (serviceability.baseDeliveryFee || 150);
      const depositFee = Number((hookahModel as any)?.depositFee || 2000.0);

      // Hookah Base Option Calculation
      const basePrices: Record<string, number> = {
        standard: 0,
        ice: 100,
        milk: 150,
        both: 200,
        ice_milk: 200,
      };
      const baseLabels: Record<string, string> = {
        standard: 'Standard Base (Included)',
        ice: 'Ice Base (+₹100)',
        milk: 'Milk Base (+₹150)',
        both: 'Ice + Milk Base Combined (+₹200)',
        ice_milk: 'Ice + Milk Base Combined (+₹200)',
      };
      const requestedBase = (dto.hookahBase || 'standard').toLowerCase();
      const baseFee = basePrices[requestedBase] !== undefined ? basePrices[requestedBase] : 0;
      const baseLabel = baseLabels[requestedBase] || 'Standard Base (Included)';

      const totalAmount = packagePrice + deliveryFee + baseFee;

      const randomSuffix = Math.floor(100000 + Math.random() * 900000);
      const bookingNumber = `SR-BK-${Date.now().toString().slice(-4)}${randomSuffix}`;
      const rentalNumber = `SR-RN-${Date.now().toString().slice(-4)}${randomSuffix}`;

      // 10. Database Transaction
      const result = await this.prisma.$transaction(async (tx) => {
        // Reserve physical inventory unit
        const unitUpdate = await tx.hookahInventory.updateMany({
          where: {
            id: availableUnit.id,
            status: HookahInventoryStatus.AVAILABLE,
          },
          data: { status: HookahInventoryStatus.RESERVED },
        });

        if (unitUpdate.count === 0) {
          throw new ConflictException(
            'Selected physical unit was just reserved by another request.',
          );
        }

        // Create Booking
        const booking = await tx.booking.create({
          data: {
            bookingNumber,
            customerId: customer!.id,
            packageId: pkg.id,
            status: BookingStatus.CONFIRMED,
            rentalStart: startDate,
            rentalEnd: endDate,
            durationHours: pkg.durationHrs,
            totalAmount,
            depositAmount: depositFee,
            deliverySlotId: slot!.id,
            deliveryAddress: dto.deliveryAddress,
            postalCode: dto.postalCode,
            notes: dto.notes ? `${dto.notes} | Base: ${baseLabel} | District: ${serviceability.district}` : `Base: ${baseLabel} | District: ${serviceability.district}`,
          },
        });

        // Create Rental record
        const rental = await tx.rental.create({
          data: {
            rentalNumber,
            bookingId: booking.id,
            customerId: customer!.id,
            packageId: pkg.id,
            status: RentalStatus.RESERVED,
            startDate,
            endDate,
            items: {
              create: [
                {
                  hookahInventoryId: availableUnit.id,
                  notes: `Assigned unit: ${availableUnit.serialNumber}`,
                },
                ...selectedFlavours.map((f) => ({
                  flavourId: f.id,
                  notes: 'Rental package flavour head',
                })),
              ],
            },
            securityDeposit: {
              create: {
                amount: depositFee,
              },
            },
          },
        });

        // Create Delivery dispatch record
        const deliveryNumber = `SR-DL-${Date.now().toString().slice(-4)}${randomSuffix}`;
        await tx.delivery.create({
          data: {
            deliveryNumber,
            rentalId: rental.id,
            slotId: slot!.id,
            scheduledDate: startDate,
            deliveryAddress: dto.deliveryAddress,
            notes: `Delivery for Booking #${bookingNumber} | District: ${serviceability.district}`,
          },
        });

        // Deduct flavour stock
        for (const f of selectedFlavours) {
          const stockUpdate = await tx.flavourStock.updateMany({
            where: { flavourId: f.id, quantityUnits: { gt: 0 } },
            data: { quantityUnits: { decrement: 1 } },
          });

          if (stockUpdate.count === 0) {
            throw new ConflictException('A selected flavour ran out of stock during checkout.');
          }
        }

        // Create Order and Payment if paymentMethod or wpOrderId is provided
        let order = null;
        let payment = null;
        let upiIntentString: string | null = null;
        let upiQrPayload: string | null = null;

        if (dto.paymentMethod || dto.wpOrderId) {
          const orderNumber = `SR-ORD-${Date.now().toString().slice(-4)}${randomSuffix.toString().slice(0, 4)}`;
          order = await tx.order.create({
            data: {
              orderNumber,
              customerId: customer!.id,
              bookingId: booking.id,
              wpOrderId: dto.wpOrderId,
              status: dto.paymentMethod === 'COD' ? 'CONFIRMED' : 'PENDING',
              subtotal: packagePrice,
              deliveryFee,
              deposit: depositFee,
              totalAmount,
              notes: dto.notes ? `${dto.notes} | District: ${serviceability.district}` : `District: ${serviceability.district}`,
              items: {
                create: [
                  {
                    name: `Rental: ${pkg.name}`,
                    quantity: 1,
                    unitPrice: totalAmount,
                    totalPrice: totalAmount,
                  },
                ],
              },
            },
          });

          if (dto.paymentMethod) {
            const paymentNumber = `SR-PAY-${Date.now().toString().slice(-4)}${randomSuffix.toString().slice(0, 4)}`;
            payment = await tx.payment.create({
              data: {
                paymentNumber,
                orderId: order.id,
                method: dto.paymentMethod === 'COD' ? 'COD' : 'UPI',
                status: 'PENDING',
                amount: totalAmount,
                currency: 'INR',
              },
            });

            if (dto.paymentMethod === 'UPI') {
              const upiVpa = 'pay.shisharent@upi';
              const txnRef = `TXN${Date.now()}`;
              upiIntentString = `upi://pay?pa=${upiVpa}&pn=ShishaRent&mc=5999&tid=${txnRef}&tr=${txnRef}&tn=ShishaRent+Rental+Order+${orderNumber}&am=${totalAmount.toFixed(2)}&cu=INR`;
              upiQrPayload = upiIntentString;
            }
          }
        }

        // Audit Log
        if (effectiveUserId && effectiveUserId !== 'bridge-system-user') {
          await tx.auditLog.create({
            data: {
              userId: effectiveUserId,
              action: 'BOOKING_CREATED',
              entity: 'Booking',
              entityId: booking.id,
              details: {
                bookingNumber,
                rentalNumber,
                totalAmount,
                hookahModel: hookahModel.name,
                serialNumber: availableUnit.serialNumber,
                district: serviceability.district,
                postalCode: dto.postalCode,
              },
            },
          });
        }

        return {
          booking,
          rental,
          order,
          payment,
          upiIntent: upiIntentString,
          upiQrPayload,
          assignedUnit: availableUnit.serialNumber,
          hookahModel: hookahModel.name,
          package: pkg.name,
          district: serviceability.district,
          state: serviceability.state,
          breakdown: {
            packagePrice,
            deliveryFee,
            depositFee,
            baseFee,
            hookahBase: requestedBase,
            hookahBaseLabel: baseLabel,
            totalToPay: totalAmount,
          },
        };
      });

      this.logger.log(
        `✓ Booking created successfully: ${bookingNumber} in District: ${serviceability.district} (PIN: ${dto.postalCode})`,
      );

      // Dispatch Admin Notification for new order
      try {
        const adminUser = await this.prisma.user.findFirst({
          where: { role: 'ADMIN' },
        });
        if (adminUser && this.notificationsService) {
          await this.notificationsService.sendNotification({
            userId: adminUser.id,
            title: `NEW SHISHARENT ORDER #${result.order?.orderNumber || bookingNumber}`,
            message: `Order ID: ${result.order?.orderNumber || bookingNumber}\nCustomer: ${dto.customerName || customer.firstName} (${dto.customerPhone || 'N/A'})\nDelivery Address: ${dto.deliveryAddress}\nPIN: ${dto.postalCode}\nResolved District: ${serviceability.district}\nRental Date: ${startDate.toISOString().slice(0, 10)}\nDelivery Slot: ${slot.startTime} - ${slot.endTime}\nPackage: ${pkg.name}\nAssigned Unit: ${result.assignedUnit}\nTotal: ₹${totalAmount}\nPayment Method: ${dto.paymentMethod || 'UPI'}\nPayment Status: ${dto.paymentMethod === 'COD' ? 'Pending COD Collection' : 'Pending Verification'}`,
            type: 'ORDER_CREATED',
          });
        }
      } catch (notifErr) {
        this.logger.debug(`Notification dispatch notice: ${(notifErr as Error).message}`);
      }

      return result;
    } finally {
      await this.redis.releaseLock(lockKey, lockId);
    }
  }

  async getCustomerBookings(userId: string) {
    const user = await this.prisma.user.findUnique({
      where: { id: userId },
      include: { customer: true },
    });

    if (!user || !user.customer) {
      throw new BadRequestException('Customer profile not found');
    }

    return this.prisma.booking.findMany({
      where: { customerId: user.customer.id },
      include: {
        package: true,
        deliverySlot: { include: { zone: true } },
        rental: {
          include: {
            items: {
              include: {
                hookahInventory: { include: { hookahModel: true } },
                flavour: true,
              },
            },
            securityDeposit: true,
          },
        },
      },
      orderBy: { createdAt: 'desc' },
    });
  }

  async findOne(id: string) {
    const booking = await this.prisma.booking.findUnique({
      where: { id },
      include: {
        customer: true,
        package: true,
        deliverySlot: { include: { zone: true } },
        rental: {
          include: {
            items: {
              include: {
                hookahInventory: { include: { hookahModel: true } },
                flavour: true,
              },
            },
            deliveries: true,
            securityDeposit: true,
            inspection: true,
          },
        },
      },
    });

    if (!booking) {
      throw new NotFoundException(`Booking with ID '${id}' not found`);
    }

    return booking;
  }
}
