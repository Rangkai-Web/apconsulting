# BbystarGO — Toko Online

Monorepo: `api/` (Laravel 13 + Filament v5 admin) + `storefront/` (Nuxt 4 SPA).
Full design: `docs/superpowers/specs/2026-06-19-toko-online-design.md`.
Storefront UI/UX guidelines: `docs/superpowers/specs/2026-06-19-storefront-ui-design.md`.
Implementation plans: `docs/superpowers/plans/`.
REST API reference (kept in sync per endpoint task): `docs/api/`.

## Stack
- Backend: Laravel 13, PHP 8.3+ (installed via Homebrew at `/opt/homebrew/bin/php`), MySQL, PHPUnit for tests, Sanctum (customer auth, `users` guard), separate `admin` guard + `admins` table for Filament v5.
- Frontend: Nuxt 4.4.x, `ssr: false` (SPA only — no Node runtime in production), Nuxt UI v4 component library, Pinia for cart/auth state, Vitest for tests.
- Payment: manual bank/e-wallet transfer, admin-verified in Filament (see `docs/api/orders.md`, `docs/api/payment-methods.md`). Shipping: RajaOngkir, single origin warehouse.

## Command Guidelines
- Run tests: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test`
- Run single test: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=TestClassName`
- Run migrations: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan migrate`
- List scheduled commands: `php artisan schedule:list`

## Deployment Note — Laravel Scheduler

The app uses `php artisan schedule:run` (registered in `bootstrap/app.php` via `->withSchedule()`). In production (Hostinger), a cron entry is required to invoke the scheduler every minute:

```bash
* * * * * cd /path/to/api && php artisan schedule:run >> /dev/null 2>&1
```

Without this cron entry, `orders:expire-pending` (and any future scheduled commands) will never run, regardless of how they are registered in code.

## Conventions
- Snapshot fields (`order_items.product_name/variant_name/price`) are intentional — never replace with a live join to `products`/`product_variants`. Order history must not change retroactively.
- Cart has no database table by design — it is client-side only (Pinia + localStorage).
- Customer and admin auth never share a table or guard. Don't merge `users` and `admins`.
- RajaOngkir `/cost` endpoint is billed per call — never call it outside an explicit user-initiated "Cek Ongkir" action. Area lookups (province/city/district) must come from the local `rajaongkir_*` cache tables, never a live API call.
- Any task that adds or changes a `/api/...` endpoint must update the matching file in `docs/api/` (one file per resource: `auth.md`, `catalog.md`, `cart-checkout.md`, `orders.md`, `shipping.md`). Plain markdown, not OpenAPI — method+path, auth requirement, example request/response, status codes.
- Product images: max 5 per product (`products.images` json array), max 2MB per file, jpg/png/webp only — enforced in the Filament `FileUpload` field, not at the DB layer. Variant image is a single optional file, same limits; storefront falls back to the product's first image when a variant has none.
- Storefront pages requiring a logged-in account (e.g. saved-address management) use an `auth` route middleware; guest checkout is allowed everywhere else, so don't gate checkout/cart behind `auth`. Login/register pages use a `guest-only` middleware that redirects already-authenticated users home.

## Build status tracking
Each implementation plan in `docs/superpowers/plans/` tracks its own task checkboxes. A task's checkbox being checked means that code is considered done and locked — don't regenerate or refactor a completed task's files unless the user explicitly asks for a change. Check the relevant plan file before modifying any file under `api/` or `storefront/`.
