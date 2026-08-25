# BookNowShisha - Backend API

Core business logic, authentication, rental engine, inventory tracking, and commerce integration API built with NestJS, TypeScript, Prisma, PostgreSQL, and Redis.

## Directory Overview

```
backend/
â”œâ”€â”€ src/
│   ├── auth/          # Email Authentication & Customer Security
â”‚   â”œâ”€â”€ users/         # User identities & role management
â”‚   â”œâ”€â”€ customers/     # Customer profiles & WooCommerce linkage
â”‚   â”œâ”€â”€ hookahs/       # Hookah models & catalog specs
â”‚   â”œâ”€â”€ rentals/       # Rental orders, status workflow & lifecycle
â”‚   â”œâ”€â”€ bookings/      # Booking reservation system & locking
â”‚   â”œâ”€â”€ flavours/      # Flavour categories & consumable tracking
â”‚   â”œâ”€â”€ inventory/     # Individual serialised hookah units
â”‚   â”œâ”€â”€ packages/      # Rental bundles & tiered pricing
â”‚   â”œâ”€â”€ delivery/      # Delivery zones, slots & dispatch
â”‚   â”œâ”€â”€ orders/        # E-commerce orders & WooCommerce sync
â”‚   â”œâ”€â”€ payments/      # UPI & payment gateway integration
â”‚   â”œâ”€â”€ returns/       # Returns inspection & intake
â”‚   â”œâ”€â”€ damage/        # Damage assessment & deposit deductions
â”‚   â”œâ”€â”€ notifications/ # Transactional emails & SMS
â”‚   â”œâ”€â”€ admin/         # Admin management, COD & reconciliation
â”‚   â”œâ”€â”€ common/        # Filters, interceptors, guards, decorators
â”‚   â”œâ”€â”€ health/        # Liveness & readiness diagnostic checks
â”‚   â”œâ”€â”€ prisma/        # Prisma service & database module
â”‚   â”œâ”€â”€ app.module.ts  # Root application module
â”‚   â””â”€â”€ main.ts        # Bootstrap entrypoint & Swagger setup
â”œâ”€â”€ prisma/
â”‚   â””â”€â”€ schema.prisma  # PostgreSQL relational models
â””â”€â”€ test/              # Unit and E2E test suites
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


