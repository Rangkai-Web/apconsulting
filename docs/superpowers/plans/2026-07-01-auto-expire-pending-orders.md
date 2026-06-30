# Auto-Expire Pending Orders Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build an Artisan command `orders:expire-pending` that automatically expires stale pending orders past the Midtrans Snap token window and releases their reserved stock, registered to run hourly via Laravel Scheduler.

**Architecture:** A single `ExpirePendingOrders` command class queries `orders` for `status=pending` rows older than `ORDER_EXPIRE_HOURS` (configurable, default 24), sets each to `expired`, and calls the already-existing `Order::releaseStockIfApplicable('pending', 'expired')` to return stock. The scheduler is registered via `->withSchedule()` in `bootstrap/app.php` (Laravel 11/13 standard); the command class is auto-discovered by adding `->withCommands()` to the same file.

**Tech Stack:** Laravel 13 Artisan command + `Illuminate\Console\Scheduling\Schedule`, PHPUnit `$this->artisan()` test helper, `Carbon` for time manipulation in tests.

## Global Constraints

- Spec: `docs/superpowers/specs/2026-07-01-auto-expire-pending-orders-design.md`.
- `Order::releaseStockIfApplicable(string $oldStatus, string $newStatus): void` already exists at `api/app/Models/Order.php` — this plan calls it, never reimplements it.
- Config key `order_expire_hours` lives in `config/midtrans.php` (Midtrans snap token lifecycle domain). Env var: `ORDER_EXPIRE_HOURS`, default `24`.
- Command signature: `orders:expire-pending`. No arguments, no options — runs unconditionally against the threshold.
- Scheduler frequency: hourly (`->hourly()`). No finer granularity needed.
- Run tests with: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test` (run from `api/`).

---

### Task 1: `ExpirePendingOrders` command + scheduler + tests

**Files:**
- Create: `api/app/Console/Commands/ExpirePendingOrders.php`
- Modify: `api/bootstrap/app.php` (add `->withCommands()` + `->withSchedule(...)`)
- Modify: `api/config/midtrans.php` (add `order_expire_hours` key)
- Modify: `api/.env.example` (add `ORDER_EXPIRE_HOURS=24`)
- Test: `api/tests/Feature/Order/ExpirePendingOrdersCommandTest.php`

**Interfaces:**
- Consumes: `App\Models\Order::releaseStockIfApplicable(string $oldStatus, string $newStatus): void` (existing, `api/app/Models/Order.php`). `config('midtrans.order_expire_hours')` (added in this task).
- Produces: `php artisan orders:expire-pending` — no output contract; side-effect only (DB updates + `Log::info`). Scheduler registration runs the command hourly in production.

- [ ] **Step 1: Write the failing test**

Create `api/tests/Feature/Order/ExpirePendingOrdersCommandTest.php`:

```php
<?php

namespace Tests\Feature\Order;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ExpirePendingOrdersCommandTest extends TestCase
{
    use RefreshDatabase;

    private function createOrderWithItem(string $status): array
    {
        $category = Category::create(['name' => 'Skincare', 'slug' => 'skincare']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sunscreen',
            'slug' => 'sunscreen',
            'description' => 'Test',
            'images' => ['sunscreen.png'],
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '50ml',
            'sku' => 'SUN-50',
            'price' => 100000,
            'stock' => 8,
            'weight' => 200,
        ]);
        $order = Order::create([
            'order_number' => 'ORD-EXPIRE-' . uniqid(),
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => $status,
        ]);
        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'price' => 100000,
            'qty' => 2,
            'subtotal' => 200000,
        ]);

        return [$order, $variant];
    }

    public function test_expires_pending_orders_past_threshold_and_releases_stock(): void
    {
        [$order, $variant] = $this->createOrderWithItem('pending');
        Order::where('id', $order->id)->update(['created_at' => Carbon::now()->subHours(25)]);

        $this->artisan('orders:expire-pending');

        $this->assertEquals('expired', $order->fresh()->status);
        $this->assertEquals(10, $variant->fresh()->stock);
    }

    public function test_does_not_expire_pending_orders_within_threshold(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-YOUNG',
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => 'pending',
        ]);

        $this->artisan('orders:expire-pending');

        $this->assertEquals('pending', $order->fresh()->status);
    }

    public function test_does_not_touch_non_pending_orders_even_if_old(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-PAID-OLD',
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => 'paid',
        ]);
        Order::where('id', $order->id)->update(['created_at' => Carbon::now()->subHours(25)]);

        $this->artisan('orders:expire-pending');

        $this->assertEquals('paid', $order->fresh()->status);
    }

    public function test_idempotent_does_not_double_release_stock_on_second_run(): void
    {
        [$order, $variant] = $this->createOrderWithItem('pending');
        Order::where('id', $order->id)->update(['created_at' => Carbon::now()->subHours(25)]);

        $this->artisan('orders:expire-pending');
        $this->artisan('orders:expire-pending');

        $this->assertEquals('expired', $order->fresh()->status);
        $this->assertEquals(10, $variant->fresh()->stock); // 8 + 2 = 10, not 12 — no double release
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=ExpirePendingOrdersCommandTest`
Expected: FAIL — `Command "orders:expire-pending" is not defined.`

- [ ] **Step 3: Add config key**

Read `api/config/midtrans.php` first. Add `order_expire_hours` to the returned array:

```php
<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'order_expire_hours' => (int) env('ORDER_EXPIRE_HOURS', 24),
];
```

- [ ] **Step 4: Add env placeholder**

Append to `api/.env.example` (after `MIDTRANS_IS_PRODUCTION=false`):

```dotenv
ORDER_EXPIRE_HOURS=24
```

- [ ] **Step 5: Create the command class**

Create `api/app/Console/Commands/ExpirePendingOrders.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire-pending';

    protected $description = 'Expire pending orders that have passed the Snap token expiry window and release their reserved stock.';

    public function handle(): void
    {
        $threshold = now()->subHours((int) config('midtrans.order_expire_hours'));

        $orders = Order::where('status', 'pending')
            ->where('created_at', '<=', $threshold)
            ->with('items.variant')
            ->get();

        foreach ($orders as $order) {
            $order->status = 'expired';
            $order->save();
            $order->releaseStockIfApplicable('pending', 'expired');
        }

        Log::info("orders:expire-pending: expired {$orders->count()} orders.");
    }
}
```

- [ ] **Step 6: Register command auto-discovery and scheduler in `bootstrap/app.php`**

Read `api/bootstrap/app.php` first. Replace the full file with:

```php
<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands()
    ->withSchedule(fn (Schedule $schedule) => $schedule->command('orders:expire-pending')->hourly())
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
```

`->withCommands()` with no arguments auto-discovers command classes in `app/Console/Commands/`. This coexists with `withRouting(commands: ...)` which loads the closure-based commands in `routes/console.php` — both register independently through the `ConsoleKernel`.

- [ ] **Step 7: Run test to verify it passes**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=ExpirePendingOrdersCommandTest`
Expected: PASS (4 tests).

- [ ] **Step 8: Run the full suite**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test`
Expected: PASS, no regressions.

- [ ] **Step 9: Commit**

```bash
git add api/app/Console/Commands/ExpirePendingOrders.php api/bootstrap/app.php api/config/midtrans.php api/.env.example api/tests/Feature/Order/ExpirePendingOrdersCommandTest.php
git commit -m "feat(api): add orders:expire-pending scheduled command to auto-expire stale pending orders"
```

---

## Verification Plan

### Automated Tests
Run inside `api/`:
```bash
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=ExpirePendingOrdersCommandTest
```
Covers: threshold boundary (old pending → expired + stock released), young pending untouched, non-pending status untouched, idempotency (no double stock release on second run).

### Manual Verification
1. `php artisan orders:expire-pending` — confirm it runs without error.
2. `php artisan schedule:list` — confirm `orders:expire-pending` appears with "Hourly" frequency.
3. To verify the scheduler is wired: `php artisan schedule:run` — in production this is invoked by cron `* * * * * php /path/to/artisan schedule:run`. Hostinger (the deployment target per spec §8) will need a cron job entry pointing to this command. Note in CLAUDE.md or deployment docs if that isn't already documented.
