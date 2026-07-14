# Progress Checklist — PT Doctor Plant Indonesia Toko Online

Terakhir diaudit: 2026-07-15. Semua fitur di design spec (`docs/superpowers/specs/2026-06-19-toko-online-design.md`) sudah dibangun dan ada test-nya. Dokumen ini isinya gap-gap yang ketemu dari audit mendalam (bukan sekadar cek "ada filenya"), plus checklist manual QA sebelum go-live.

## Ringkasan Progress: ~85%

Kode fitur inti (auth, catalog, cart, checkout, order, admin panel, rating, shipping, payment) **selesai dan matching spec**. Sisa 15% itu gabungan dari: data referensi wilayah yang belum penuh, beberapa gap kecil-menengah di bawah ini, dan proses go-live (config production, deploy, manual QA) yang belum dijalankan.

---

## A. Admin Panel (Filament)

**Fitur yang belum ada:**
- [ ] `CustomerResource` ("Pelanggan") **belum ada sama sekali** — model `User` juga belum punya relasi `orders()`. Ini requirement inti di spec ("Pelanggan: list + detail histori beli") yang belum dikerjakan.
- [ ] Dashboard belum ada **chart** (cuma 4 stat card `StatsOverview`) — spec minta "stats + chart".
- [ ] **Gak ada cara ganti password admin** dari dalam panel — `SettingsPage` cuma ada form profil toko, gak ada halaman/aksi ganti password akun admin.
- [ ] Kategori punya kolom `parent_id` di DB+model (support subkategori), tapi **form Category gak ada field buat itu** — subkategori gak bisa diatur dari admin panel.

**Polish/konsistensi kecil:**
- [ ] Field "RajaOngkir origin district" di Settings ditulis wajib di teks bantuan, tapi field-nya **gak divalidasi `required()`** — admin bisa save kosong padahal fitur cek ongkir butuh ini.
- [ ] Tabel Produk & Banner gak ada filter (kategori / aktif-nonaktif), padahal Order udah ada filter status.
- [ ] Kolom `slug` di Category gak searchable (cuma `name`).
- [ ] Import `Textarea` gak kepake di `ProductResource.php`, `getRelations()` isinya stub kosong — dead code kecil.
- [ ] Kolom `contact` di `StoreSetting` gak pernah dipakai di form Settings — field mati, bisa dihapus.

---

## B. API (Laravel Backend)

**Correctness/keamanan:**
- [ ] `/login` & `/register` **gak ada rate limit khusus** (cuma limiter global 60/menit) — rawan brute-force. Endpoint lain yang sensitif (`/shipping/cost`) udah dikasih throttle eksplisit, ini belum.
- [ ] `courier` & `courier_service` di `POST /api/orders` gak divalidasi max-length — input kepanjangan bisa lolos validasi terus gagal di level DB, jadi error 500 generik bukan 422 yang rapi.
- [ ] Ada **2 cara beda** buat bikin URL gambar (`asset()` di ProductResource vs `Storage::disk()->url()` di Banner/Settings Resource) — bisa beda hasil kalau nanti pakai CDN/proxy.

**Konsistensi:**
- [ ] Response `AddressController` pakai **camelCase**, semua endpoint lain **snake_case** — inkonsisten (meski sudah terdokumentasi apa adanya).
- [ ] Order/Profile/Address **gak pakai API Resource class** (langsung serialize model Eloquent) — response Order jadi bocorin field internal yang gak terdokumentasi: `guest_email`, `midtrans_order_id`, `midtrans_transaction_id`, `shipped_at`, `completed_at`, dll.
- [ ] Error "not found" gak konsisten — sebagian custom message Bahasa Indonesia, sebagian jatuh ke pesan default Laravel (Bahasa Inggris + bocorin nama class model).
- [ ] `docs/api/orders.md` gak nyebut blok `meta` (pagination) yang sebenarnya selalu ada di response `GET /api/orders`.
- [ ] Policy validasi password beda jalur antara register (`min:8`) vs ganti password (`Password::defaults()`) — kebetulan sama sekarang, tapi gak dijamin tetap sama kalau salah satu diubah nanti.

**Sudah oke (dikonfirmasi lewat audit, gak perlu dikerjain):** gak ada IDOR di endpoint manapun, shipping cost udah diverifikasi server-side, webhook signature+idempotency aman, race condition stock udah di-lock, pagination udah dibatasi server-side, gak ada N+1 query.

---

## C. Frontend (Storefront)

**Checkout & Cart (paling kritis buat konversi):**
- [ ] `getProvinces`/`getCities`/`getDistricts` di checkout **gak ada try/catch** — kalau API gagal, dropdown alamat diem kosong tanpa pesan error ke user.
- [ ] Form checkout guest gak ada validasi per-field — cuma tombol submit yang disable, user gak tau field mana yang salah.
- [ ] Selector kurir di checkout berpotensi overflow di layar sangat sempit (<375px) — belum ada `flex-wrap`.

**Loading/error states:**
- [ ] Home, Katalog, Wishlist: fetch produk awal **gak ada try/catch** — kalau API gagal, skeleton loading muter selamanya tanpa pesan error.
- [ ] Detail produk: error API asli (network/500) ke-treat sama kayak "Produk tidak ditemukan" — nyamarin error asli jadi 404 palsu.
- [ ] `auth.ts` `logout()` gak ada try/catch — kalau logout gagal di server, tetap nampilin toast "Logout Berhasil" ke user (menyesatkan).

**Aksesibilitas:**
- [ ] Beberapa tombol icon-only gak ada `aria-label`: tombol +/- qty stepper (cart & detail produk), tombol wishlist-heart, 3 tombol close (×) di slideover checkout/katalog/header.
- [ ] Login & Register pakai `<label>` tanpa `for`/`id` terhubung ke input — screen reader gak bisa asosiasikan label ke field.
- [ ] Login & Register cuma nampilin 1 pesan error pertama dari validasi, gak ada indikator per-field mana yang salah.

**Sudah oke:** cart logic (stock cap, persist), halaman order detail/history/cek, akun profil/alamat, checkout submit+Midtrans callback handling — semua udah ada loading/error/empty state yang proper.

---

## D. Data & Infrastruktur

- [ ] RajaOngkir area data: **26/34 provinsi** ke-sync (402 kota, 5544 kecamatan). Sisa 8 provinsi (Gorontalo, NTT, Sulut, Sulteng, Papua Barat, Kalut, Malut, Sulbar) — nunggu kuota harian reset / keputusan upgrade paket.
- [ ] `MIDTRANS_IS_PRODUCTION=false` — masih sandbox, ganti ke `true` + API key production pas mau live.
- [ ] `APP_ENV=local`, `APP_URL=http://127.0.0.1:8000` — belum di-set buat server production.
- [x] ~~Cron scheduler (`orders:expire-pending`)~~ — sudah terpasang di Hostinger (`* * * * *` → `schedule:run`), terverifikasi 2026-07-15.
- [ ] Test coverage frontend masih tipis — baru store `cart` yang ke-cover. `auth.ts`, `catalog.ts`, `wishlist.ts`, dan semua file `services/*.ts` belum ada test.
- [ ] CI/CD deploy workflow (`deploy-api.yml`, `deploy-storefront.yml`) — ada fix SSH key baru-baru ini, worth dipastikan jalan mulus sampai production.

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
