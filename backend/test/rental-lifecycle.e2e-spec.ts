import { Test, TestingModule } from '@nestjs/testing';
import { INestApplication, ValidationPipe } from '@nestjs/common';
// eslint-disable-next-line @typescript-eslint/no-var-requires
const request = require('supertest');
import * as bcrypt from 'bcryptjs';
import { AppModule } from '../src/app.module';
import { PrismaService } from '../src/prisma/prisma.service';
import { RentalStatus, HookahInventoryStatus, PaymentStatus, UserRole } from '@prisma/client';

describe('Complete ShishaRent Rental Lifecycle (E2E Integration)', () => {
  let app: INestApplication;
  let prisma: PrismaService;

  let customerToken: string;
  let customerId: string;
  let adminToken: string;

  let packageId: string;
  let hookahModelId: string;
  let flavourId1: string;
  let flavourId2: string;
  let deliverySlotId: string;
  let inventoryUnitId: string;

  let createdBookingId: string;
  let createdBookingNumber: string;
  let createdPaymentNumber: string;
  let createdRentalId: string;

  const testEmail = `e2e_customer_${Date.now()}@shisharent.local`;
  const testPassword = 'Password123!';
  const testPhone = `+919${Math.floor(100000000 + Math.random() * 900000000)}`;

  const adminEmail = `admin_e2e_${Date.now()}@shisharent.local`;
  const adminPassword = 'AdminPassword123!';

  beforeAll(async () => {
    const moduleFixture: TestingModule = await Test.createTestingModule({
      imports: [AppModule],
    }).compile();

    app = moduleFixture.createNestApplication();
    app.setGlobalPrefix('api');
    app.useGlobalPipes(
      new ValidationPipe({
        whitelist: true,
        transform: true,
        forbidNonWhitelisted: false,
      }),
    );

    await app.init();
    prisma = app.get<PrismaService>(PrismaService);

    // Create Admin + Staff User for Staff/Admin operations
    const passwordHash = await bcrypt.hash(adminPassword, 10);
    await prisma.user.create({
      data: {
        email: adminEmail,
        passwordHash,
        role: UserRole.ADMIN,
        isVerified: true,
        admin: {
          create: {
            fullName: 'Operations Manager',
            department: 'Logistics',
          },
        },
        staff: {
          create: {
            fullName: 'Operations Manager',
            phone: `+918${Math.floor(100000000 + Math.random() * 900000000)}`,
            designation: 'Logistics Lead',
          },
        },
      },
    });

    // Retrieve seed fixtures from database
    const pkg = await prisma.package.findFirst({
      where: { isActive: true },
      include: { items: { include: { hookahModel: true } } },
    });
    if (pkg) {
      packageId = pkg.id;
      if (pkg.items.length > 0) {
        hookahModelId = pkg.items[0].hookahModelId;
      }
    }

    const flavours = await prisma.flavour.findMany({
      where: { isActive: true },
      take: 2,
    });
    if (flavours.length >= 2) {
      flavourId1 = flavours[0].id;
      flavourId2 = flavours[1].id;
    }

    const slot = await prisma.deliverySlot.findFirst({
      where: { isActive: true },
      include: { zone: true },
    });
    if (slot) {
      deliverySlotId = slot.id;
    }

    // Ensure at least one available serialized unit exists for this model
    if (hookahModelId) {
      let unit = await prisma.hookahInventory.findFirst({
        where: { hookahModelId, status: HookahInventoryStatus.AVAILABLE },
      });
      if (!unit) {
        unit = await prisma.hookahInventory.create({
          data: {
            hookahModelId,
            serialNumber: `E2E-TEST-${Date.now()}`,
            barcode: `BAR-E2E-${Date.now()}`,
            status: HookahInventoryStatus.AVAILABLE,
          },
        });
      }
      inventoryUnitId = unit.id;
    }
  });

  afterAll(async () => {
    // Clean up test customer data
    if (customerId) {
      try {
        await prisma.customer.delete({ where: { id: customerId } }).catch(() => {});
      } catch {}
    }
    await app.close();
  });

  describe('Step 1: Diagnostics & Health Check', () => {
    it('GET /api/health - should report healthy infrastructure with connected database and redis', async () => {
      const res = await request(app.getHttpServer())
        .get('/api/health')
        .expect(200);

      expect(res.body.status).toBe('ok');
      expect(res.body.services.database.status).toBe('connected');
      expect(res.body.services.redis.status).toBe('connected');
    });
  });

  describe('Step 2: Customer Identity & Admin Authentication', () => {
    it('POST /api/auth/register - should register a new customer', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/auth/register')
        .send({
          email: testEmail,
          password: testPassword,
          firstName: 'Vikram',
          lastName: 'Mehta',
          phone: testPhone,
          addressLine1: '42, Salt Lake Sector V',
          city: 'Kolkata',
          postalCode: '700091',
        })
        .expect(201);

      expect(res.body).toHaveProperty('user');
      expect(res.body).toHaveProperty('accessToken');
    });

    it('POST /api/auth/login - should authenticate customer and return JWT access token', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/auth/login')
        .send({
          email: testEmail,
          password: testPassword,
        })
        .expect(200);

      expect(res.body).toHaveProperty('accessToken');
      customerToken = res.body.accessToken;
      expect(customerToken).toBeDefined();

      // Retrieve created customer record
      const user = await prisma.user.findUnique({
        where: { email: testEmail },
        include: { customer: true },
      });
      if (user && user.customer) {
        customerId = user.customer.id;
      }
    });

    it('POST /api/auth/login - should authenticate admin and return admin JWT token', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/auth/login')
        .send({
          email: adminEmail,
          password: adminPassword,
        })
        .expect(200);

      expect(res.body).toHaveProperty('accessToken');
      adminToken = res.body.accessToken;
      expect(adminToken).toBeDefined();
    });

    it('POST /api/auth/login - should reject invalid password with 401 Unauthorized', async () => {
      await request(app.getHttpServer())
        .post('/api/auth/login')
        .send({
          email: adminEmail,
          password: 'IncorrectPassword123!',
        })
        .expect(401);
    });

    it('GET /api/auth/me - should retrieve authenticated user profile with JWT token', async () => {
      const res = await request(app.getHttpServer())
        .get('/api/auth/me')
        .set('Authorization', `Bearer ${adminToken}`)
        .expect(200);

      expect(res.body).toHaveProperty('id');
      expect(res.body.email).toBe(adminEmail);
    });
  });

  describe('Step 3: Catalog & Delivery Zone Availability Verification', () => {
    it('POST /api/delivery/check-zone - should PASS for Kolkata district PIN 700019', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/delivery/check-zone')
        .send({ postalCode: '700019' })
        .expect(201);

      expect(res.body.serviceable).toBe(true);
      expect(res.body.deliverable).toBe(true);
      expect(res.body.district).toBe('Kolkata');
      expect(res.body.message).toContain('Delivery available in Kolkata');
    });

    it('POST /api/delivery/check-zone - should PASS for North 24 Parganas district PIN 700091', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/delivery/check-zone')
        .send({ postalCode: '700091' })
        .expect(201);

      expect(res.body.serviceable).toBe(true);
      expect(res.body.deliverable).toBe(true);
      expect(res.body.district).toBe('North 24 Parganas');
      expect(res.body).toHaveProperty('baseDeliveryFee');
    });

    it('POST /api/delivery/check-zone - should PASS for South 24 Parganas district PIN 700027', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/delivery/check-zone')
        .send({ postalCode: '700027' })
        .expect(201);

      expect(res.body.serviceable).toBe(true);
      expect(res.body.deliverable).toBe(true);
      expect(res.body.district).toBe('South 24 Parganas');
    });

    it('POST /api/delivery/check-zone - should REJECT Howrah district PIN 711101', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/delivery/check-zone')
        .send({ postalCode: '711101' })
        .expect(201);

      expect(res.body.serviceable).toBe(false);
      expect(res.body.deliverable).toBe(false);
      expect(res.body.district).toBe('Howrah');
      expect(res.body.message).toContain('Delivery not available in Howrah');
    });

    it('POST /api/delivery/check-zone - should REJECT Delhi PIN 110001', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/delivery/check-zone')
        .send({ postalCode: '110001' })
        .expect(201);

      expect(res.body.serviceable).toBe(false);
      expect(res.body.deliverable).toBe(false);
      expect(res.body.district).toBe('New Delhi');
    });

    it('GET /api/packages - should list active packages', async () => {
      const res = await request(app.getHttpServer())
        .get('/api/packages')
        .expect(200);

      expect(Array.isArray(res.body)).toBe(true);
      expect(res.body.length).toBeGreaterThan(0);
    });

    it('GET /api/flavours - should list available flavour catalog', async () => {
      const res = await request(app.getHttpServer())
        .get('/api/flavours')
        .expect(200);

      expect(Array.isArray(res.body)).toBe(true);
      expect(res.body.length).toBeGreaterThan(0);
    });
  });

  describe('Step 4: Concurrency-Safe Booking Reservation', () => {
    it('POST /api/bookings - should create reservation, acquire Redis lock, and assign unit', async () => {
      const rentalStart = new Date(Date.now() + 24 * 3600 * 1000).toISOString();

      const res = await request(app.getHttpServer())
        .post('/api/bookings')
        .set('Authorization', `Bearer ${customerToken}`)
        .send({
          packageId,
          hookahModelId,
          flavourIds: [flavourId1, flavourId2],
          rentalStart,
          deliverySlotId,
          deliveryAddress: '42, Salt Lake Sector V, Kolkata',
          postalCode: '700091',
          notes: 'Test booking with luxury setup',
        })
        .expect(201);

      expect(res.body).toHaveProperty('assignedUnit');
      expect(res.body.breakdown).toHaveProperty('totalToPay');

      if (res.body.booking) {
        createdBookingId = res.body.booking.id;
        createdBookingNumber = res.body.booking.bookingNumber;
      }
      if (res.body.rental) {
        createdRentalId = res.body.rental.id;
      }

      expect(createdBookingId).toBeDefined();
    });

    it('POST /api/bookings - should REJECT booking for unserviceable PIN 711101 (Howrah) with 400 Bad Request', async () => {
      const rentalStart = new Date(Date.now() + 24 * 3600 * 1000).toISOString();

      const res = await request(app.getHttpServer())
        .post('/api/bookings')
        .set('Authorization', `Bearer ${customerToken}`)
        .send({
          packageId,
          hookahModelId,
          flavourIds: [flavourId1, flavourId2],
          rentalStart,
          deliverySlotId,
          deliveryAddress: 'Howrah Station Road, Kolkata',
          postalCode: '711101',
        })
        .expect(400);

      expect(res.body.message).toContain('Kolkata, North 24 Parganas and South 24 Parganas');
    });
  });

  describe('Step 5: UPI Payment Generation & Webhook Confirmation', () => {
    it('POST /api/payments/upi/initiate - should generate dynamic UPI Intent & QR string', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/payments/upi/initiate')
        .set('Authorization', `Bearer ${customerToken}`)
        .send({
          bookingId: createdBookingId,
          amount: 1499.0,
          notes: 'ShishaRent E2E Test Payment',
        })
        .expect(201);

      expect(res.body).toHaveProperty('paymentNumber');
      expect(res.body).toHaveProperty('upiIntent');
      expect(res.body.upiIntent).toContain('upi://pay?');

      createdPaymentNumber = res.body.paymentNumber;
    });

    it('POST /api/payments/upi/webhook - should confirm payment via gateway webhook', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/payments/upi/webhook')
        .send({
          paymentNumber: createdPaymentNumber,
          gatewayTxnId: `UPI-TXN-${Date.now()}`,
          status: 'SUCCESS',
          amount: 1499.0,
        })
        .expect(201);

      expect(res.body.status).toBe(PaymentStatus.SUCCESS);
    });
  });

  describe('Step 6: Rental Activation & State Machine Transitions', () => {
    it('GET /api/rentals/:id - should retrieve full rental details', async () => {
      if (!createdRentalId) {
        const booking = await prisma.booking.findUnique({
          where: { id: createdBookingId },
          include: { rental: true },
        });
        if (booking?.rental) {
          createdRentalId = booking.rental.id;
        }
      }

      const res = await request(app.getHttpServer())
        .get(`/api/rentals/${createdRentalId}`)
        .set('Authorization', `Bearer ${customerToken}`)
        .expect(200);

      expect(res.body.id).toBe(createdRentalId);
      expect(res.body).toHaveProperty('package');
      expect(res.body).toHaveProperty('customer');
    });

    it('PATCH /api/rentals/:id/status - should transition through OUT_FOR_DELIVERY -> DELIVERED -> ACTIVE', async () => {
      // Status is PREPARING from webhook, transition to OUT_FOR_DELIVERY
      await request(app.getHttpServer())
        .patch(`/api/rentals/${createdRentalId}/status`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({ status: RentalStatus.OUT_FOR_DELIVERY })
        .expect(200);

      // Transition to DELIVERED
      await request(app.getHttpServer())
        .patch(`/api/rentals/${createdRentalId}/status`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({ status: RentalStatus.DELIVERED })
        .expect(200);

      // Transition to ACTIVE
      const activeRes = await request(app.getHttpServer())
        .patch(`/api/rentals/${createdRentalId}/status`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({ status: RentalStatus.ACTIVE })
        .expect(200);

      expect(activeRes.body.status).toBe(RentalStatus.ACTIVE);
    });
  });

  describe('Step 7: Return Request & Digital Intake Inspection', () => {
    it('POST /api/rentals/:id/return - customer requests return pickup', async () => {
      const res = await request(app.getHttpServer())
        .post(`/api/rentals/${createdRentalId}/return`)
        .set('Authorization', `Bearer ${customerToken}`)
        .expect(201);

      expect(res.body.status).toBe(RentalStatus.RETURN_PENDING);
    });

    it('POST /api/returns/:id/return - staff conducts return intake inspection', async () => {
      const res = await request(app.getHttpServer())
        .post(`/api/returns/${createdRentalId}/return`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          status: 'PASSED',
          isClean: true,
          allPartsPresent: true,
          notes: 'Hookah stem and bowl returned in excellent condition',
        })
        .expect(201);

      expect(res.body.success).toBe(true);
      expect(res.body.data).toBeDefined();
    });
  });

  describe('Step 8: Damage Assessment & Security Deposit Settlement', () => {
    it('POST /api/damage - files itemized damage report and auto-deducts from security deposit', async () => {
      const res = await request(app.getHttpServer())
        .post('/api/damage')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          rentalId: createdRentalId,
          description: 'Minor silicone bowl thermal wear',
          damageCost: 350.0,
          photos: ['https://cdn.shisharent.com/damage/bowl-001.jpg'],
          autoDeductFromDeposit: true,
        })
        .expect(201);

      expect(res.body).toHaveProperty('id');
      expect(Number(res.body.damageCost)).toBe(350.0);
    });

    it('GET /api/damage/rental/:rentalId - verifies damage report history', async () => {
      const res = await request(app.getHttpServer())
        .get(`/api/damage/rental/${createdRentalId}`)
        .set('Authorization', `Bearer ${customerToken}`)
        .expect(200);

      expect(Array.isArray(res.body)).toBe(true);
      expect(res.body.length).toBeGreaterThan(0);
      expect(res.body[0].description).toBe('Minor silicone bowl thermal wear');
    });
  });

  describe('Step 9: Inventory Release & Completion', () => {
    it('PATCH /api/rentals/:id/status - completes rental and releases unit back to stock', async () => {
      const res = await request(app.getHttpServer())
        .patch(`/api/rentals/${createdRentalId}/status`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({ status: RentalStatus.COMPLETED })
        .expect(200);

      expect(res.body.status).toBe(RentalStatus.COMPLETED);
    });

    it('GET /api/inventory/metrics - validates updated fleet metrics', async () => {
      const res = await request(app.getHttpServer())
        .get('/api/inventory/metrics')
        .set('Authorization', `Bearer ${adminToken}`)
        .expect(200);

      expect(res.body).toHaveProperty('totalUnits');
      expect(res.body).toHaveProperty('available');
      expect(res.body).toHaveProperty('utilizationRate');
    });
  });
});

