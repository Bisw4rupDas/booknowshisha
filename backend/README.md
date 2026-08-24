# BookNowShisha - Backend API

Core business logic, authentication, rental engine, inventory tracking, and commerce integration API built with NestJS, TypeScript, Prisma, PostgreSQL, and Redis.

## Directory Overview

```
backend/
├── src/
│   ├── auth/          # Authentication & Google OAuth
│   ├── users/         # User identities & role management
│   ├── customers/     # Customer profiles & WooCommerce linkage
│   ├── hookahs/       # Hookah models & catalog specs
│   ├── rentals/       # Rental orders, status workflow & lifecycle
│   ├── bookings/      # Booking reservation system & locking
│   ├── flavours/      # Flavour categories & consumable tracking
│   ├── inventory/     # Individual serialised hookah units
│   ├── packages/      # Rental bundles & tiered pricing
│   ├── delivery/      # Delivery zones, slots & dispatch
│   ├── orders/        # E-commerce orders & WooCommerce sync
│   ├── payments/      # UPI & payment gateway integration
│   ├── returns/       # Returns inspection & intake
│   ├── damage/        # Damage assessment & deposit deductions
│   ├── notifications/ # Transactional emails & SMS
│   ├── admin/         # Admin management, COD & reconciliation
│   ├── common/        # Filters, interceptors, guards, decorators
│   ├── health/        # Liveness & readiness diagnostic checks
│   ├── prisma/        # Prisma service & database module
│   ├── app.module.ts  # Root application module
│   └── main.ts        # Bootstrap entrypoint & Swagger setup
├── prisma/
│   └── schema.prisma  # PostgreSQL relational models
└── test/              # Unit and E2E test suites
```

## Quick Start

### 1. Install Dependencies
```bash
npm install
```

### 2. Generate Prisma Client
```bash
npx prisma generate
```

### 3. Start Development Server
```bash
npm run start:dev
```

### 4. Interactive API Documentation
Once running, open: [http://localhost:3000/api/docs](http://localhost:3000/api/docs)
