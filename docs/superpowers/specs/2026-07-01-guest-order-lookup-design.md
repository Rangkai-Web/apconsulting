# Guest Order Lookup — Design

Status: approved.
Related: `docs/superpowers/specs/2026-06-19-toko-online-design.md` §3 (Flow Customer — guest order history via order_number + email).

## 1. Tujuan

Guest checkout tidak bikin akun, jadi tidak ada order history halaman `/orders/history` untuk mereka. Setelah checkout, guest perlu bisa melihat status pesanannya. Dua masalah saat ini:

1. Setelah checkout, semua `router.push(/orders/${orderNumber})` tidak membawa `?email=`, sehingga halaman detail pesanan mencoba authenticated path → 403 untuk guest.
2. Tidak ada entry point (halaman/link) bagi guest untuk melacak pesanan lama.

## 2. Scope

**In:** Halaman `/orders/cek` (form lookup), fix checkout redirect untuk guest, link "Cek Pesanan" di AppHeader.

**Out:** Email notifikasi ke guest (di luar scope). Guest order history (list banyak pesanan — guest hanya bisa cek 1 pesanan per form submit). SMS. Save lookup history di localStorage.

## 3. Perubahan

### 3.1 Halaman `/orders/cek` (baru)

Route: `storefront/app/pages/orders/cek.vue` — **tidak butuh auth middleware** (justru untuk guest).

Form:
- **Nomor Pesanan** (required) — text input, contoh placeholder: `ORD-20260701-XXXX`
- **Email** (required) — email input, email yang dipakai saat checkout

Submit → tidak ada API call di halaman ini → `router.push(/orders/${orderNumber}?email=${email.trim()})`. Halaman detail pesanan yang sudah handle lookup via `orderService.lookupOrder()`.

Validasi minimal (client-side): kedua field tidak boleh kosong. Tidak perlu validasi format canggih — kalau kombinasi salah, halaman detail akan tampilkan "Pesanan tidak ditemukan".

### 3.2 Fix checkout redirect — guest path

File: `storefront/app/pages/checkout/index.vue`

Tambah helper function:

```typescript
function orderUrl(orderNumber: string, extra: Record<string, string> = {}): string {
  const params = new URLSearchParams(extra);
  if (!isLoggedIn.value && guestForm.email) {
    params.set('email', guestForm.email);
  }
  const qs = params.toString();
  return `/orders/${orderNumber}${qs ? `?${qs}` : ''}`;
}
```

Ganti semua `router.push(\`/orders/${orderNumber}...\`)` pakai `router.push(orderUrl(orderNumber, { ... }))`:

| Before | After |
|--------|-------|
| `` `/orders/${orderNumber}?mock=true` `` | `orderUrl(orderNumber, { mock: 'true' })` |
| `` `/orders/${orderNumber}?status=success` `` | `orderUrl(orderNumber, { status: 'success' })` |
| `` `/orders/${orderNumber}?status=pending` `` | `orderUrl(orderNumber, { status: 'pending' })` |
| `` `/orders/${orderNumber}?status=error` `` | `orderUrl(orderNumber, { status: 'error' })` |
| `` `/orders/${orderNumber}` `` | `orderUrl(orderNumber)` |

### 3.3 AppHeader — link "Cek Pesanan"

File: `storefront/app/components/AppHeader.vue`

**Desktop**: Di kanan header, sebelum tombol "Masuk", tambah `NuxtLink` ke `/orders/cek` — tampil hanya saat `!isLoggedIn`. Style: `text-xs font-bold text-gray-600 hover:text-primary-600`, sejajar dengan tombol login.

**Mobile nav**: Tambah link "Cek Pesanan" (`/orders/cek`) di bagian guest — di atas "Masuk" dan "Daftar". Hanya tampil `v-if="!isLoggedIn"`.

## 4. Files

| Action | Path |
|--------|------|
| Create | `storefront/app/pages/orders/cek.vue` |
| Modify | `storefront/app/pages/checkout/index.vue` — tambah `orderUrl()` helper, replace 5 `router.push` calls |
| Modify | `storefront/app/components/AppHeader.vue` — tambah link desktop + mobile |

## 5. Testing

- Vitest: tidak ada (pola existing tidak punya component tests untuk pages)
- Manual: guest checkout → setelah bayar → URL mengandung `?email=` → halaman detail terbuka tanpa error
- Manual: buka `/orders/cek` → isi form → submit → halaman detail terbuka untuk guest
- Manual: header menampilkan "Cek Pesanan" saat belum login, hilang setelah login
