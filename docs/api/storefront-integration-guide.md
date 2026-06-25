# Storefront API Integration Guide

Peta lengkap API yang dibutuhkan storefront (Nuxt 4 SPA, `storefront/`) untuk seluruh customer journey — dari autentikasi sampai checkout. Dokumen ini adalah index/overview; detail kontrak tiap endpoint tetap di file per-resource (`docs/api/auth.md`, `catalog.md`, `settings.md`, `shipping.md`).

**Status terakhir:** 2026-06-25. Backend: Laravel 13 (`api/`). Frontend: Nuxt 4 (`storefront/`).

## Legend

| Simbol | Arti |
|---|---|
| ✅ | Backend ada, sudah dipanggil dari storefront |
| 🟡 | Backend ada, **belum** dipanggil dari storefront (endpoint nganggur) |
| ❌ | Backend belum ada — perlu dibangun |

## Customer Journey & Status per Tahap

```
1. Browse katalog (kategori, produk, search, filter)         ✅
2. Lihat detail produk + pilih variant                        ✅
3. Add to cart (client-side, Pinia + localStorage)             ✅ (no API — by design)
4. Login / Register / Guest checkout                          ✅ login/register, ❌ guest order lookup
5. Checkout Step 1 — pilih/isi alamat pengiriman                ❌
6. Checkout Step 2 — cek ongkir (RajaOngkir)                    🟡 backend ready, not wired
7. Checkout Step 3 — submit order + bayar (Midtrans Snap)       ❌
8. Webhook Midtrans → update status order                       ❌
9. Order History (riwayat + tracking resi)                       ❌
10. Rating produk (pasca order completed)                       ❌
```

---

## A. Auth — ✅ Implemented

- `POST /api/register`, `POST /api/login`, `POST /api/logout` — dipanggil dari `storefront/app/services/authService.ts:42,84,137`.
- Kontrak lengkap: [auth.md](auth.md).
- Dipakai di `storefront/app/store/auth.ts` (state `user`/`token`, persist ke localStorage `dpi-auth-user`/`dpi-auth-token`).

⚠️ **Risiko bug ditemukan (lihat §J di bawah):** `authService.ts` punya mock-fallback yang aktif juga saat backend balas `500` — bukan cuma saat server unreachable.

## B. Profile — ✅ Implemented

- `GET/PUT /api/profile`, `PUT /api/profile/password` — kontrak di [auth.md](auth.md).
- **Belum dipanggil dari storefront** — belum ada halaman "Profil Saya". Perlu dibangun kalau mau expose edit profile/password ke member.

## C. Catalog (Categories & Products) — ✅ Implemented

- `GET /api/categories`, `GET /api/products`, `GET /api/products/{slug}` — kontrak di [catalog.md](catalog.md).
- Dipanggil dari `storefront/app/services/catalogService.ts` — semua response snake_case backend di-map ke camelCase frontend lewat `mapProduct()`/`mapVariant()`/`mapCategory()` (`catalogService.ts:3-38`). **Service address/order baru nanti harus ikut pola mapping yang sama** — jangan return raw snake_case langsung ke component.

## D. Settings — ✅ Implemented

- `GET /api/settings` — kontrak di [settings.md](settings.md). Dipakai `AppFooter.vue` lewat `services/api.ts:17-19`.

## E. Address (Alamat Tersimpan Member) — ❌ Missing

Dibutuhkan buat Checkout Step 1. Frontend sudah punya tipe `Address` (`storefront/app/types/index.ts:46-57`):

```ts
interface Address {
  id: number
  label: string
  recipientName: string
  phone: string
  province: string
  city: string
  district: string
  postalCode: string
  fullAddress: string
  isDefault: boolean
}
```

Endpoint yang perlu dibangun (sesuai spec utama §5 Profile & Address):

| Method | Path | Auth | Body |
|---|---|---|---|
| GET | `/api/addresses` | `auth:sanctum` | - |
| POST | `/api/addresses` | `auth:sanctum` | `label, recipient_name, phone, province, city, district, postal_code, full_address, is_default` |
| PUT | `/api/addresses/{id}` | `auth:sanctum` | sama seperti POST |
| DELETE | `/api/addresses/{id}` | `auth:sanctum` | - |

**Catatan field mapping:** backend `recipient_name`/`postal_code`/`full_address`/`is_default` (snake_case) → frontend `recipientName`/`postalCode`/`fullAddress`/`isDefault` (camelCase) — service mapper wajib translate, sama persis pola `catalogService.ts`.

**Guest checkout:** alamat guest TIDAK pakai endpoint ini — langsung snapshot ke `orders.shipping_address` saat submit order (lihat §G). Address API ini member-only.

**district untuk RajaOngkir:** field `district` di tipe frontend ini string nama district, BUKAN `district_id` RajaOngkir. Saat checkout, storefront butuh `district_id` (integer, RajaOngkir native ID) buat dikirim ke `/api/shipping/cost` — pertimbangkan tambah field `rajaongkirDistrictId: number` ke tipe `Address` & ke form alamat, supaya gak perlu lookup nama→id lagi pas checkout. Area dropdown (province/city/district) sebaiknya pakai cache table `rajaongkir_*` (lihat §F), bukan input bebas.

## F. Shipping Cost (RajaOngkir) — 🟡 Backend ready, not wired

- `POST /api/shipping/cost` — **sudah dibangun & tested** (lihat [shipping.md](shipping.md)). Endpoint nganggur — belum dipanggil dari frontend manapun.
- Bukti: `storefront/app/pages/cart/index.vue:185,196-197` masih nampilin teks statis "Estimasi Ongkir — Dihitung nanti" / "diatur pada langkah checkout selanjutnya" — bukan hasil API call.
- **Belum bisa full-functional** — `RAJAONGKIR_API_KEY` belum diisi (akun belum daftar, lihat `docs/superpowers/specs/2026-06-25-shipping-cost-rajaongkir-design.md` §2). Endpoint akan return 503 sampai key diisi.
- Body yang harus dikirim storefront: `{ district_id: number, total_weight: number }` (gram, sum dari `cart.items[].weight * qty`). `cart.ts` store sudah punya `weight` per item (`storefront/app/types/index.ts:43`) — cukup `items.reduce((sum, i) => sum + i.weight * i.qty, 0)`, belum ada helper getter ini di `store/cart.ts` — perlu ditambah saat checkout dibangun.

## G. Checkout & Orders — ❌ Missing

Frontend sudah punya tipe `Order`/`OrderItem` (`storefront/app/types/index.ts:68-91`) tapi **tidak ada field buat Midtrans Snap token/redirect** — perlu ditambah saat endpoint ini dibangun (lihat §J.3).

Endpoint yang perlu dibangun (sesuai spec utama §6, §9.2):

| Method | Path | Auth | Keterangan |
|---|---|---|---|
| POST | `/api/orders` | optional (member atau guest) | Create order dari cart, decrement stock, return `snap_token` |
| GET | `/api/orders` | `auth:sanctum` | List order history member |
| GET | `/api/orders/{id}` | `auth:sanctum` atau owner-check | Detail 1 order |
| GET | `/api/orders/lookup?order_number=&email=` | none | Guest order lookup (lihat spec §9.2 — bukan auth, validasi `order_number`+`email` cocok) |

`POST /api/orders` request body (diturunkan dari cart store + checkout form):
```json
{
  "items": [{ "variant_id": 1, "qty": 2 }],
  "address_id": 5,
  "guest_name": null, "guest_email": null, "guest_phone": null,
  "shipping_address": { "...": "snapshot kalau guest, atau hasil lookup address_id kalau member" },
  "courier": "jne", "courier_service": "REG", "shipping_cost": 15000
}
```
Response sukses harus include `snap_token` (dipakai `snap.pay(snap_token, {...})` di frontend) dan `order_number`.

**Halaman frontend:** `storefront/app/pages/checkout/index.vue:28-32` masih EmptyState "Checkout Segera Hadir" — belum ada implementasi sama sekali (form alamat, pilih kurir, tombol bayar).

## H. Payment (Midtrans) — ❌ Missing

- Server-to-server webhook `POST /api/webhooks/midtrans` — **source of truth status order**, bukan callback `onSuccess` Snap.js (lihat spec utama §6 poin kritis: callback frontend gampang dispoof).
- Frontend cuma butuh: load `Snap.js` (script tag dari Midtrans), panggil `snap.pay(snap_token, { onSuccess, onPending, onError, onClose })` — token didapat dari response `POST /api/orders` (§G), bukan endpoint terpisah.
- `MIDTRANS_SERVER_KEY` belum ada di `.env`/`.env.example` — perlu ditambah saat dibangun.

## I. Order History & Rating — ❌ Missing

- Order history member: `GET /api/orders` (§G).
- Rating: `POST /api/order-items/{id}/rating` body `{ stars: 1-5 }` — hanya untuk `order_item` dari order `status=completed`, 1x per item (unique constraint sudah ada di DB, model `Rating` & migration sudah siap — tinggal controller+route). Frontend `OrderItem.rated` (`types/index.ts:76`) udah disiapkan buat nampilin tombol "Beri Rating" vs "Sudah dirating".

---

## J. Bug Risk & Improvement Notes (backend/service layer — UI tidak disentuh)

### J.1 — `authService.ts` mock-fallback ketriger juga saat backend error 500 (bukan cuma offline)

**Lokasi:** `storefront/app/services/authService.ts:49,90`
```ts
const isConnectionError = !error.response || error.status === 503 || error.status === 504 || error.status === 500;
```
**Masalah:** kondisi ini true juga kalau backend hidup tapi balas `500` (bug nyata di backend, bukan server down). User dianggap "berhasil login" via mock dengan token palsu `mock-jwt-token-xyz-12345` — token ini gak dikenal backend sama sekali. Begitu user coba akses endpoint authenticated (`/api/profile`, nanti `/api/orders`), semua bakal `401` karena token gak valid di server, padahal UI nunjukin user "sudah login".

**Dampak:** silent false-positive login, error backend asli (500) ketutup jadi keliatan "berhasil", debugging production jadi sulit (gak ada error yang muncul ke user/log eksternal saat backend benar-benar bug).

**Rekomendasi (belum diimplementasi — di luar scope dokumentasi ini, tapi dicatat supaya gak lolos pas checkout/order dibangun):** pisahkan kondisi network-down (`!error.response`, timeout) dari HTTP error response (`error.status` ada nilainya = server hidup, response valid). Mock fallback harus cuma jalan untuk network-down, bukan untuk 500.

### J.2 — `api.ts` vs `authService.ts`/`catalogService.ts` — inkonsistensi struktur file

`storefront/app/services/api.ts` cuma punya 1 method (`getSettings`), sementara auth & catalog masing-masing punya file service sendiri. Saat `addressService.ts`/`orderService.ts` dibangun nanti, ikuti pola file-per-resource yang sudah ada (`authService.ts`, `catalogService.ts`) — bukan ditumpuk ke `api.ts`.

### J.3 — Tipe `Order` frontend belum punya field buat Midtrans

`storefront/app/types/index.ts:79-91` — `Order` interface gak punya `snapToken`/`paymentUrl`. Saat `POST /api/orders` dibangun, tipe ini perlu field tambahan (mis. `snapToken: string` di response create-order — gak perlu nempel permanen di `Order` kalau cuma dipakai sekali waktu create, bisa jadi tipe response terpisah `CreateOrderResponse extends Order { snapToken: string }`).

### J.4 — `Address` frontend belum punya field RajaOngkir `district_id`

Lihat §E — tipe `Address` cuma punya `district` (nama string), bukan ID numerik RajaOngkir yang dibutuhkan `/api/shipping/cost`. Tanpa ini, checkout step 2 gak bisa connect alamat tersimpan ke endpoint ongkir tanpa lookup tambahan nama→ID yang rawan salah (nama district bisa ambigu antar kota).

### J.5 — `cart.ts` store belum punya helper total weight

Lihat §F — perlu getter `totalWeight` di `storefront/app/store/cart.ts` (`items.reduce((sum, i) => sum + i.weight * i.qty, 0)`) supaya checkout gak hitung manual di tiap tempat yang butuh (cart page estimasi + checkout step 2 actual call).

---

## Urutan Kerja yang Disarankan

1. **Address API** (§E) — `AddressController` CRUD, tambah `rajaongkir_district_id` mirroring ke tipe frontend.
2. **Wire shipping cost ke frontend** (§F) — tambah `totalWeight` getter, panggil `/api/shipping/cost` dari checkout step 2 (perlu `RAJAONGKIR_API_KEY` diisi dulu, atau test pakai `Http::fake` kalau UI dibangun duluan).
3. **Orders + Midtrans** (§G, §H) — `OrderController`, integrasi Midtrans Snap, webhook handler. Ini blok terbesar, kemungkinan perlu di-plan terpisah jadi 2-3 plan kecil (create order, payment, webhook) sesuai workflow `brainstorming` → `writing-plans` yang sudah dipakai buat shipping.
4. **Order History + guest lookup** (§G, §I).
5. **Rating** (§I) — paling independen, bisa dikerjakan kapan aja setelah Orders ada.
6. Sambil jalan: fix `authService.ts` mock-fallback (§J.1) sebelum checkout production-ready — kalau gak, bug silent-login bisa nyamar jadi "checkout gagal karena 401" yang membingungkan buat debug nanti.
