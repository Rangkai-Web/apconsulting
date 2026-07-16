# Progress Checklist — PT Doctor Plant Indonesia Toko Online

Terakhir diaudit: 2026-07-15. Semua fitur di design spec (`docs/superpowers/specs/2026-06-19-toko-online-design.md`) sudah dibangun dan ada test-nya. Dokumen ini isinya gap-gap yang ketemu dari audit mendalam (bukan sekadar cek "ada filenya"), plus checklist manual QA sebelum go-live.

## Ringkasan Progress: ~85%

Kode fitur inti (auth, catalog, cart, checkout, order, admin panel, rating, shipping, payment) **selesai dan matching spec**. Sisa 15% itu gabungan dari: data referensi wilayah yang belum penuh, beberapa gap kecil-menengah di bawah ini, dan proses go-live (config production, deploy, manual QA) yang belum dijalankan.

---

## A. Admin Panel (Filament)

- [x] Kategori punya kolom `parent_id` di DB+model (support subkategori) — form + table Category sekarang punya field/kolom "Kategori Induk" (`CategoryResource.php`).

### A1. Bisa dikerjakan sekarang (urut effort terbesar → terkecil)

- [x] **[Medium]** Tabel Produk & Banner gak ada filter — `SelectFilter('category_id')` + `TernaryFilter('is_active')` di `ProductResource.php`, `TernaryFilter('is_active')` di `BannerResource.php`.
- [x] **[Kecil, ~1 baris]** Field "RajaOngkir origin district" sekarang `->required()` di `SettingsPage.php`.
- [x] **[Kecil, ~1 baris]** Kolom `slug` di Category sekarang `->searchable()`.
- [x] **[Kecil, ~1 baris]** Import `Textarea` gak kepake + `getRelations()` stub kosong dihapus dari `ProductResource.php`.
- [x] **[Kecil]** Kolom `contact` di `StoreSetting` dihapus — migration `drop_contact_from_store_settings_table` + fillable diupdate (field emang gak pernah kepake di form Settings maupun `SettingsResource`).

### A2. Fitur masa depan (setelah fitur utama kelar)

- [ ] `CustomerResource` ("Pelanggan") **belum ada sama sekali** — model `User` juga belum punya relasi `orders()`. Requirement inti di spec ("Pelanggan: list + detail histori beli").
- [ ] Dashboard belum ada **chart** (cuma 4 stat card `StatsOverview`) — spec minta "stats + chart".
- [ ] **Gak ada cara ganti password admin** dari dalam panel — `SettingsPage` cuma ada form profil toko, gak ada halaman/aksi ganti password akun admin.

---

## B. API (Laravel Backend)

**Correctness/keamanan:**
- [x] ~~`/login` & `/register` gak ada rate limit khusus~~ — ditambah `throttle:5,1` (2026-07-15).
- [x] ~~`courier` & `courier_service` di `POST /api/orders` gak divalidasi max-length~~ — ditambah `max:255` (match kolom DB) di `OrderController::store` (2026-07-15).
- [x] ~~Ada 2 cara beda buat bikin URL gambar~~ — `ProductResource`/`ProductVariantResource` (API) diseragamkan pakai `Storage::disk('public_uploads')->url()`, sama kayak Banner/Settings Resource (2026-07-15).

**Konsistensi:**
- [x] ~~Response `AddressController` pakai camelCase, endpoint lain snake_case~~ — `mapAddress()` diganti snake_case (2026-07-15). Ternyata gak breaking: `addressService.ts` `mapAddress()` udah punya fallback `raw.recipientName ?? raw.recipient_name` dari awal (defensif, gak sengaja nunggu ini), jadi FE tetap jalan begitu BE ganti; fallback camelCase-nya dibersihin karena udah mati. `unmapAddress()` (request body) emang udah snake_case dari awal, gak kesentuh. Test BE (`AddressApiTest.php`) + docs (`addresses.md`) diupdate ke snake_case.
- [x] ~~Order/Profile/Address gak pakai API Resource class~~ — `OrderResource`/`OrderItemResource` dibuat, Order sekarang gak bocorin `guest_email`, `midtrans_order_id`, `midtrans_transaction_id`, `shipped_at`, `completed_at`, dll (2026-07-15). Profile/Address masih raw array/model tapi field-nya emang udah sesuai spec, gak ada yang bocor.
- [x] ~~Error "not found" gak konsisten~~ — semua 404 di `api/*` sekarang pesan Bahasa Indonesia seragam, gak ada lagi yang bocorin nama class model (2026-07-15).
- [x] ~~`docs/api/orders.md` gak nyebut blok `meta`~~ — udah ditambahin ke contoh response (2026-07-15).
- [x] ~~Policy validasi password beda jalur antara register vs ganti password~~ — `AuthController::register` sekarang pakai `Password::defaults()`, sama kayak `ProfileController` (2026-07-15). `docs/api/auth.md` diupdate.

**Sudah oke (dikonfirmasi lewat audit, gak perlu dikerjain):** gak ada IDOR di endpoint manapun, shipping cost udah diverifikasi server-side, webhook signature+idempotency aman, race condition stock udah di-lock, pagination udah dibatasi server-side, gak ada N+1 query.

---

## C. Frontend (Storefront)

**Checkout & Cart (paling kritis buat konversi):**
- [x] ~~`getProvinces`/`getCities`/`getDistricts` di checkout gak ada try/catch~~ — sekarang toast error + loading skeleton pas initial load (2026-07-15).
- [x] ~~Form checkout guest gak ada validasi per-field~~ — 10 field guest form (`checkout/index.vue`) sekarang punya `guestFieldErrors` + `:error` di tiap `UFormField`, tervalidasi saat submit diklik (bukan cuma disable tombol), error per field hilang otomatis begitu diisi (2026-07-15).
- [x] ~~Selector kurir di checkout berpotensi overflow di layar sempit~~ — ditambah `flex-wrap` (2026-07-15).

**Loading/error states:**
- [x] ~~Home, Katalog, Wishlist: fetch produk awal gak ada try/catch~~ — sekarang toast error + loading direset di `finally` (2026-07-15).
- [x] ~~Detail produk: error API asli ke-treat sama kayak "Produk tidak ditemukan"~~ — `catalogService.getProductBySlug` sekarang cuma treat 404 asli sebagai "not found", error lain (network/500) di-rethrow dan tampil sebagai state error terpisah + toast + tombol "Coba Lagi" (`produk/[slug].vue`, `EmptyState.vue` ditambah event `@action` buat retry non-navigasi) (2026-07-15).
- [x] ~~`auth.ts` `logout()` gak ada try/catch~~ — sekarang selalu clear sesi lokal di `finally`, walau server-side logout gagal (2026-07-15).

**Aksesibilitas:**
- [x] ~~Beberapa tombol icon-only gak ada `aria-label`~~ — qty stepper, wishlist-heart (card & PDP), 3 tombol close slideover (checkout/katalog/header) udah dikasih `aria-label`/`aria-pressed` (2026-07-15).
- [x] ~~Login & Register pakai `<label>` tanpa `for`/`id`~~ — udah dikasih `id`/`for` di semua field (2026-07-15).
- [x] ~~Login & Register cuma nampilin 1 pesan error pertama~~ — kedua form diganti `UFormField` + `:error` per field (pattern sama kayak `akun/profil.vue`), error field hilang otomatis begitu user ngetik ulang (2026-07-15).

**Sudah oke:** cart logic (stock cap, persist), halaman order detail/history/cek, akun profil/alamat, checkout submit+Midtrans callback handling — semua udah ada loading/error/empty state yang proper.

---

## D. Data & Infrastruktur

- [ ] RajaOngkir area data: **26/34 provinsi** ke-sync (402 kota, 5544 kecamatan). Sisa 8 provinsi (Gorontalo, NTT, Sulut, Sulteng, Papua Barat, Kalut, Malut, Sulbar) — nunggu kuota harian reset / keputusan upgrade paket.
- [ ] `MIDTRANS_IS_PRODUCTION` — key production udah dipasang di server (2026-07-16), tapi lagi disetel sementara ke **Sandbox** (buat testing checkout aman tanpa uang asli). Belum di-swap balik ke production, dan checkout end-to-end belum ke-tes tuntas (kebentur error cek ongkir/RajaOngkir 429 duluan — lihat poin pertama).
- [x] ~~`APP_ENV=local`, `APP_URL=http://127.0.0.1:8000` — belum di-set buat server production~~ — dikonfirmasi 2026-07-16, `.env` server production udah benar (`APP_ENV=production`, `APP_URL=https://api.drplant.co.id`).
- [x] ~~Cron scheduler (`orders:expire-pending`)~~ — entry lama ternyata salah format (redirect `>>` ke-passing literal ke `artisan`, gagal tiap menit walau sempat tercatat "terverifikasi" 2026-07-15). Di-fix ulang 2026-07-16, re-tested lewat "Lihat Output" → `INFO No scheduled commands are ready to run.` (sukses, scheduler jalan normal).
- [ ] Test coverage frontend masih tipis — baru store `cart` yang ke-cover. `auth.ts`, `catalog.ts`, `wishlist.ts`, dan semua file `services/*.ts` belum ada test.
- [x] ~~CI/CD deploy workflow (`deploy-api.yml`, `deploy-storefront.yml`) — ada fix SSH key baru-baru ini, worth dipastikan jalan mulus sampai production~~ — dikonfirmasi 2026-07-16, 2 auto-deploy sukses ke production (fix `User::orders()` + `ProductController` count query) via push ke `main`, run status hijau.

---

## Checklist Manual QA (kamu yang ceklist sendiri)

### Belanja & Checkout
- [ ] Add-to-cart beberapa produk beda varian, ubah qty, hapus item — subtotal/berat kehitung bener.
- [ ] Checkout sebagai **guest** — isi alamat manual, cek ongkir (pake kota dari provinsi yang udah ke-sync: Jabar/Jateng/Jatim/Bali/Sumut/Sumbar/dll), submit order, bayar via Midtrans Snap sandbox.
- [ ] Checkout sebagai **member** (login) — pake alamat tersimpan, ulangi alur yang sama.
- [ ] Bayar sampai tuntas di Snap sandbox — status order `pending` → `paid` otomatis, email + bel notif admin masuk.
- [ ] Batalin popup Snap di tengah jalan — order tetap `pending`, bisa retry bayar dari halaman order.

### Order & Akun
- [ ] Guest cek pesanan via nomor order + email di `/orders/cek`.
- [ ] Kasih rating produk setelah order `completed` (ubah status manual dulu di admin panel).
- [ ] Kelola alamat tersimpan (tambah/edit/hapus/jadikan utama) di `/akun/alamat`.
- [ ] Edit profil & ganti password di `/akun/profil`.

### Admin Panel
- [ ] Login admin (`perawatankulittumbuhanalpine@gmail.com` / `Admindrplant123@`).
- [ ] CRUD produk + upload gambar (test batas 5 gambar/2MB) + kelola varian.
- [ ] Input resi & ubah status order manual.
- [ ] Cek dashboard nampilin data bener.
- [ ] Kelola banner homepage & Settings (ganti info toko, cek muncul di footer storefront).

### Lain-lain
- [ ] Buka storefront di HP asli (responsive check, khususnya checkout & cart).
- [ ] Cek email order-paid beneran nyampe pas ada order asli (bukan test manual).
