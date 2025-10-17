# Order Fulfillment System

This repository contains a small order fulfillment system built on Laravel. Core responsibilities implemented in this codebase:

- User authentication (Laravel Sanctum)
- Order placement, items and stock management
- Payment (marking orders as paid)
- Invoice generation (queued jobs creating plain-text invoices)
- Policies and authorization to ensure buyers only see their orders and sellers see their sales

This README covers installation, running the app, API endpoints, queues/invoices, authorization and Postman Guide.

## Quick setup

Prerequisites:

- PHP 8.x, Composer
- MySQL

Installation (project root):

```CMD
composer install
cp .env.example .env  # or copy/edit .env as needed on Windows
php artisan key:generate
```

Configure your `.env` database settings (DB_CONNECTION, DB_DATABASE, DB_USERNAME, DB_PASSWORD). This app's default `.env` uses MySQL.

Run migrations and seeders:

```CMD
php artisan migrate --seed
```

Serve the app (development):

```CMD
php artisan serve --host=127.0.0.1 --port=8000
```


## API - Authentication

Endpoints (base `/api`):

- POST /api/register
	- Request: name, email, password, role (buyer|seller|admin)
	- Response: user object + token

- POST /api/login
	- Request: email, password
	- Response: user object + token

- POST /api/logout (auth:sanctum)
	- Revokes current token

Auth is implemented via an `AuthService` and `UserRepository`. The controller `App\Http\Controllers\Api\AuthController` delegates work to the service.

Usage: include `Authorization: Bearer <token>` header on protected requests.

## API - Orders

Endpoints (protected, use `auth:sanctum`):

- POST /api/order (role: buyer)
	- Place an order (buyer only)

- POST /api/order/{order}/pay (role: buyer)
	- Mark an order as paid (only the buyer who created the order can pay)

- GET /api/orders
	- List orders scoped to the authenticated user:
		- Buyers: their orders
		- Sellers: orders containing their items

- GET /api/orders/{order}
	- Show a single order — guarded by `OrderPolicy::view` (buyers can view own orders, sellers can view if they sold an item in the order)

Controllers delegate business logic to `App\Services\OrderService` which uses `App\Repositories\OrderRepository` and other repositories for data access.



## Authorization (Policies & Role scoping)

- `App\Policies\OrderPolicy` implements `view` and `viewAny`. `view` enforces that only the buyer or any seller who appears in the order items can view an order.
- `viewAny` is implemented to indicate who can request a list (buyers/sellers). Controller/service also scope queries to the authenticated user for defense in depth.

Recommendation: keep `viewAny` as a guard and still scope queries in services/repositories. This provides two-layer protection (policy + query scoping).

## Queues & Invoices

- Jobs are queued using the `database` queue driver by default (see `config/queue.php` and `QUEUE_CONNECTION` in `.env`). Ensure `jobs` and `failed_jobs` tables exist (`php artisan queue:table` + migrate if needed).
- Invoice generation is handled by `App\Jobs\GenerateInvoiceJob` which creates a plain text invoice file and marks the order invoiced.

Important: the application config sets the `local` filesystem disk root to `storage/app/private` (see `config/filesystems.php`). That means calls to `Storage::put('invoices/...')` write to `storage/app/private/invoices`.

To process queued invoice jobs manually (dispatching is done by `orders:process-invoices` command):

```CMD
# Dispatch jobs for unpaid-but-paid-orders
php artisan orders:process-invoices

# Run a queue worker to process jobs:
php artisan queue:work
```

After processing, check `storage/app/private/invoices/` for generated files.

## Notable code-review findings (what I checked)

- Good separation of concerns: services + repositories + jobs + listeners are used consistently.
- `AuthService` + `UserRepository` used by `AuthController` keep auth logic testable.
- `OrderService` encapsulates order creation, stock adjustments, and payment logic.
- `GenerateInvoiceJob` is idempotent and logs success/failure; invoices are plain text files.

Scheduling Notes:

- Scheduling: `AppServiceProvider::boot()` currently calls `Schedule::command('orders:process-invoices')->dailyAt('1:00');`.

## Postman collection

This repo includes a Postman collection and environment to exercise the API endpoints. Files in the repo root:

- `Order Fulfillment System.postman_collection.json` — API collection (requests for register, login, create order, pay, list orders, etc.)
- `Order Fulfillment System ENV Variable.postman_environment.json` — Postman environment with variables (e.g., base URL, token placeholder)

How to use:

1. Open Postman and import the collection (`File > Import` → select `Order Fulfillment System.postman_collection.json`).
2. Import the environment file (`File > Import` → select `Order Fulfillment System ENV Variable.postman_environment.json`).
3. Select the imported environment in the top-right environment selector.
4. Update the `base_url` variable to `http://127.0.0.1:8000/api` 
5. Use `Login` to receive a token and Go on.
6. Run requests like `Create Order`, `Pay Order`, `List Orders` using the token in the Authorization header.



