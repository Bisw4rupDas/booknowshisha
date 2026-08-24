# Hookah Rental Core Plugin

Custom WordPress / WooCommerce integration plugin designed to connect the BookNowShisha digital storefront with the NestJS API backend.

## Plugin Structure

```
hookah-rental-core/
├── hookah-rental-core.php    # Main plugin loader & activation hooks
├── includes/                 # Core orchestrator and lifecycle classes
├── api/                      # Authenticated client for NestJS REST API
├── woo/                      # WooCommerce product, cart & checkout hooks
├── rental/                   # Rental calculation, shortcodes & widgets
├── checkout/                 # Custom checkout fields (Date, Slot, 21+ Verification)
├── availability/             # Real-time AJAX availability checks
├── admin/                    # Admin settings page in WP-Admin
├── assets/                   # Public and admin CSS/JS assets
├── templates/                # Frontend template parts & booking widgets
└── README.md
```

## Configuration
In the WordPress Admin:
1. Navigate to **Hookah Rentals** in the sidebar.
2. Configure the **NestJS API Base URL** (e.g. `http://backend:3000/api` or `http://localhost:3000/api`).
3. Set the **Shared API Secret** corresponding to `HOOKAH_RENTAL_CORE_SHARED_SECRET`.
