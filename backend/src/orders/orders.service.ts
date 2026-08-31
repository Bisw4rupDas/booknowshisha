import {
  Injectable,
  NotFoundException,
  BadRequestException,
  ForbiddenException,
  Logger,
} from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateOrderDto } from './dto/create-order.dto';
import { UpdateOrderStatusDto } from './dto/update-order-status.dto';
import { OrderFilterDto } from './dto/order-filter.dto';
import { WooCommerceOrderWebhookDto } from './dto/woocommerce-webhook.dto';
import { OrderStatus, UserRole, Prisma } from '@prisma/client';
import { PinServiceabilityService } from '../delivery/serviceability/pin-serviceability.service';

@Injectable()
export class OrdersService {
  private readonly logger = new Logger(OrdersService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly pinServiceability: PinServiceabilityService,
  ) {}

  /**
   * Create an Order from internal frontend / API
   */
  async create(createOrderDto: CreateOrderDto, user?: any) {
    const customer = await this.prisma.customer.findUnique({
      where: { id: createOrderDto.customerId },
    });

    if (!customer) {
      throw new NotFoundException(`Customer #${createOrderDto.customerId} not found.`);
    }

    // Role check if customer is attempting creation
    if (user && user.role === UserRole.CUSTOMER && customer.userId !== user.id) {
      throw new ForbiddenException('You cannot create orders for another customer.');
    }

    // Validate delivery PIN serviceability
    const postalCode = customer.postalCode || '';
    const serviceability = this.pinServiceability.checkPinServiceability(postalCode);
    if (!serviceability.deliverable) {
      throw new BadRequestException(serviceability.message);
    }

    const dateStr = new Date().toISOString().slice(0, 10).replace(/-/g, '');
    const random = Math.floor(1000 + Math.random() * 9000);
    const orderNumber = `ORD-${dateStr}-${random}`;

    let subtotal = 0;
    const itemsData = createOrderDto.items.map((item) => {
      const itemTotal = Number(item.unitPrice) * item.quantity;
      subtotal += itemTotal;
      return {
        wpProductId: item.wpProductId,
        name: item.name,
        quantity: item.quantity,
        unitPrice: item.unitPrice,
        totalPrice: itemTotal,
      };
    });

    const deliveryFee = Number(createOrderDto.deliveryFee || 0);
    const deposit = Number(createOrderDto.deposit || 0);
    const discount = Number(createOrderDto.discount || 0);
    const totalAmount = subtotal + deliveryFee + deposit - discount;

    const order = await this.prisma.order.create({
      data: {
        orderNumber,
        customerId: customer.id,
        bookingId: createOrderDto.bookingId,
        wpOrderId: createOrderDto.wpOrderId,
        status: OrderStatus.PENDING,
        subtotal,
        discount,
        deliveryFee,
        deposit,
        totalAmount,
        notes: createOrderDto.notes
          ? `${createOrderDto.notes} | District: ${serviceability.district}`
          : `District: ${serviceability.district}`,
        items: {
          create: itemsData,
        },
      },
      include: {
        items: true,
        customer: true,
        booking: true,
      },
    });

    this.logger.log(`Created internal Order #${order.orderNumber} for Customer #${customer.id}`);
    return this.findOne(order.id, user);
  }

  /**
   * Process webhook payload from WooCommerce with strict 3-district verification
   */
  async syncFromWooCommerce(dto: WooCommerceOrderWebhookDto) {
    const data = dto.orderData;
    const wpOrderId = Number(data.id);

    if (!wpOrderId) {
      throw new BadRequestException('Invalid WooCommerce order data: Missing order ID.');
    }

    const billing = data.billing || {};
    const shipping = data.shipping || {};
    const email = billing.email || `customer_${wpOrderId}@shisharent.local`;
    const phone = billing.phone || `+919800000000`;
    const firstName = billing.first_name || 'Valued';
    const lastName = billing.last_name || 'Customer';
    const postalCode = shipping.postcode || billing.postcode || '';

    // Verify serviceability against 3-district whitelist
    const serviceability = this.pinServiceability.checkPinServiceability(postalCode);

    return this.prisma.$transaction(async (tx) => {
      // 1. Find or create user & customer record
      let user = await tx.user.findUnique({ where: { email } });
      if (!user) {
        user = await tx.user.create({
          data: {
            email,
            role: UserRole.CUSTOMER,
            isVerified: true,
          },
        });
      }

      let customer = await tx.customer.findUnique({ where: { userId: user.id } });
      if (!customer) {
        customer = await tx.customer.create({
          data: {
            userId: user.id,
            firstName,
            lastName,
            phone: billing.phone || `+91${Math.floor(1000000000 + Math.random() * 9000000000)}`,
            addressLine1: shipping.address_1 || billing.address_1 || '',
            addressLine2: shipping.address_2 || billing.address_2 || '',
            city: serviceability.district || shipping.city || billing.city || 'Kolkata',
            postalCode: postalCode,
            wpCustomerId: data.customer_id ? Number(data.customer_id) : null,
          },
        });
      }

      // 2. Map WooCommerce Status
      const statusMap: Record<string, OrderStatus> = {
        pending: OrderStatus.PENDING,
        processing: OrderStatus.CONFIRMED,
        'on-hold': OrderStatus.PENDING,
        completed: OrderStatus.DELIVERED,
        cancelled: OrderStatus.CANCELLED,
        refunded: OrderStatus.REFUNDED,
        failed: OrderStatus.CANCELLED,
      };

      // If unserviceable, forcibly mark CANCELLED / REJECTED and do not assign fulfillment
      let orderStatus = statusMap[data.status] || OrderStatus.PENDING;
      let orderNotes = data.customer_note || `Imported from WooCommerce #${wpOrderId}`;

      if (!serviceability.deliverable) {
        orderStatus = OrderStatus.CANCELLED;
        orderNotes = `[REJECTED - UNSERVICEABLE AREA] ${serviceability.message}`;
        this.logger.warn(`WooCommerce Order #${wpOrderId} rejected due to unserviceable PIN ${postalCode}`);
      } else {
        orderNotes = `${orderNotes} | Resolved District: ${serviceability.district}`;
      }

      const totalAmount = Number(data.total || 0);
      const deliveryFee = Number(data.shipping_total || 0);
      const discount = Number(data.discount_total || 0);

      // Check if existing order with this wpOrderId
      const existingOrder = await tx.order.findUnique({
        where: { wpOrderId },
      });

      let orderRecord;
      if (existingOrder) {
        orderRecord = await tx.order.update({
          where: { id: existingOrder.id },
          data: {
            status: orderStatus,
            totalAmount,
            deliveryFee,
            discount,
            notes: orderNotes,
          },
        });
      } else {
        const dateStr = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        const orderNumber = `ORD-WP-${wpOrderId}-${dateStr}`;

        orderRecord = await tx.order.create({
          data: {
            orderNumber,
            customerId: customer.id,
            wpOrderId,
            status: orderStatus,
            subtotal: totalAmount - deliveryFee + discount,
            discount,
            deliveryFee,
            totalAmount,
            notes: orderNotes,
          },
        });

        // Insert line items
        if (data.line_items && Array.isArray(data.line_items)) {
          for (const item of data.line_items) {
            await tx.orderItem.create({
              data: {
                orderId: orderRecord.id,
                wpProductId: item.product_id ? Number(item.product_id) : null,
                name: item.name || 'Product',
                quantity: Number(item.quantity || 1),
                unitPrice: Number(item.price || 0),
                totalPrice: Number(item.total || 0),
              },
            });
          }
        }
      }

      this.logger.log(`Synced WooCommerce Order #${wpOrderId} -> Order ID: ${orderRecord.id} (Status: ${orderStatus})`);
      return orderRecord;
    });
  }

  /**
   * List orders with filtering, pagination, and resolved district metadata
   */
  async findAll(filter: OrderFilterDto, user?: any) {
    const { status, customerId, search, page = 1, limit = 10 } = filter;
    const skip = (page - 1) * limit;

    const where: Prisma.OrderWhereInput = {};

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
        { orderNumber: { contains: search } },
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
      this.prisma.order.count({ where }),
      this.prisma.order.findMany({
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
          booking: {
            select: {
              id: true,
              bookingNumber: true,
              status: true,
              rentalStart: true,
              rentalEnd: true,
              deliverySlot: { include: { zone: true } },
            },
          },
          payments: {
            select: {
              id: true,
              method: true,
              status: true,
              amount: true,
            },
          },
          items: true,
        },
      }),
    ]);

    // Enrich with authoritative resolved district
    const items = rawItems.map((order) => {
      const pin = order.customer?.postalCode || '';
      const serviceability = this.pinServiceability.checkPinServiceability(pin);
      return {
        ...order,
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
   * Retrieve single order details
   */
  async findOne(id: string, user?: any) {
    const order = await this.prisma.order.findUnique({
      where: { id },
      include: {
        customer: true,
        booking: {
          include: {
            package: true,
            deliverySlot: { include: { zone: true } },
            rental: true,
          },
        },
        items: true,
        payments: true,
      },
    });

    if (!order) {
      throw new NotFoundException(`Order with ID ${id} not found.`);
    }

    if (user && user.role === UserRole.CUSTOMER) {
      const customer = await this.prisma.customer.findUnique({
        where: { userId: user.id },
      });
      if (!customer || order.customerId !== customer.id) {
        throw new ForbiddenException('You do not have permission to view this order.');
      }
    }

    const pin = order.customer?.postalCode || '';
    const serviceability = this.pinServiceability.checkPinServiceability(pin);

    return {
      ...order,
      resolvedDistrict: serviceability.district || 'Unserviceable Area',
      isServiceable: serviceability.deliverable,
    };
  }

  /**
   * Alias for create
   */
  async createOrder(createOrderDto: CreateOrderDto, user?: any) {
    return this.create(createOrderDto, user);
  }

  /**
   * Update order status
   */
  async updateStatus(id: string, statusOrDto: UpdateOrderStatusDto | OrderStatus, user?: any) {
    await this.findOne(id, user);

    const statusValue = typeof statusOrDto === 'string' ? statusOrDto : statusOrDto.status;

    const updated = await this.prisma.order.update({
      where: { id },
      data: { status: statusValue },
    });

    this.logger.log(`Order #${id} status updated to ${statusValue}`);
    return updated;
  }
}
