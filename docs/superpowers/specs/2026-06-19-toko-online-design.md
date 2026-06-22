# Design: Website Toko Online — PT Doctor Plant Indonesia

**Status:** Draft — menunggu review user
**Tanggal:** 2026-06-19
**Sumber:** `Flow Sistem Web Toko Online.docx` (disusun oleh Adi Primanto) + sesi brainstorming

## 1. Ringkasan & Keputusan Stack

| Aspek               | Keputusan                                                                                                                                                                                      |
| ------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Arsitektur          | **Monolith**, bukan microservice. Skala 1 client, solo/2 dev, hosting shared — microservice overkill.                                                                                          |
| Backend             | **Laravel 13** (PHP 8.3+ minimum). Serve REST API + Admin Panel dalam 1 app.                                                                                                                   |
| Admin Panel         | **Filament v5** (kompatibel Laravel 13, di dalam app Laravel yang sama), bukan app terpisah.                                                                                                   |
| Frontend (customer) | **Nuxt 4 (4.4.x), mode SPA (`ssr: false`)**. SEO tidak prioritas (traffic dari ads/sosmed) → tidak perlu Node server jalan terus di hosting.                                                   |
| Repo                | **Monorepo**, 1 repo dengan 2 folder (`api/`, `storefront/`). Tim solo/1-2 orang, monorepo lebih simpel dikelola.                                                                              |
| Hosting             | Hostinger (milik client) — tier belum dipastikan (shared/VPS/cloud). Desain ini dibuat agar tetap jalan di shared hosting basic (tanpa Node runtime, tanpa SSH dianggap sebagai _worst case_). |
| Payment             | **Midtrans only** (Snap, popup overlay). Xendit dicoret dari scope.                                                                                                                            |
| Shipping            | **RajaOngkir**, 1 origin/gudang (single warehouse).                                                                                                                                            |

### Open item — wajib diverifikasi sebelum mulai implementasi

- [ ] Cek plan Hostinger client: shared / VPS / cloud, dan apakah ada **SSH access**.
- [ ] Cek PHP version yang tersedia di hPanel client — **wajib PHP 8.3+** untuk Laravel 13. Kalau hosting cuma stop di 8.1/8.2, fallback ke Laravel 12 (min PHP 8.2) atau upgrade plan.

## 2. Arsitektur

```
┌─────────────────────────────┐         ┌──────────────────────────────┐
│  Nuxt 4 SPA (storefront)    │         │   Laravel 13 (api)            │
│  - ssr: false               │  REST   │   - REST API (customer)       │
│  - Build -> static dist/     │ <─────> │   - Admin Panel (Filament)    │
│  - Vue Router, Pinia (cart)  │  JSON   │   - Business logic            │
│  - axios/fetch -> API        │         │   - Sanctum (auth token)      │
└─────────────────────────────┘         │   - MySQL                     │
         ▲                              └──────────────────────────────┘
         │ static files                              ▲
         │ (HTML/CSS/JS)                              │ PHP-FPM
┌────────┴────────────┐                  ┌────────────┴──────────────┐
│ domain.com           │                  │ api.domain.com (subdomain) │
│ Hostinger public_html│                  │ Laravel public/ folder    │
└──────────────────────┘                  └────────────────────────────┘
                              ↑ admin diakses di api.domain.com/admin
```

**Poin kunci:**

- **Auth API**: Laravel Sanctum, token-based (bukan session/cookie) karena Nuxt SPA beda subdomain dari Laravel.
- **Admin Panel**: Filament jalan di Laravel app yang sama, akses via `api.domain.com/admin`.
- **Repo structure**:

```
pt-doctor-plant-indonesia/
├── api/          (Laravel 13 + Filament v5)
├── storefront/   (Nuxt 4 SPA)
└── .github/workflows/
    ├── deploy-api.yml         (path: api/**)
    └── deploy-storefront.yml  (path: storefront/**)
```

## 3. Flow Customer (Buyer)

```
Homepage (banner, produk unggulan, info toko)
  → Katalog (filter/search)
  → Detail Produk (gambar, harga, deskripsi, rating bintang avg, pilih variant)
  → Cart (qty/hapus per-variant)
  → [Auth Gate] Login / Register / Guest checkout (email only)
  → Checkout Step 1: Alamat pengiriman
  → Checkout Step 2: Pilih kurir & ongkir (RajaOngkir)
  → Checkout Step 3: Konfirmasi & Bayar → Midtrans Snap popup overlay
  → Webhook Midtrans → update status order
  → Order History (riwayat + tracking resi)
  → [Pasca-order, status completed] User bisa kasih rating bintang (1-5) ke produk yang dibeli
```

Detail:

- **Produk + variant**: pilih variant (misal ukuran kemasan) dulu sebelum add-to-cart. Stock & harga ikut variant terpilih, bukan produk induk.
- **Cart**: fully client-side (Pinia + localStorage), baik guest maupun login. Tidak ada tabel cart di database — cart baru jadi record `orders` saat submit checkout.
- **Guest checkout**: tidak bikin akun, cuma input nama + email + telp. Order history guest dicari pakai `order_number` + email (tidak ada login session).
- **Rating**: hanya bintang (1-5), tanpa komentar teks. Hanya bisa rating produk dari order yang sudah `completed`, 1 rating per `order_item` (verified-purchase-only, dijamin oleh unique constraint, tanpa kolom `is_verified` terpisah).
- **Ongkir**: dipanggil 1x ke RajaOngkir saat user klik "Cek Ongkir" di checkout step 2, origin tetap (gudang tunggal), destination dari alamat user.
- **Payment**: Midtrans Snap popup overlay — Laravel generate Snap token via API, Nuxt buka popup pakai Snap.js. Status order tetap `pending` sampai webhook notification masuk dari Midtrans.

## 4. Flow Admin

```
Dashboard (ringkasan sales/order/revenue + chart)
Produk (CRUD produk + kategori + variant + stock per variant)
Pesanan (list masuk → detail → konfirmasi bayar manual jika perlu → input resi)
Pelanggan (list + detail histori beli)
Settings (config toko, ganti password admin)
```

- Role admin cuma 1 (owner/admin full akses) — tidak perlu RBAC/permission granular.
- Status order: `pending` → `paid` (auto via webhook Midtrans) → `processing` → `shipped` (admin input resi) → `completed` / `cancelled` / `expired`.
- Admin bisa override status manual untuk edge case, walau pembayaran cuma lewat Midtrans.
- **Notifikasi order baru**: begitu webhook Midtrans ubah status order jadi `paid`, dispatch Laravel Notification ke `Admin` (Filament database notification — muncul di ikon bel panel admin — **dan** email). Admin gak boleh baru tau ada order pas buka dashboard manual. Implementasi pakai `Illuminate\Notifications\Notifiable` di model `Admin` + `Filament\Notifications\Notification::make()->sendToDatabase()`, dipicu dari listener/job yang sama dengan webhook handler (§6).

## 5. Skema Database

### Auth (terpisah, beda guard)

```
users          : id, name, email(unique), phone, password, email_verified_at, timestamps
admins         : id, name, email(unique), password, timestamps   -- single role, no roles table
```

### Produk & Variant

```
categories       : id, name, slug, parent_id(nullable, self-ref), timestamps
products         : id, category_id, name, slug, description, images(json), is_active,
                   rating_avg(decimal, cached), rating_count(int, cached), timestamps
product_variants : id, product_id, name, sku(unique), price, stock, weight(gram), image(nullable), timestamps
```

- `rating_avg`/`rating_count` didenormalisasi di `products`, di-update tiap ada rating baru (hindari hitung ulang `AVG()` tiap load katalog).
- `weight` per variant wajib ada — dipakai untuk hitung total berat cart saat panggil RajaOngkir.
- `product_variants.image` nullable — variant boleh punya gambar sendiri (misal beda kemasan/ukuran kelihatan beda). Kalau kosong, storefront fallback ke gambar pertama di `products.images`.
- **Batas upload gambar** (ditegakkan di Filament `FileUpload` field, bukan di level DB): produk maksimal **5 gambar**, tiap file maksimal **2MB**, tipe file `jpg`/`jpeg`/`png`/`webp` saja. Variant image: 1 file, batas sama (2MB, jpg/png/webp). Validasi ini masuk ke Admin Catalog plan (Filament Resource), bukan Foundation plan.

### Cart

- Tidak ada tabel. Cart fully client-side (Pinia + localStorage).

### Alamat

```
addresses : id, user_id(FK, nullable), label, recipient_name, phone,
            province, city, district, postal_code, full_address, is_default, timestamps
```

- Guest checkout: alamat tidak disimpan ke tabel ini, langsung snapshot ke `orders.shipping_address` (json).

### Order

```
orders      : id, order_number(unique), user_id(FK nullable), guest_name, guest_email, guest_phone(nullable),
              shipping_address(json snapshot), courier, courier_service, shipping_cost, subtotal, total,
              status(enum: pending/paid/processing/shipped/completed/cancelled/expired),
              midtrans_order_id, midtrans_transaction_id, tracking_number, shipped_at, completed_at, timestamps

order_items : id, order_id(FK), product_variant_id(FK), product_name(snapshot), variant_name(snapshot),
              price(snapshot), qty, subtotal, timestamps
```

- `product_name`/`variant_name`/`price` di-snapshot di `order_items` agar histori order tidak berubah kalau produk diedit/dihapus admin di kemudian hari.
- Stock decrement terjadi saat order dibuat (status `pending`) — reserve stock. Scheduled job (Laravel Task Scheduling) auto-cancel order `pending` yang lewat batas waktu Snap token (default ~24 jam) dan mengembalikan stock.

### Rating

```
ratings : id, order_item_id(FK, unique), product_id(FK), user_id(FK), stars(1-5), timestamps
```

- Unique constraint di `order_item_id` → 1 rating per item yang dibeli.

### RajaOngkir area cache

```
rajaongkir_provinces : id (= ID asli RajaOngkir), name
rajaongkir_cities    : id (= ID asli RajaOngkir), province_id, name
rajaongkir_districts : id (= ID asli RajaOngkir), city_id, name
```

- Diseed sekali via artisan command dari endpoint area RajaOngkir (biasanya tidak dikenakan biaya, berbeda dari endpoint `/cost`). Wajib pakai ID asli RajaOngkir, tidak bisa pakai dataset wilayah generik karena sistem ID berbeda.

### Settings

```
store_settings : single-row table — store_name, logo, address, contact, dll (non-secret saja)
```

- Midtrans server key & RajaOngkir API key disimpan di **`.env`**, bukan di DB/admin UI — secret tidak boleh editable lewat panel.

## 6. Integrasi Midtrans (Payment)

```
Nuxt checkout submit
  → POST /api/orders (Laravel: create order status=pending, decrement stock)
  → Laravel: call Midtrans Snap API (server key) -> dapat snap_token
  → return snap_token ke Nuxt
  → Nuxt load Snap.js, snap.pay(snap_token, {onSuccess, onPending, onError, onClose})
  → popup overlay muncul, user bayar

[paralel, server-to-server]
Midtrans → POST /api/webhooks/midtrans (HTTP Notification)
  → Laravel verify signature_key (SHA512: order_id+status_code+gross_amount+server_key)
  → map transaction_status → order.status, idempotent (cek status existing dulu)
  → update stok kalau cancelled/expired (release reserved stock)
```

Poin kritis:

- **Source kebenaran status = webhook**, bukan callback `onSuccess` di frontend. Callback Snap.js hanya untuk UX (redirect ke halaman order detail / tampilkan pesan); mudah dispoof kalau dipakai untuk update status.
- `midtrans_order_id` berbeda dari `order_number` kalau user retry payment (Midtrans wajib `order_id` unik per attempt) — generate `{order_number}-{attempt}` setiap bikin Snap transaction baru, simpan attempt terakhir di kolom `midtrans_order_id`.
- Webhook Midtrans bisa retry/duplicate kirim notification → handler harus idempotent.
- `onClose` (popup ditutup tanpa bayar) → order tetap `pending`, user bisa retry dari Order History (generate Snap token baru).
- Job scheduler: auto-expire order `pending` yang lewat batas waktu Snap token, release stock balik.

## 7. Integrasi RajaOngkir (Shipping)

```
Checkout step 2: Nuxt POST /api/shipping/cost { district_id_tujuan, total_weight }
  → Laravel (pakai API key dari .env) call RajaOngkir /cost endpoint
  → return list kurir+layanan+harga (JNE REG, J&T EZ, dst)
  → user pilih salah satu → simpan ke order saat submit
```

Cost-saving (krusial karena RajaOngkir charge per call):

- Area data (province/city/district) diseed sekali (lihat §5), tidak pernah lookup real-time ke RajaOngkir.
- Origin (gudang) ID disimpan fix di `.env`/`store_settings`, tidak perlu lookup tiap request.
- `/cost` API hanya dipanggil saat user klik "Cek Ongkir" (bukan tiap ganti dropdown tanpa submit).
- Cache hasil `/cost` per kombinasi (district_id + weight) di Laravel cache (Redis/file, TTL beberapa jam).

## 8. Deployment & Hosting (Hostinger)

```
git push main
  → GitHub Actions trigger
  → Job 1 (api):    composer install --no-dev --optimize-autoloader
                     -> upload via SFTP/FTP ke public_html/api (subdomain folder)
  → Job 2 (storefront): npm run build (Nuxt SPA -> dist/)
                     -> upload via SFTP/FTP ke public_html (root domain)
  → Job 3: trigger migration (lihat opsi di bawah)
```

**Struktur folder di Hostinger:**

```
public_html/              -> domain.com           = Nuxt dist/ (static)
public_html/api/          -> api.domain.com        = Laravel (document root diarahkan ke /api/public)
```

Subdomain `api.domain.com` disetup di hPanel, document root pointing ke folder Laravel `public/`.

**Migration — 2 skenario (tergantung hasil verifikasi plan Hostinger di §1):**

1. **Kalau ada SSH access** (Business/Cloud/VPS) — direkomendasikan:
   - GitHub Actions SSH ke server, jalankan `php artisan migrate --force` + `php artisan storage:link` setelah upload selesai.
2. **Kalau shared hosting basic, tanpa SSH:**
   - Buat route khusus terlindungi token secret, misal `POST /deploy/migrate` (cek header `X-Deploy-Token` cocok dengan `.env`), isinya jalankan `Artisan::call('migrate', ['--force' => true])`. CI hit endpoint ini via curl setelah upload. Wajib diproteksi ketat (token kuat, idealnya whitelist IP GitHub Actions kalau memungkinkan) — ini workaround umum untuk shared hosting tanpa SSH, bukan best practice ideal tapi berfungsi.

**Config tambahan:**

- **CORS**: Laravel `config/cors.php` allow origin `https://domain.com` untuk akses `api.domain.com`.
- **Secrets**: `.env` production (DB credentials, Midtrans server key, RajaOngkir key, `APP_KEY`) disimpan di GitHub Secrets, di-inject jadi file `.env` saat CI build — tidak pernah commit ke repo.
- **Storage**: upload gambar produk disarankan langsung ke folder `public/` (skip `storage:link`) untuk menghindari masalah symlink di shared hosting yang restriktif.
- **Database**: MySQL Hostinger biasanya sudah include di plan, connect via `.env` (`DB_HOST=localhost` kalau app & DB di server yang sama).

## 9. Dokumentasi API (Convention)

Ada 2 permukaan yang dipakai client berbeda: **Filament admin panel** (server-rendered, gak butuh dokumentasi API — UI-nya nempel langsung ke Resource) dan **REST API** yang dikonsumsi storefront Nuxt (lintas origin, butuh kontrak jelas).

- Tiap kali sebuah plan implementasi menambah/mengubah endpoint REST (`/api/...`), task tersebut **wajib** menyertakan update ke `docs/api/<resource>.md` — 1 file per resource (`docs/api/auth.md`, `docs/api/catalog.md`, `docs/api/cart-checkout.md`, `docs/api/orders.md`, `docs/api/shipping.md`).
- Format per endpoint: method+path, auth requirement (`Bearer token` / `none`), request body contoh, response body contoh (sukses & error utama), status code yang dipakai.
- Bukan OpenAPI/Swagger formal — cukup markdown manual, konsisten format, gampang dibaca tanpa tooling tambahan. Di-generate begini biar gak nambah dependency (`l5-swagger` dkk) yang gak dibutuhin skala project ini.
- Ditambahkan ke aturan CLAUDE.md (Foundation plan Task 11) sebagai convention wajib.

## 10. Scope yang Dicoret / Tidak Termasuk

- Xendit (cuma Midtrans).
- Multi-role admin / RBAC (cuma 1 role: owner/admin).
- Multi-warehouse (cuma 1 origin gudang).
- Komentar teks di rating (cuma bintang).
- Microservice architecture.
