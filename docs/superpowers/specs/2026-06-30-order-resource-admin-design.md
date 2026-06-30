# Admin Order Management (`OrderResource`) — Design

Status: approved, pending implementation plan.
Related: `docs/superpowers/specs/2026-06-19-toko-online-design.md` §4 (Flow Admin — Pesanan).

## 1. Tujuan

Admin saat ini punya nol visibility ke pesanan masuk — `php artisan test` 72/72 pass dan checkout end-to-end sudah jalan dari sisi customer, tapi tidak ada Filament resource untuk melihat order, konfirmasi, atau input nomor resi. Ini blocker operasional: toko bisa terima pembayaran tapi admin tidak bisa memprosesnya.

Scope ini HANYA admin order management (Filament). Tidak termasuk: scheduled job auto-expire order, email notification — itu task terpisah.

## 2. Halaman & Komponen

### List page (`ListOrders`)

Tabel, kolom:
- `order_number` — searchable
- Customer — computed: `user.name` jika member, else `guest_name` + badge "Guest"
- `status` — badge warna per status (pending=gray, paid=info, processing=warning, shipped=primary, completed=success, cancelled/expired=danger)
- `total` — format Rupiah
- `courier` + `courier_service`
- `created_at`

Filter: `status` (SelectFilter, semua 7 status). Default sort: `created_at` desc. Tidak ada Create action (order hanya dibuat lewat API checkout). Tidak ada Edit action — klik row buka View page.

### View page (`ViewOrder`) — read-only infolist

Items, harga, alamat adalah snapshot per convention project (`CLAUDE.md`: "Snapshot fields ... never replace with a live join ... Order history must not change retroactively") — infolist ini **read-only total**, tidak ada form edit di halaman ini.

Sections:
1. **Informasi Pesanan** — order_number, status badge, created_at, courier+courier_service, tracking_number, shipped_at, completed_at.
2. **Pelanggan** — kalau `user_id` ada: `user.name`/`user.email`/`user.phone`. Kalau null: `guest_name`/`guest_email`/`guest_phone` + badge "Guest".
3. **Alamat Pengiriman** — dari kolom `shipping_address` (json). Field yang dirender (key persis sesuai `OrderController::store()`): `recipient_name`, `phone`, `full_address`, `province`, `city`, `district`, `postal_code`.
4. **Item Pesanan** — `RepeatableEntry` dari relasi `Order::items()`: `product_name`, `variant_name`, `qty`, `price`, `subtotal`. Diikuti ringkasan `subtotal`/`shipping_cost`/`total` order.

Header action: **"Update Status"** (lihat §3).

## 3. Action "Update Status"

Modal form, header action di View page:
- `Select` field `status` — 7 opsi (`pending`, `paid`, `processing`, `shipped`, `completed`, `cancelled`, `expired`), default = status order saat ini. **Tidak ada pembatasan transisi** — admin bisa pilih status apapun (keputusan eksplisit: spec utama §4 bilang "Admin bisa override status manual untuk edge case").
- `TextInput` field `tracking_number` — visible & required hanya kalau `status` yang dipilih di form (bukan status order saat ini) `=== 'shipped'`.

Submit logic:
```
oldStatus = order.status
order.status = data.status
if data.status === 'shipped' and tracking_number diisi: order.tracking_number = data.tracking_number
if data.status === 'shipped' and order.shipped_at masih null: order.shipped_at = now()
if data.status === 'completed' and order.completed_at masih null: order.completed_at = now()
order.save()
order.releaseStockIfApplicable(oldStatus, data.status)
```

## 4. Stock-release reuse (refactor `WebhookController`)

`WebhookController.php:86-94` saat ini punya inline loop release-stock dengan guard `in_array($newStatus, ['cancelled','expired']) && $oldStatus === 'pending'`. Extract jadi method baru di `Order` model:

```php
public function releaseStockIfApplicable(string $oldStatus, string $newStatus): void
{
    if (! in_array($newStatus, ['cancelled', 'expired']) || $oldStatus !== 'pending') {
        return;
    }

    foreach ($this->items()->with('variant')->get() as $item) {
        if ($item->variant) {
            $item->variant->increment('stock', $item->qty);
        }
    }

    Log::info("Released stock for cancelled/expired order {$this->order_number}.");
}
```

`WebhookController::midtrans()` ganti baris 86-94 jadi `$order->releaseStockIfApplicable($oldStatus, $newStatus);` — perilaku identik, sekarang dipakai juga oleh Update Status action di admin. Guard `$oldStatus === 'pending'` dipertahankan exact sama untuk admin action — artinya kalau admin cancel order yang udah `paid`/`processing`, stock TIDAK auto-release (sama seperti webhook, by design — refund order yang udah dibayar adalah proses manual terpisah, bukan auto-release).

## 5. Akses

`OrderResource` hanya muncul di Filament admin panel (guard `admin`, sudah ada infrastruktur panel ini dari resource lain). Tidak ada akses tambahan yang perlu dikonfigurasi — ikut pola existing `ProductResource`/`CategoryResource`/`BannerResource` (1 role admin, no RBAC, sesuai spec utama §10 "Scope yang Dicoret").

## 6. Testing

`tests/Feature/Order/OrderResourceTest.php` (baru):
- Admin authenticated bisa akses list page (200) dan view page (200) untuk order tertentu.
- Guest/unauthenticated request ke route admin order di-redirect (Filament panel auth, bukan API 401/403).
- Update Status action: ubah status `pending` → `shipped` dengan `tracking_number` terisi → assert `order->fresh()->tracking_number` dan `shipped_at` terisi.
- Update Status action: ubah status `pending` → `cancelled` → assert stock variant ter-increment balik (reuse `releaseStockIfApplicable`).
- Update Status action: ubah status `paid` → `cancelled` → assert stock **TIDAK** ter-increment (guard `oldStatus === 'pending'` berlaku juga di sini, regression test untuk behavior yang sengaja dipertahankan).

Existing `tests/Feature/Order/*WebhookTest*` (atau nama file yang sesuai, dicek pas implementasi) harus tetap pass tanpa modifikasi — regression check bahwa refactor `releaseStockIfApplicable` tidak mengubah behavior webhook.

## 7. Out of scope

- Scheduled job auto-expire order pending — task terpisah.
- Email notification order baru — task terpisah (saat ini cuma database notification Filament, sudah jalan, tidak disentuh task ini).
- `midtrans/midtrans-php` Composer package — integrasi existing pakai raw HTTP, tidak diubah.
- Refund/stock-release untuk order yang sudah `paid`/`processing` dibatalkan admin — sengaja tidak di-auto-handle (lihat §4), kalau dibutuhkan nanti jadi task terpisah dengan keputusan bisnis yang jelas dulu (refund partial? full? siapa yang approve?).
