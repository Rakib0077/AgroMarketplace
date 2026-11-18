# Database Setup

Requirements: MySQL 8+, PHP with `pdo_mysql`, and a web server that executes PHP.

Create the database and tables:

```sh
mysql -u root -p < database/schema.sql
```

Existing databases created before listing photos were added need this one-time migration:

```sh
mysql -u root -p < database/migrations/002_listing_images.sql
```

Existing databases also need the one-time pending registration approval migration:

```sh
mysql -u root -p < database/migrations/003_pending_registration_approval.sql
```

Existing databases also need the one-time commission and delivery accounting migration:

```sh
mysql -u root -p < database/migrations/004_delivery_commission.sql
```

Existing databases also need the one-time delivery damage-report migration:

```sh
mysql -u root -p < database/migrations/005_delivery_damage_reports.sql
```

The PHP/web-server user must be able to write to `assets/listings/` for photo uploads. Uploads are limited to JPEG, PNG, or WebP images up to 5 MB.

Load repeatable demo data after creating the schema:

```sh
mysql -u root -p < database/sample-data.sql
```

The sample buyer, farmer, and delivery accounts all use `DemoPass123!`. These are local demonstration accounts only; change or remove them before deploying the application.

The app defaults to `localhost:3306`, database `agromarketplace`, and the existing local development credentials. Set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS` in the PHP process environment for other installations. Do not use the development credentials in production.

Create the first administrator from the project root. This command is CLI-only; public account registration cannot create administrators:

```sh
php database/create-admin.php admin admin@example.com 'use-a-long-unique-password'
```

Buyer, farmer, and delivery users can register at `/auth/register.php`. The connected core flows are listings, inventory, bids, orders, invoices, delivery jobs, profile updates, and order conversations. Payment processing, OTP/SMS delivery, route tracking, and several remaining prototype panels do not have external service integrations yet.
