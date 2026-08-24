# BookNowShisha - Architectural Decision Records (ADRs)

## ADR 001: Separation of Concerns (Headless NestJS Backend vs WordPress CMS)

### Status: Accepted
### Context:
The business requires a rich e-commerce storefront with high SEO visibility and easy content management (WordPress + WooCommerce), alongside complex real-time rental booking, physical serialised hardware tracking, delivery slot concurrency control, damage deposit reconciliation, and field cash collection.

### Decision:
Maintain a strict separation between presentation/catalog management (WordPress + WooCommerce) and mission-critical business logic (NestJS + PostgreSQL + Redis). WooCommerce handles the customer-facing cart, customer accounts, and standard digital checkout, while the custom `hookah-rental-core` plugin communicates via authenticated REST to NestJS.

### Consequences:
- **Pros**: Business logic is framework-agnostic, strictly typed (TypeScript), unit-tested, and secure from direct CMS vulnerabilities.
- **Cons**: Requires synchronization of IDs (e.g. `wpProductId`, `wpOrderId`) across boundaries.

---

## ADR 002: PostgreSQL + Prisma ORM for Relational Modeling

### Status: Accepted
### Context:
Rental operations require strict relational integrity (customers, bookings, rental items, physical inventory units, deposits, return inspections).

### Decision:
Use PostgreSQL 16 with Prisma ORM. Models use foreign keys with explicit cascade/restrict rules to guarantee data consistency.

### Consequences:
- High type safety across all database queries.
- Automatic migration history tracking.

---

## ADR 003: Redis for Fast Availability Caching & Concurrency Locks

### Status: Accepted
### Context:
Simultaneous users attempting to book the same limited hookah models or delivery time slots must not cause race conditions or overbooking.

### Decision:
Use Redis 7 for distributed resource locking during the booking checkout flow and short-lived caching for slot availability.

---

## ADR 004: Single UPI Aggregator + Cash on Delivery (COD) for MVP

### Status: Accepted
### Context:
The initial platform market focuses on Indian metro delivery zones where UPI and Cash on Delivery dominate consumer preference.

### Decision:
Scope MVP payment integration to UPI (via dynamic QR / Intent) and Cash on Delivery with full field collection and reconciliation tracking. Exclude international gateways (Stripe, PayPal) and card tokenization until future phases.
