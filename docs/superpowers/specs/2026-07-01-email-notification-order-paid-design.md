# Email Notifikasi Order Baru Dibayar — Design

Status: approved, pending implementation plan.
Related: `docs/superpowers/specs/2026-06-19-toko-online-design.md` §4 (Flow Admin — "Notifikasi order baru: ... dan email. Admin gak boleh baru tau ada order pas buka dashboard manual.").

## 1. Tujuan

Saat ini `WebhookController` sudah mengirim Filament database notification (muncul di ikon bel panel) ketika order masuk `paid`. Spec §4 mewajibkan juga pengiriman **email** ke admin. Tanpa email, admin bisa melewatkan order baru kalau tidak sedang membuka panel.

## 2. Scope

**In:** `OrderPaidNotification` class (mail channel only), integrasi di `WebhookController`, Resend mail provider setup, tests.

**Out:** HTML email template fancy (pakai default Laravel MailMessage markdown, cukup untuk scope toko ini). Queued notification (sync delivery, single admin toko kecil). Mengubah Filament database notification yang sudah berjalan.

## 3. Arsitektur

```
WebhookController::midtrans()
  → status = 'paid' (existing trigger)
  → [existing] Filament::Notification::make()->sendToDatabase($admins)  ← tidak diubah
  → [NEW] foreach $admins: $admin->notify(new OrderPaidNotification($order))
                                         ↓
                              via: ['mail']
                              toMail(): MailMessage
```

`OrderPaidNotification` hanya menangani `mail` channel — bukan `database` karena Filament DB notification menggunakan format tersendiri yang berbeda dari Laravel's built-in toDatabase(). Menggabungkan keduanya dalam satu Notification class berisiko merusak Filament bell UI.

Admin model sudah punya `use Notifiable` trait (`app/Models/Admin.php`) — tidak ada perubahan pada model.

## 4. Mail Provider: Resend

Package: `resend/resend-laravel` (Composer, official Resend Laravel SDK).

Konfigurasi (`.env.example` dan `.env`):
```dotenv
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="noreply@domainanda.com"
MAIL_FROM_NAME="${APP_NAME}"
RESEND_KEY=re_xxxxxxxxxxxxxxxx
```

`RESEND_KEY` diisi oleh client dari Resend dashboard. Sampai diisi, email tidak terkirim (Resend SDK akan melempar exception, ditangkap oleh try/catch yang sudah ada di WebhookController — email gagal tidak boleh menggagalkan proses webhook).

## 5. `OrderPaidNotification`

```php
namespace App\Notifications;

class OrderPaidNotification extends Notification
{
    public function __construct(private Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $customerName = $this->order->user?->name ?? $this->order->guest_name ?? 'Guest';
        $total = 'Rp ' . number_format($this->order->total, 0, ',', '.');
        $adminUrl = config('app.url') . '/admin/orders/' . $this->order->id;

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

## 6. Perubahan `WebhookController`

Existing block di baris 97-110 (setelah `$newStatus === 'paid'` check) dipertahankan seluruhnya. Tambahkan foreach email di dalam blok try/catch yang sama, setelah `sendToDatabase`:

```php
if ($newStatus === 'paid' && $oldStatus !== 'paid') {
    try {
        $admins = Admin::all();
        if ($admins->count() > 0 && class_exists(Notification::class)) {
            Notification::make()
                ->title('Pesanan Baru Dibayar')
                ->body("Pesanan {$order->order_number} sebesar Rp " . number_format($order->total) . " telah berhasil dibayar.")
                ->success()
                ->sendToDatabase($admins);
        }

        // [NEW] Email notification via Resend
        foreach ($admins as $admin) {
            $admin->notify(new OrderPaidNotification($order));
        }
    } catch (\Throwable $e) {
        Log::error('Gagal mengirim notifikasi ke Filament admin: ' . $e->getMessage());
    }
}
```

Email failure (exception dari Resend mis. invalid API key, network error) ditangkap oleh try/catch yang sama dan hanya di-log — tidak menggagalkan response webhook ke Midtrans. Idempotency sudah dijamin oleh guard `$newStatus === 'paid' && $oldStatus !== 'paid'` yang diwarisi dari logic existing.

## 7. Testing

`tests/Feature/Order/OrderPaidEmailNotificationTest.php` — menggunakan `Notification::fake()`:

1. **Order baru masuk `paid` → `OrderPaidNotification` dikirim via `mail` ke semua admin.** Assert `Notification::assertSentTo($admin, OrderPaidNotification::class)`.

2. **Order masuk status selain `paid` (misal `processing`) → `OrderPaidNotification` tidak dikirim.** Assert `Notification::assertNothingSent()`.

3. **No admin exists → webhook runs without error, no notification sent.** Edge case: `Admin::all()` returns empty collection.

## 8. Out of scope

- Mengubah format atau konten Filament database notification yang sudah ada.
- HTML template email custom (laravel/markdown default cukup).
- Queue untuk notification ini (sync delivery, acceptable untuk volume toko kecil).
- Retry logic untuk email gagal.
