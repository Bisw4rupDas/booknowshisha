# BookNowShisha - API Architecture & Endpoints

## 1. Overview
The BookNowShisha API is a RESTful service built with **NestJS**, adhering to OpenAPI 3.0 specifications.

- **Base URL (Local)**: `http://localhost:3000/api`
- **Swagger Documentation**: `http://localhost:3000/api/docs`
- **Authentication**: Bearer JWT / Internal Bridge Key (`X-Core-Secret`)

---

## 2. Planned API Endpoints

### 2.1 Diagnostics & Health
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `GET` | `/api/health` | Comprehensive infrastructure & database health check | Public |

### 2.2 Authentication & Identity
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `POST` | `/api/auth/register` | Register customer account | Public |
| `POST` | `/api/auth/login` | Email/password login | Public |
| `POST` | `/api/auth/google` | Social OAuth exchange | Public |
| `POST` | `/api/auth/refresh` | Refresh JWT access token | Refresh Token |
| `POST` | `/api/auth/logout` | Revoke session | Authenticated |

### 2.3 Rentals & Lifecycle
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `GET` | `/api/rentals` | List customer or system rentals | Authenticated |
| `POST` | `/api/rentals` | Initialize new rental order | Authenticated |
| `GET` | `/api/rentals/:id` | Get single rental details | Authenticated |
| `POST` | `/api/rentals/:id/cancel` | Cancel active rental | Customer / Admin |
| `POST` | `/api/rentals/:id/return` | Mark rental ready for return | Customer / Staff |
| `POST` | `/api/rentals/:id/damage` | Submit damage assessment | Staff / Admin |
| `GET` | `/api/rentals/:id/inspection` | Get inspection report | Staff / Admin |

### 2.4 Availability & Inventory Engine
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `GET` | `/api/availability` | Check general availability for date/slot | Public |
| `GET` | `/api/availability/hookahs` | Check available hookah models for time window | Public |
| `GET` | `/api/availability/flavours`| Check consumable flavour stock levels | Public |
| `GET` | `/api/availability/slots` | Check delivery slot capacity for given postal code | Public |

### 2.5 Bookings
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `POST` | `/api/bookings` | Create a temporary reservation & lock unit | Authenticated / Bridge |
| `GET` | `/api/bookings/:id` | Get booking reservation details | Authenticated |
| `PATCH`| `/api/bookings/:id` | Modify booking details | Authenticated |
| `POST` | `/api/bookings/:id/cancel` | Cancel booking | Authenticated |

### 2.6 Delivery & Logistics
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `POST` | `/api/delivery/check-zone` | Validate postal code & return delivery pricing | Public |
| `GET` | `/api/delivery/zones` | List supported delivery zones | Public / Staff |
| `GET` | `/api/delivery/slots` | List active delivery time slots | Public |
| `POST` | `/api/delivery/assign` | Assign delivery order to courier staff | Admin |
| `PATCH`| `/api/delivery/:id/status` | Update delivery status (Out, Delivered) | Staff / Admin |

### 2.7 Payments & UPI
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `POST` | `/api/payments/upi/initiate` | Generate dynamic UPI intent / QR code | Authenticated |
| `GET` | `/api/payments/upi/:orderId/status` | Poll payment confirmation status | Authenticated |
| `POST` | `/api/payments/upi/webhook` | Process gateway webhook confirmation | Gateway Signature |

### 2.8 Cash on Delivery (COD) & Admin Reconciliation
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `GET` | `/api/admin/cod/pending` | List uncollected / unreconciled COD orders | Admin |
| `POST` | `/api/admin/cod/:orderId/collect` | Confirm cash collected by staff | Staff / Admin |
| `POST` | `/api/admin/cod/:orderId/partial` | Record partial cash collection | Admin |
| `POST` | `/api/admin/payments/upi/:orderId/reconcile` | Manually reconcile UPI transactions | Admin |

---

## 3. Standard Response Envelope

Successful responses:
```json
{
  "success": true,
  "data": { ... },
  "timestamp": "2026-08-22T12:00:00.000Z"
}
```

Error responses:
```json
{
  "statusCode": 400,
  "timestamp": "2026-08-22T12:00:00.000Z",
  "path": "/api/bookings",
  "method": "POST",
  "error": {
    "message": ["postalCode must be a valid 6-digit postal code"]
  }
}
```
