# Email Notifikasi Order Baru Dibayar Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Send an email to all admin accounts when a new order is paid, via Resend mail provider, alongside the existing Filament bell notification.

**Architecture:** A `OrderPaidNotification` Laravel Notification class handles the `mail` channel only (Filament's `sendToDatabase` for the bell stays untouched). In `WebhookController`, a `foreach ($admins as $admin)` loop calls `$admin->notify(new OrderPaidNotification($order))` inside the existing try/catch, right after the Filament sendToDatabase call. Resend is wired via Laravel's built-in Resend transport (requires installing `resend/resend-php`).

**Tech Stack:** Laravel 13 Notifications (`Illuminate\Notifications\Notification`, `Illuminate\Notifications\Messages\MailMessage`), `resend/resend-php` ^1.0, PHPUnit `Notification::fake()`.

## Global Constraints

- Spec: `docs/superpowers/specs/2026-07-01-email-notification-order-paid-design.md`.
- Notification class: `App\Notifications\OrderPaidNotification`. Method `via()` returns `['mail']` — NOT `['database', 'mail']` (Filament DB notification stays separate).
- Mail provider: Resend. Config key is `RESEND_API_KEY` (set in `config/services.php:resend.key`). **Not** `RESEND_KEY`.
- Email must NOT fail silently without logging, and its failure must NOT abort the webhook response to Midtrans — the existing try/catch in `WebhookController` already handles this; place the email loop inside it.
- Existing Filament notification block in `WebhookController` is **not modified** — only add a foreach email loop after the existing `sendToDatabase` call.
- `phpunit.xml` already sets `MAIL_MAILER=array` for tests — no real emails sent during test runs. `Notification::fake()` intercepts `$admin->notify()` calls.
- Run tests with: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test` (run from `api/`).

---

### Task 1: `OrderPaidNotification` + Resend setup + webhook wiring + tests

**Files:**
- Install: `resend/resend-php` via `composer require resend/resend-php` (from `api/`)
- Create: `api/app/Notifications/OrderPaidNotification.php`
- Modify: `api/app/Http/Controllers/Api/WebhookController.php` — add foreach email loop inside existing try/catch
- Modify: `api/.env.example` — add Resend env vars
- Test: `api/tests/Feature/Order/OrderPaidEmailNotificationTest.php`

**Interfaces:**
- Consumes: `App\Models\Order` (existing: `order_number`, `total`, `id`, `user` relation, `guest_name`). `App\Models\Admin` (existing, has `Notifiable` trait + `email` field).
- Produces: `App\Notifications\OrderPaidNotification` — instantiated with `new OrderPaidNotification(Order $order)`, dispatched via `$admin->notify(...)`.

- [ ] **Step 1: Write the failing test**

Create `api/tests/Feature/Order/OrderPaidEmailNotificationTest.php`:

```php
<?php

namespace Tests\Feature\Order;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Notifications\OrderPaidNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderPaidEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createOrder(string $status, string $orderId = 'ORD-TEST-001'): Order
    {
        return Order::create([
            'order_number' => $orderId,
            'guest_name' => 'Guest Tester',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest Tester'],
            'courier' => 'jne',
            'courier_service' => 'REG',
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => $status,
        ]);
    }

    public function test_sends_email_notification_to_all_admins_when_order_paid(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create();
        $order = $this->createOrder('pending', 'ORD-PAY-001');

        $this->postJson('/api/webhooks/midtrans', [
            'order_id' => $order->order_number,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '215000.00',
            'signature_key' => 'dummy',
        ]);

        Notification::assertSentTo($admin, OrderPaidNotification::class);
    }

    public function test_does_not_send_email_notification_for_non_paid_status(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create();
        $order = $this->createOrder('pending', 'ORD-PENDING-001');

        $this->postJson('/api/webhooks/midtrans', [
            'order_id' => $order->order_number,
            'transaction_status' => 'pending',
            'status_code' => '200',
            'gross_amount' => '215000.00',
            'signature_key' => 'dummy',
        ]);

        Notification::assertNothingSent();
    }

    public function test_webhook_succeeds_even_when_no_admin_exists(): void
    {
        Notification::fake();

        $order = $this->createOrder('pending', 'ORD-NOADMIN-001');

        $response = $this->postJson('/api/webhooks/midtrans', [
            'order_id' => $order->order_number,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '215000.00',
            'signature_key' => 'dummy',
        ]);

        $response->assertStatus(200);
        Notification::assertNothingSent();
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=OrderPaidEmailNotificationTest`
Expected: FAIL — `Class "App\Notifications\OrderPaidNotification" not found`.

- [ ] **Step 3: Install Resend PHP package**

Run from `api/`:
```bash
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && composer require resend/resend-php
```
Expected: `Package operations: 1 install` (or similar). This installs the `Resend\Client` class that Laravel's built-in `ResendTransport` depends on.

- [ ] **Step 4: Create `OrderPaidNotification`**

Create `api/app/Notifications/OrderPaidNotification.php`:

```php
<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPaidNotification extends Notification
{
    public function __construct(private Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $customerName = $this->order->user?->name ?? $this->order->guest_name ?? 'Guest';
        $total = 'Rp ' . number_format($this->order->total, 0, ',', '.');
        $adminUrl = rtrim(config('app.url'), '/') . '/admin/orders/' . $this->order->id;

        return (new MailMessage)
            ->subject('Pesanan Baru Dibayar — ' . $this->order->order_number)
            ->greeting('Halo Admin,')
            ->line('Pesanan baru telah berhasil dibayar.')
            ->line('**No. Pesanan:** ' . $this->order->order_number)
            ->line('**Customer:** ' . $customerName)
            ->line('**Total:** ' . $total)
            ->action('Lihat Pesanan', $adminUrl)
            ->salutation('DR Plant Indonesia Admin');
    }
}
```

- [ ] **Step 5: Add email loop to `WebhookController`**

Read `api/app/Http/Controllers/Api/WebhookController.php` first. Add two things:

**Import** (add after existing imports at top of file):
```php
use App\Notifications\OrderPaidNotification;
```

**Email loop** — add inside the existing `if ($newStatus === 'paid' && $oldStatus !== 'paid')` try/catch block, after the existing `sendToDatabase` call. The block currently ends with:

```php
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim database notification ke Filament admin: ' . $e->getMessage());
            }
```

The full block after modification should look like:

```php
        // Filament notifications to Admin on successful payment
        if ($newStatus === 'paid' && $oldStatus !== 'paid') {
            $admins = Admin::all();

            // Filament bell notification (separate try/catch so its failure doesn't block email)
            try {
                if ($admins->count() > 0 && class_exists(Notification::class)) {
                    Notification::make()
                        ->title('Pesanan Baru Dibayar')
                        ->body("Pesanan {$order->order_number} sebesar Rp " . number_format($order->total) . " telah berhasil dibayar.")
                        ->success()
                        ->sendToDatabase($admins);
                }
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim Filament database notification ke admin: ' . $e->getMessage());
            }

            // Email notification via Resend
            try {
                foreach ($admins as $admin) {
                    $admin->notify(new OrderPaidNotification($order));
                }
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim email notification ke admin: ' . $e->getMessage());
            }
        }
```

**Kenapa 2 try/catch terpisah:** Filament's `sendToDatabase` mungkin throw exception (misal notifications table belum ada) — kalau digabung 1 try/catch, exception dari Filament akan mencegah foreach email di-execute. Dengan 2 try/catch, keduanya independent: failure satu tidak memblokir yang lain. `$admins` di-fetch sekali di luar dan di-share ke kedua block.

- [ ] **Step 6: Update `.env.example`**

Add after `MAIL_FROM_NAME="${APP_NAME}"`:

```dotenv
# Resend (production mail provider — get key from resend.com dashboard)
RESEND_API_KEY=
```

Also change the `MAIL_MAILER=log` line to:

```dotenv
MAIL_MAILER=resend
```

And update `MAIL_FROM_ADDRESS` to a placeholder that reminds client to use a Resend-verified domain:

```dotenv
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
```

- [ ] **Step 7: Run test to verify it passes**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=OrderPaidEmailNotificationTest`
Expected: PASS (3 tests).

- [ ] **Step 8: Run the full suite**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test`
Expected: PASS, no regressions.

- [ ] **Step 9: Commit**

```bash
git add api/app/Notifications/OrderPaidNotification.php api/app/Http/Controllers/Api/WebhookController.php api/.env.example api/tests/Feature/Order/OrderPaidEmailNotificationTest.php api/composer.json api/composer.lock
git commit -m "feat(api): add email notification to admin on order paid via Resend"
```

---

## Verification Plan

### Automated Tests
Run inside `api/`:
```bash
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=OrderPaidEmailNotificationTest
```
Covers: email sent to admin on paid, email NOT sent on non-paid status, webhook succeeds even with no admin (edge case).

### Manual Verification (requires Resend API key)
1. Set `MAIL_MAILER=resend` and `RESEND_API_KEY=re_xxx` in `api/.env`.
2. Set `MAIL_FROM_ADDRESS` to a domain verified in Resend dashboard.
3. Trigger a test webhook via `POST /api/webhooks/midtrans` with a pending order and `transaction_status=settlement`.
4. Verify email arrives at admin's inbox.

**Note:** `RESEND_API_KEY` is provided by the client after signing up at resend.com. The code is fully operational once this key is set — no further code changes required.
