# Auto-Expire Pending Orders — Design

Status: approved, pending implementation plan.
Related: `docs/superpowers/specs/2026-06-19-toko-online-design.md` §6 (Integrasi Midtrans — "Job scheduler: auto-expire order `pending` yang lewat batas waktu Snap token, release stock balik.").

## 1. Tujuan

Order yang dibuat tapi ditinggal user (gak jadi bayar) tetap berstatus `pending` selamanya, karena Midtrans hanya kirim webhook `expire`/`cancel` kalau user interaksi dengan popup Snap. Order yang diabaikan sama sekali (popup tidak pernah dibuka, atau user tutup browser sebelum popup terbuka) tidak pernah dapat webhook — stock tetap ter-lock di order itu selamanya.

Job ini expire semua order `pending` yang sudah melewati batas waktu Snap token (default 24 jam), dan release stock-nya kembali ke `product_variants`.

## 2. Scope

**In:** Command + scheduler + config key + test.

**Out:** Notifikasi ke customer saat order expired (tidak diminta di spec, YAGNI). Retry payment setelah expired sudah di-handle storefront via `POST /orders/{order_number}/snap-token` yang generate token baru.

## 3. Arsitektur

```
[Laravel Scheduler — setiap jam]
  → php artisan orders:expire-pending
  → Query: orders WHERE status='pending' AND created_at <= now() - ORDER_EXPIRE_HOURS
  → foreach order:
      order.status = 'expired'
      order.save()
      order.releaseStockIfApplicable('pending', 'expired')  ← method dari OrderResource Task 1
  → Log::info("Expired N pending orders.")
```

### Kenapa reuse `releaseStockIfApplicable`?

`Order::releaseStockIfApplicable(string $oldStatus, string $newStatus): void` sudah ada di `app/Models/Order.php` — ditulis saat membangun admin OrderResource agar logik release-stock tidak duplikat antara Midtrans webhook dan admin action. Job ini adalah caller ketiga dengan logik yang sama: `$oldStatus === 'pending'` dan `$newStatus === 'expired'` → memenuhi guard, stock ter-release.

## 4. Files

- **Create:** `api/app/Console/Commands/ExpirePendingOrders.php`
- **Modify:** `api/config/midtrans.php` — tambah key `order_expire_hours`
- **Modify:** `api/.env.example` — tambah `ORDER_EXPIRE_HOURS=24`
- **Modify:** `api/routes/console.php` — tambah schedule hourly via `Schedule::command(...)->hourly()`
- **Test:** `api/tests/Feature/Order/ExpirePendingOrdersCommandTest.php`

## 5. Config

`config/midtrans.php` (tambah):
```php
'order_expire_hours' => (int) env('ORDER_EXPIRE_HOURS', 24),
```

`.env.example` (tambah):
```
ORDER_EXPIRE_HOURS=24
```

## 6. Command Logic (lengkap)

```php
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

Eager-load `items.variant` di query untuk efisiensi — menghindari N+1 di `releaseStockIfApplicable` yang melakukan `$this->items()->with('variant')->get()` (ini akan hit cache relation yg sudah loaded).

> **Catatan eager-load:** `releaseStockIfApplicable` saat ini selalu melakukan fresh query (`$this->items()->with('variant')->get()`), sehingga eager-load di command ini redundan untuk saat ini (tidak merusak, hanya tidak di-reuse). Ini trade-off yang disadari: method mempertahankan kemandirian caller-agnostik demi kesederhanaan. Jika performa menjadi concern di masa depan (ribuan orders sekaligus), opsi adalah refactor untuk menerima pre-loaded collection — bukan scope task ini.

## 7. Scheduler

`routes/console.php` (tambah):
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('orders:expire-pending')->hourly();
```

Frekuensi hourly cukup — threshold 24 jam, presisi 1 jam masih jauh lebih granular dari kebutuhan. Tidak perlu lebih sering.

## 8. Testing

`tests/Feature/Order/ExpirePendingOrdersCommandTest.php` — 4 test cases:

1. **Order pending melewati threshold → ter-expire dan stock ter-release.**
   Setup: order `status=pending`, `created_at = 25 jam lalu`, variant stock=8, qty=2.
   Assert: status `expired`, stock=10.

2. **Order pending belum melewati threshold → tidak disentuh.**
   Setup: order `status=pending`, `created_at = now()` (baru saja).
   Assert: status tetap `pending`.

3. **Order non-pending (mis. `paid`) yang umurnya > threshold → tidak disentuh.**
   Setup: order `status=paid`, `created_at = 25 jam lalu`.
   Assert: status tetap `paid`.

4. **Idempotency — jalankan 2x → order yang sudah expired tidak di-proses ulang (stock tidak double-release).**
   Setup: order `status=pending`, `created_at = 25 jam lalu`, stock=8, qty=2.
   Jalankan command 2x.
   Assert: stock=10 (bukan 12 — tidak double-increment).
