# 🚀 BookNowShisha — Complete cPanel Deployment Guide

This guide walks you through deploying the **BookNowShisha** NestJS backend on **Grabersites cPanel** hosting using **MySQL/MariaDB**, **Node.js (Passenger / Application Manager)**, and connecting it to your **WordPress + WooCommerce** storefront.

---

## 📋 Architecture Overview

```
WordPress + WooCommerce (Storefront)
       │
       │ HTTPS REST API calls (api.yourdomain.com)
       ▼
NestJS Backend (cPanel Node.js / Phusion Passenger via app.js)
       │
       ▼
cPanel MySQL / MariaDB (Local Database)
       │
       ▼
Database ACID Concurrency Locking (100% anti-double-booking protection)
```

---

## 🛠️ Step-by-Step Deployment Instructions

### STEP 1: Prepare the Project Locally
Ensure your code is clean, built, and committed:
```bash
# Inside project root
git status
git add .
git commit -m "feat: cPanel MySQL and Phusion Passenger production compatibility"
git push origin main
```

---

### STEP 2: Create the Database in cPanel
1. Log in to your **cPanel** dashboard.
2. In the **Databases** section, click **MySQL® Databases** (or **MySQL® Database Wizard**).
3. Under **Create New Database**, enter a database name, e.g. `booknowshisha`.
4. Click **Create Database**.
   > **Note:** cPanel automatically prefixes names with your cPanel username (e.g. `youruser_booknowshisha`). Note this full name.

---

### STEP 3: Create the Database User
1. Scroll down to **MySQL Users** -> **Add New User**.
2. Enter a username (e.g. `bns_user` -> full name `youruser_bns_user`).
3. Click **Password Generator** to create a strong password (at least 16+ characters).
4. Save the password securely in your password manager.
5. Click **Create User**.

---

### STEP 4: Assign Database Privileges
1. Scroll down to **Add User To Database**.
2. Select your user (`youruser_bns_user`) and database (`youruser_booknowshisha`).
3. Click **Add**.
4. Check the box **ALL PRIVILEGES**.
5. Click **Make Changes**.

---

### STEP 5: Import / Migrate Existing PostgreSQL Data (If Applicable)
If you have existing data in a PostgreSQL database (e.g., Supabase / RDS / Local) that you wish to migrate into your new cPanel MySQL database:

#### A. Back Up Your PostgreSQL Database First:
```bash
pg_dump -h your-postgres-host -U postgres -d booknowshisha_db -F c -b -v -f "backup_$(date +%Y%m%d).dump"
```

#### B. Run the Safe Data Migration Script:
```bash
cd backend
SOURCE_POSTGRES_URL="postgresql://user:pass@host:5432/dbname" \
TARGET_MYSQL_URL="mysql://youruser_bns_user:YourPass@localhost:3306/youruser_booknowshisha" \
npm run migrate:data
```
> The migration script transfers users, customer profiles, hookah models, serialized units, flavours, packages, delivery zones, bookings, rentals, orders, and payments with 100% data integrity validation.

---

### STEP 6: Upload Project or Connect Git Repository
You can deploy your code via **cPanel Git Version Control** or **File Manager / SSH / FTP**:

#### Option A: via cPanel Git Version Control (Recommended):
1. In cPanel, click **Git™ Version Control**.
2. Click **Create**.
3. Toggle on **Clone a Repository**.
4. Enter your Git Clone URL (e.g., `https://github.com/yourorg/booknowshisha.git`).
5. Set the **Repository Path**: `repositories/booknowshisha` (outside `public_html`).
6. Click **Create**.

#### Option B: via cPanel Terminal / SSH:
```bash
cd ~
git clone https://github.com/yourorg/booknowshisha.git booknowshisha
cd booknowshisha
```

---

### STEP 7: Create the Node.js Application in cPanel
1. In cPanel, navigate to **Software** -> **Setup Node.js App** (or **Application Manager**).
2. Click **Create Application**.
3. Configure the following fields:
   - **Node.js version**: Select **20.x** or **18.x LTS**.
   - **Application mode**: Select **Production**.
   - **Application root**: `booknowshisha` (or the folder path where you cloned the repo).
   - **Application URL**: Select your subdomain (e.g. `api.yourdomain.com`).
   - **Application startup file**: `app.js`.
4. Click **Create**.

---

### STEP 8: Configure Environment Variables
In the **Setup Node.js App** interface, scroll to **Environment variables** (or create a `.env` file inside `backend/.env`):

| Variable Name | Example Value | Description |
| :--- | :--- | :--- |
| `NODE_ENV` | `production` | Production mode |
| `PORT` | `3000` | Process port (Passenger routes automatically) |
| `API_PREFIX` | `api` | Base API prefix |
| `DATABASE_URL` | `mysql://youruser_bns_user:Pass@localhost:3306/youruser_booknowshisha` | cPanel MySQL Connection |
| `REDIS_URL` | *(leave empty if not using external Redis)* | External Redis URL (optional) |
| `JWT_SECRET` | `your_64_character_random_secret_here` | Customer/Admin JWT Secret |
| `JWT_EXPIRATION` | `7d` | Token expiry |
| `JWT_REFRESH_SECRET` | `your_64_character_random_refresh_secret` | Refresh JWT Secret |
| `JWT_REFRESH_EXPIRATION` | `30d` | Refresh expiry |
| `API_PUBLIC_URL` | `https://api.yourdomain.com` | Public Backend URL |
| `CORS_ORIGINS` | `https://yourdomain.com,https://www.yourdomain.com` | Allowed frontend domains |
| `WORDPRESS_URL` | `https://yourdomain.com` | WordPress Storefront URL |
| `HOOKAH_RENTAL_CORE_SHARED_SECRET` | `your_shared_plugin_bridge_secret` | WordPress Plugin Shared Secret |
| `SMS_PROVIDER` | `fast2sms` | SMS Provider for Indian OTP |
| `FAST2SMS_API_KEY` | `your_fast2sms_key` | Fast2SMS API Key |

Click **Save**.

---

### STEP 9: Enter the Virtual Environment & Install Dependencies
1. In the **Setup Node.js App** screen, copy the command to enter the virtual environment, for example:
   ```bash
   source /home/youruser/nodevenv/booknowshisha/20/bin/activate && cd /home/youruser/booknowshisha
   ```
2. Open **cPanel Terminal** (or SSH) and paste the command.
3. Install dependencies in the backend directory:
   ```bash
   cd backend
   npm install --production=false
   ```

---

### STEP 10: Generate Prisma Client
Run Prisma Client generation for MySQL:
```bash
npx prisma generate
```

---

### STEP 11: Run Database Migrations
Apply the MySQL schema migrations to create all database tables:
```bash
npx prisma migrate deploy
```
*(Optional for fresh installs: Seed default catalog, Kolkata zones, and models)*:
```bash
npm run prisma:seed
```

---

### STEP 12: Build NestJS Application
Compile the TypeScript code to JavaScript:
```bash
npm run build
```
*(Verify that `backend/dist/main.js` is generated).*

---

### STEP 13: Start / Restart the Application
1. Go back to cPanel **Setup Node.js App**.
2. Click **Restart** on your application.

---

### STEP 14: Configure the API Subdomain
1. In cPanel, navigate to **Domains** -> **Domains** (or **Subdomains**).
2. Click **Create A New Domain**.
3. Domain: `api.yourdomain.com`.
4. Document Root: Leave as default or map to your Node.js application directory.
5. In cPanel **SSL/TLS Status**, click **Run AutoSSL** to obtain an SSL certificate for `api.yourdomain.com`.

---

### STEP 15: Configure WordPress & WooCommerce
1. Log in to your **WordPress Admin Panel** (`https://yourdomain.com/wp-admin`).
2. Go to **ShishaRent** (or **Hookah Rental**) -> **Settings**.
3. Set **Backend API URL**:
   ```
   https://api.yourdomain.com/api
   ```
4. Set **Shared Secret Key**: Enter the value matching `HOOKAH_RENTAL_CORE_SHARED_SECRET`.
5. Click **Save Changes**.

---

### STEP 16: Test Health Endpoint
Open your browser or run in Terminal:
```bash
curl -i https://api.yourdomain.com/api/health
```
**Expected Response:**
```json
{
  "status": "ok",
  "timestamp": "2026-08-31T13:30:00.000Z",
  "uptime": 12.34,
  "environment": "production",
  "services": {
    "database": {
      "status": "connected",
      "latencyMs": 2
    },
    "redis": {
      "status": "disabled"
    }
  }
}
```

---

### STEP 17: Test Authentication
Verify login and profile retrieval:
```bash
# Test Customer Login
curl -X POST https://api.yourdomain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "customer@shisharent.com", "password": "ShishaRent@2026"}'
```

---

### STEP 18: Test Booking & Concurrency
Verify postal PIN serviceability check:
```bash
curl -X POST https://api.yourdomain.com/api/delivery/check-zone \
  -H "Content-Type: application/json" \
  -d '{"postalCode": "700091"}'
```

Verify available packages:
```bash
curl https://api.yourdomain.com/api/packages
```

---

## 🔒 Security Best Practices for Shared Hosting

1. **Keep `.env` outside public root**: Store your `.env` file in the application directory, not inside `public_html`.
2. **Deny direct access to dotfiles**: Ensure `.htaccess` blocks all files beginning with `.env` or `.git`.
3. **Database Permissions**: Use a dedicated MySQL user with privileges restricted strictly to the `booknowshisha` database.
4. **SSL Enforcement**: Always force HTTPS on both `yourdomain.com` and `api.yourdomain.com`.
