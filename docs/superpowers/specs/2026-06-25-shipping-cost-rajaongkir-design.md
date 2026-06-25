# Shipping Cost (RajaOngkir) — Design

Status: approved, pending implementation plan.
Related: `docs/superpowers/specs/2026-06-19-toko-online-design.md` §7 (Integrasi RajaOngkir).

## 1. Tujuan

Implement `POST /api/shipping/cost` — endpoint publik yang dipanggil storefront saat user klik "Cek Ongkir" di checkout step 2. Endpoint proxy ke RajaOngkir, hitung ongkos kirim dari gudang (origin tetap) ke alamat tujuan user, return list kurir+layanan+harga.

Scope ini HANYA shipping cost calculation. Tidak termasuk: `OrderController`, Midtrans, `AddressController` — itu spec/plan terpisah.

## 2. RajaOngkir Account

Belum daftar akun RajaOngkir saat spec ini ditulis. Desain diasumsikan pakai **RajaOngkir Komerce (API v2)** — produk aktif yang dijual sekarang (v1 klasik `api.rajaongkir.com` sedang/sudah disunset untuk akun baru).

**Belum terverifikasi live** (perlu konfirmasi begitu API key didapat, sebelum implementasi dianggap final):
- Base URL: `https://rajaongkir.komerce.id/api/v1`
- Endpoint: `POST /calculate/domestic-cost`
- Auth header: `key: <api_key>`
- Body: `origin`, `destination` (district id RajaOngkir), `weight` (gram), `courier` (comma-separated lowercase code, mis. `jne:jnt:sicepat`)
- Response shape: `{ meta: {...}, data: [ { name, code, service, description, cost, etd, ... } ] }`

Kalau shape ternyata beda pas verifikasi live, `RajaOngkirService::calculateDomesticCost()` yang diupdate — kontrak endpoint `/api/shipping/cost` ke storefront tidak berubah (sudah dinormalisasi).

## 3. Kurir yang didukung

JNE, J&T, SiCepat — disimpan sebagai config array (`config('rajaongkir.couriers')`), bukan hardcode di service, biar gampang nambah/kurangi tanpa ubah logic.

## 4. Arsitektur

```
Nuxt checkout step 2
  → POST /api/shipping/cost { district_id, total_weight }
  → ShippingController@cost
      - validasi input
      - ambil origin dari StoreSetting::rajaongkir_origin_district_id
      - Cache::remember(key, ttl, fn() => RajaOngkirService::calculateDomesticCost(...))
  → return list kurir+layanan+harga
```

### `App\Services\RajaOngkirService`

```php
calculateDomesticCost(int $originDistrictId, int $destinationDistrictId, int $weightGrams): array
```

- Panggil RajaOngkir `/calculate/domestic-cost` sekali (semua kurir di config dikirim dalam 1 request via param `courier` comma-separated).
- Normalisasi response jadi `[{ courier: string, service: string, cost: int, etd: string }, ...]`.
- Throw exception kalau response gagal/invalid — ditangkap di controller, bukan di service (service tetap dumb/predictable).

### `App\Http\Controllers\Api\ShippingController@cost`

- Route: `POST /api/shipping/cost`, no middleware (public, sesuai spec utama §7).
- Validasi:
  - `district_id`: required, integer, `exists:rajaongkir_districts,id`
  - `total_weight`: required, integer, min:1 (gram)
- Origin check: `StoreSetting::first()?->rajaongkir_origin_district_id` kosong → `422 { message: "Toko belum mengatur lokasi asal pengiriman" }`.
- Cache key: `"shipping-cost:{origin}:{destination}:{weight}"`.
- Cache TTL: `config('rajaongkir.cache_ttl_hours')`, default 6 jam.
- RajaOngkir call gagal (exception/timeout/non-2xx) → `Log::error(...)`, return `503 { message: "Gagal cek ongkir, coba lagi" }`. Tidak ada auto-retry.
- Sukses → `200 { couriers: [{ courier, service, cost, etd }, ...] }`.

## 5. Config & Secrets

`config/rajaongkir.php`:
```php
return [
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
    'api_key' => env('RAJAONGKIR_API_KEY'),
    'couriers' => explode(',', env('RAJAONGKIR_COURIERS', 'jne,jnt,sicepat')),
    'cache_ttl_hours' => (int) env('RAJAONGKIR_CACHE_TTL_HOURS', 6),
];
```

`.env.example` tambahan:
```
RAJAONGKIR_API_KEY=
RAJAONGKIR_BASE_URL=https://rajaongkir.komerce.id/api/v1
RAJAONGKIR_COURIERS=jne,jnt,sicepat
RAJAONGKIR_CACHE_TTL_HOURS=6
```

API key tetap di `.env`, tidak pernah masuk DB/admin UI (konsisten sama spec utama §5).

## 6. Cost-saving guarantees (kenapa desain begini)

- Area lookup (province/city/district) sudah dari cache table lokal (`rajaongkir_districts` dkk) — endpoint ini gak pernah lookup area real-time, cuma validasi `exists:rajaongkir_districts,id`.
- `/calculate/domestic-cost` cuma kepanggil 1x per kombinasi (origin tetap + destination + weight) per window cache TTL — request kedua dengan kombinasi sama dalam TTL kena cache hit, gak hit API lagi.
- Endpoint ini cuma terpanggil dari 1 aksi user eksplisit ("Cek Ongkir" button) — bukan side-effect dari render halaman atau perubahan dropdown.

## 7. Testing

- `Http::fake()` simulate response sukses → assert normalized output (`couriers` array, field names benar).
- 2 request identik (district_id + total_weight sama) dalam TTL → `Http::assertSentCount(1)` (cache hit terbukti).
- `district_id` invalid (gak ada di `rajaongkir_districts`) → 422.
- `StoreSetting.rajaongkir_origin_district_id` null → 422 pesan spesifik.
- RajaOngkir return error/exception/timeout → 503 + message, dan log error tercatat.

## 8. Dokumentasi

Buat `docs/api/shipping.md` (belum ada) — dokumentasi `POST /api/shipping/cost`: method+path, auth (`none`), request body contoh, response body contoh (sukses 200 & error 422/503), status code dipakai. Format konsisten sama `docs/api/catalog.md`/`auth.md` (manual markdown, bukan OpenAPI — sesuai convention §9 spec utama).

## 9. Out of scope

- `OrderController` / checkout flow penuh — spec/plan terpisah.
- Midtrans — spec/plan terpisah.
- `AddressController` (CRUD alamat member) — spec/plan terpisah, meski endpoint ini konsumsi `district_id` yang nantinya datang dari alamat tersimpan member atau input manual guest.
- Filament UI buat lihat/edit konfigurasi RajaOngkir di luar `rajaongkir_origin_district_id` (sudah ada di Settings page) — tidak perlu UI tambahan.
