# ShishaRent Monorepo

> Premium E-commerce & Hookah Rental Platform

**ShishaRent** is a hybrid commerce and rental platform uniting a luxury WordPress/WooCommerce frontend storefront with a high-performance NestJS microservice backend, PostgreSQL database, and Redis cache.

---

## 📁 Monorepo Structure

```
shisharent/
│
├── backend/                  # NestJS TypeScript API & Business Logic Engine
│   ├── src/
│   │   ├── auth/             # Authentication & OAuth
│   │   ├── users/            # Users & RBAC
│   │   ├── customers/        # Customer profiles & WooCommerce linking
│   │   ├── hookahs/          # Hookah catalog specs
│   │   ├── rentals/          # Rental lifecycle management
│   │   ├── bookings/         # Booking reservation engine
│   │   ├── flavours/         # Flavours & consumables stock
│   │   ├── inventory/        # Serialised physical unit tracking
│   │   ├── packages/         # Rental packages & tiered pricing
│   │   ├── delivery/         # Delivery zones & slots
│   │   ├── orders/           # E-commerce orders & Woo sync
│   │   ├── payments/         # UPI gateway & payment processing
│   │   ├── returns/          # Returns inspection & intake
│   │   ├── damage/           # Damage assessment & deposit deductions
│   │   ├── notifications/    # Transactional notifications
│   │   ├── admin/            # Admin operations & COD reconciliation
│   │   ├── common/           # Filters, interceptors, guards & pipes
│   │   ├── health/           # Infrastructure & diagnostic health check
│   │   ├── prisma/           # Prisma service & database module
│   │   ├── app.module.ts     # Root NestJS application module
│   │   └── main.ts           # Application bootstrap & Swagger config
│   │
│   ├── test/                 # Unit & End-to-End test suites
│   ├── prisma/
│   │   └── schema.prisma     # PostgreSQL schema definitions
│   ├── Dockerfile            # Multi-stage production container build
│   ├── package.json          # Backend dependencies & npm scripts
│   ├── tsconfig.json         # Strict TypeScript configuration
│   └── README.md
│
├── wordpress/                # WordPress Storefront & Custom Extensions
│   ├── theme/
│   │   └── booknowshisha-theme/      # Luxury dark theme with WooCommerce support
│   │
│   └── plugins/
│       └── hookah-rental-core/       # Custom integration plugin & rental widgets
│           ├── hookah-rental-core.php
│           ├── includes/
│           ├── api/
│           ├── woo/
│           ├── rental/
│           ├── checkout/
│           ├── availability/
│           ├── admin/
│           ├── assets/
│           ├── templates/
│           └── README.md
│
├── docs/                     # Comprehensive Architecture & Technical Specs
│   ├── architecture.md       # High-level architecture & sequence flows
│   ├── api.md                # REST API endpoints & payload specifications
│   ├── database.md           # Entity relationships & data dictionary
│   ├── development.md        # Local environment & Docker instructions
│   └── decisions.md          # Architectural Decision Records (ADRs)
│
├── docker/                   # Docker initialization scripts & database seeds
│   └── postgres/
│       └── init.sql          # DB extensions init script
│
├── .env.example              # Environment variables template
├── .gitignore                # Global git ignore configuration
├── docker-compose.yml        # Multi-service local development environment
├── package.json              # Monorepo root orchestration scripts
└── README.md                 # Project overview & documentation index
```

---

## 🚀 Quick Start

### 1. Environment Setup
```bash
# Copy example environment file
cp .env.example .env
```

### 2. Launch Stack via Docker
```bash
# Start PostgreSQL, Redis, WordPress, and MySQL
docker compose up -d
```

### 3. Verify Health & Documentation
- **WordPress Storefront**: [http://localhost:8080](http://localhost:8080)
- **Backend Health Check**: [http://localhost:3000/api/health](http://localhost:3000/api/health)
- **Interactive Swagger Docs**: [http://localhost:3000/api/docs](http://localhost:3000/api/docs)

---

## 🛠️ Technology Stack

| Layer | Technology | Purpose |
|---|---|---|
| **Frontend CMS** | WordPress 6.x + WooCommerce | Storefront, catalog, SEO, content management |
| **Theme** | Custom `shisharent` theme | Editorial dark luxury styling, mobile-first UX |
| **WP Plugin** | Custom `hookah-rental-core` | Bridge between WooCommerce cart/checkout and API |
| **Backend API** | NestJS 10.x (TypeScript) | Business-critical logic, booking engine, RBAC |
| **Database** | PostgreSQL 16 + Prisma ORM | Relational data persistence & migrations |
| **Cache & Queue**| Redis 7 | Availability caching & distributed locking |
| **Containerization**| Docker & Docker Compose | Containerized local and production infrastructure |
