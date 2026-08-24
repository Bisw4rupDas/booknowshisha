# BookNowShisha - Local Development Guide

## 1. Prerequisites

Ensure the following tools are installed on your workstation:
- **Node.js**: v20.x or v22.x+
- **npm**: v10.x+
- **Docker Desktop**: v24.x+ (with Docker Compose v2)
- **Git**: v2.40+

---

## 2. Initial Setup

### Step 1: Clone and Configure Environment
Copy `.env.example` to create your local `.env`:
```bash
cp .env.example .env
```

### Step 2: Install Backend Dependencies
```bash
cd backend
npm install
```

### Step 3: Generate Prisma Client
```bash
npx prisma generate
```

---

## 3. Running with Docker Compose

To start all development services (PostgreSQL, Redis, NestJS Backend, WordPress, and WP MySQL):

```bash
docker compose up -d
```

### Checking Running Services:
```bash
docker compose ps
```

### Viewing Logs:
```bash
# All service logs
docker compose logs -f

# Backend logs only
docker compose logs -f backend

# PostgreSQL logs only
docker compose logs -f postgres
```

### Stopping Services:
```bash
# Stop containers without deleting data
docker compose down

# Stop containers and wipe persistent volumes (clean reset)
docker compose down -v
```

---

## 4. Running Backend Locally (Non-Docker)

If running PostgreSQL and Redis via Docker, but running NestJS directly on your host machine for rapid development:

1. Start database and cache services:
```bash
docker compose up -d postgres redis
```

2. Run Prisma migrations:
```bash
cd backend
npx prisma migrate dev --name init
```

3. Start NestJS in watch mode:
```bash
npm run start:dev
```

4. Verify local backend health:
```bash
curl http://localhost:3000/api/health
```

---

## 5. WordPress & WooCommerce Setup

1. Open [http://localhost:8080](http://localhost:8080) in your browser.
2. Complete the WordPress 5-minute installation.
3. In WP-Admin:
   - Navigate to **Plugins** -> Activate **WooCommerce** and **Hookah Rental Core**.
   - Navigate to **Appearance -> Themes** -> Activate **BookNowShisha Theme**.
   - Navigate to **Hookah Rentals** in the sidebar -> Verify API URL points to `http://backend:3000/api` (or `http://localhost:3000/api`).

---

## 6. Testing & Quality Checks

Run linting:
```bash
npm run backend:lint
```

Run unit tests:
```bash
npm run backend:test
```

Run production build check:
```bash
npm run backend:build
```
