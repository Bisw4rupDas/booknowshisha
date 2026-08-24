import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { PrismaModule } from './prisma/prisma.module';
import { RedisModule } from './common/redis/redis.module';
import { HealthModule } from './health/health.module';
import { AuthModule } from './auth/auth.module';
import { UsersModule } from './users/users.module';
import { CustomersModule } from './customers/customers.module';
import { HookahsModule } from './hookahs/hookahs.module';
import { RentalsModule } from './rentals/rentals.module';
import { BookingsModule } from './bookings/bookings.module';
import { FlavoursModule } from './flavours/flavours.module';
import { InventoryModule } from './inventory/inventory.module';
import { PackagesModule } from './packages/packages.module';
import { DeliveryModule } from './delivery/delivery.module';
import { OrdersModule } from './orders/orders.module';
import { PaymentsModule } from './payments/payments.module';
import { ReturnsModule } from './returns/returns.module';
import { DamageModule } from './damage/damage.module';
import { NotificationsModule } from './notifications/notifications.module';
import { AdminModule } from './admin/admin.module';

@Module({
  imports: [
    // Configuration
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: ['.env', '../.env'],
    }),

    // Database ORM & Fast In-Memory State Store
    PrismaModule,
    RedisModule,

    // Core & Diagnostics
    HealthModule,

    // Planned Business Modules
    AuthModule,
    UsersModule,
    CustomersModule,
    HookahsModule,
    RentalsModule,
    BookingsModule,
    FlavoursModule,
    InventoryModule,
    PackagesModule,
    DeliveryModule,
    OrdersModule,
    PaymentsModule,
    ReturnsModule,
    DamageModule,
    NotificationsModule,
    AdminModule,
  ],
})
export class AppModule {}
