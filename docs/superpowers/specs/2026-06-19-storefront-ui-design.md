# Design: Storefront UI/UX Guidelines — PT Doctor Plant Indonesia

**Status:** Draft — menunggu review user
**Tanggal:** 2026-06-19
**Relasi:** Pelengkap `2026-06-19-toko-online-design.md` (§3 Flow Customer). Dokumen ini cuma soal **storefront** (Nuxt). Admin panel (Filament) sengaja tidak dispek di sini — lihat §6.

## 1. Kenapa Dokumen Ini Ada

Tanpa acuan tertulis, tiap sesi ngoding (apalagi pakai AI) bisa improvisasi gaya beda-beda — modal kadang dipake kadang enggak, pagination kadang infinite-scroll kadang page-number, warna tombol gak konsisten. Dokumen ini bukan pixel-perfect mockup, tapi **keputusan baku** yang dipakai berulang di semua page, biar AI/dev gak perlu nebak ulang tiap kali bikin page baru.

## 2. Stack & Library

| Aspek        | Keputusan                                                                                            |
| ------------ | ---------------------------------------------------------------------------------------------------- |
| UI Library   | **Nuxt UI v4** (v4.9.x) — 110+ komponen, behavior + a11y built-in (modal, toast, pagination, form).  |
| Styling base | Tailwind CSS (bawaan Nuxt UI, gak perlu setup manual).                                               |
| Font         | Default Nuxt UI (Public Sans) — cukup, gak perlu custom font kecuali client kasih brand guide nanti. |

**Install:**

```bash
cd storefront
npx nuxi module add ui
```

## 3. Identitas Visual

Brand color sudah ditentukan: **Primary `#045C34`** (hijau tua), **Secondary `#F1FFF5`** (mint pucat — dipakai sebagai tint/background, bukan warna tombol solid, karena kontras terhadap teks putih/gelap terlalu rendah).

Nuxt UI v4 butuh ramp **50–950** per warna (bukan cuma 1 hex) karena tiap shade dipakai utk state berbeda (hover, disabled, dark mode). Generate ramp dari hex brand pakai **Nuxt UI Theme generator** (`https://ui.nuxt.com/docs/getting-started/theme` — paste `#045C34` & `#F1FFF5`, copy hasil CSS), jangan ditebak manual.

```css
/* storefront/app/assets/css/main.css */

@import "tailwindcss";
@import "@nuxt/ui";

@theme static {
  --color-primary-50: /* hasil generator */;
  /* ...lanjut sampai 950... */
  --color-primary-500: #045C34; /* pastikan generator nempatin base color di shade ini */

  --color-secondary-50: /* hasil generator dari #F1FFF5 */;
  /* ...lanjut sampai 950... */
}
```

```typescript
// storefront/app/app.config.ts

export default defineAppConfig({
  ui: {
    colors: {
      primary: "primary",
      secondary: "secondary",
      neutral: "slate",
    },
  },
});
```

- **Primary**: tombol utama (Add to Cart, Checkout, Bayar), link aktif, badge status `paid`/`completed`.
- **Secondary**: background section/card tint (hero, highlight box), badge ringan — bukan tombol CTA utama.
- **Neutral** (`slate`): teks, border, background netral.
- **Semantic warna status order** (Order History, lihat §5.6): `pending`=neutral/gray, `paid`/`processing`=blue, `shipped`=amber/orange, `completed`=primary (hijau), `cancelled`/`expired`=red.

## 4. Behavior Baku (Berlaku di Semua Page)

| Behavior               | Keputusan                                                                                                                                                                                                                                           | Komponen Nuxt UI                    |
| ---------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------- |
| **Notifikasi**         | Toast, posisi top-right, auto-dismiss 4s. Dipakai utk: add-to-cart sukses, error API, logout sukses, dll. Jangan pakai `alert()` browser.                                                                                                           | `useToast()`                        |
| **Modal**              | Cuma utk konfirmasi aksi destruktif (misal "Hapus item dari cart?") atau quick info. **Bukan** utk flow utama (checkout/login tetap full page/section, bukan di dalam modal).                                                                       | `UModal`                            |
| **Pagination katalog** | **"Load More" button**, bukan page-number, bukan infinite-scroll-otomatis. Klik tombol → fetch halaman berikutnya, append ke grid. Alasan: lebih simpel dari infinite-scroll observer, lebih murah dari auto-load (user kontrol kapan fetch jalan). | `UButton` + manual fetch            |
| **Search**             | Debounce **400ms** sebelum trigger API call, search box di Katalog & Header.                                                                                                                                                                        | `UInput` + `useDebounceFn` (VueUse) |
| **Filter**             | Filter (kategori, range harga, sort) **sync ke URL query params** (`?category=pupuk&min=10000&max=50000&sort=price_asc`) — biar bisa di-bookmark & tombol back browser jalan normal.                                                                | `useRoute().query` + `navigateTo`   |
| **Loading state**      | Skeleton placeholder saat fetch data (bukan spinner polos) utk grid produk & detail produk.                                                                                                                                                         | `USkeleton`                         |
| **Empty state**        | Pesan eksplisit + ilustrasi/icon sederhana saat: katalog kosong, cart kosong, order history kosong. Jangan biarin blank.                                                                                                                            | —                                   |
| **Form & validasi**    | Semua form (login, register, address, checkout) pakai validasi schema (Zod), error inline di bawah field, bukan toast/alert generic.                                                                                                                | `UForm` + `zod`                     |
| **Format harga**       | Selalu pakai 1 fungsi util `formatCurrency()` (`Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })`) → `Rp 25.000`. Jangan tampilin angka mentah di mana pun.                                            | `composables/useCurrency.ts`        |
| **Halaman 404**        | `storefront/app/error.vue` (Nuxt 4 default error page) — pesan "Halaman tidak ditemukan" + tombol balik ke Homepage. Bukan blank page/stack trace.                                                                                                  | —                                   |

## 5. Page-by-Page Breakdown

### 5.1 Homepage

- Header: logo, nav (Katalog, kategori dropdown), search bar (debounce 400ms), cart icon + badge jumlah item.
- Hero banner (carousel sederhana, 1-3 slide, dari `products.images`/konten admin — bukan fitur dinamis kompleks, cukup gambar+link).
- Section "Produk Unggulan" — grid produk (reuse komponen `ProductCard` yang sama dipakai di Katalog).
- Footer: info toko (dari `store_settings`), link kontak.

### 5.2 Katalog

- Filter bar (kategori checkbox, range harga, dropdown sort: terbaru/termurah/termahal/rating).
- Search box (debounce, query synced ke URL — lihat §4).
- Grid `ProductCard` (gambar, nama, harga mulai dari `MIN(variant.price)`, rating bintang avg).
- **Stok habis**: kalau semua variant produk stock=0 → badge "Stok Habis" nempel di gambar card, card tetap kelihatan (gak disembunyikan) tapi visual agak pudar (`opacity-60`).
- Tombol "Load More" di bawah grid (§4).
- Empty state kalau filter gak ada hasil.

### 5.3 Detail Produk

- **Galeri gambar**: `UCarousel` (Nuxt UI) + thumbnail strip di bawah buat loncat langsung ke gambar tertentu. Maksimal **5 gambar per produk** (lihat §5 spec utama — batas ditegakkan di admin Filament, bukan storefront). Gak perlu zoom/lightbox kompleks.
- Kalau variant terpilih punya `image` sendiri (non-null) → gambar itu yang ditonjolkan di slide pertama carousel; kalau null → tetap pakai galeri produk induk.
- Nama, rating bintang avg + jumlah rating (`★ 4.5 (23 rating)`), deskripsi.
- Variant selector: pill/button per variant (`ProductVariant.name`), pilih 1 → update harga, stock, dan gambar (poin di atas) yang ditampilkan. Variant dengan stock=0 tetap muncul di selector tapi disabled + label "Stok Habis" kecil di bawah nama variant.
- Qty stepper (− / input / +), disabled kalau qty = stock variant terpilih.
- **Stok habis** (variant terpilih stock=0): tombol "Tambah ke Keranjang" berubah jadi disabled dengan label "Stok Habis", qty stepper disembunyikan.
- Tombol "Tambah ke Keranjang" → toast sukses, update badge cart di header.
- **Race condition checkout**: kalau API order creation balas error stok gak cukup (stok berubah antara add-to-cart dan submit checkout), toast error spesifik (`"Stok {nama produk} tidak cukup, tersisa {n}"`) + redirect balik ke Cart dengan qty otomatis disesuaikan ke stok tersisa.
- **Tidak ada** form/komentar rating di sini — rating cuma muncul setelah pasca-pembelian (lihat §5.6).

### 5.4 Cart

- List item: thumbnail, nama produk + variant, qty stepper, subtotal per item, tombol hapus (konfirmasi via `UModal` kecil).
- Ringkasan: subtotal total, tombol "Checkout".
- Empty state kalau cart kosong (CTA balik ke Katalog).

### 5.5 Checkout

- Stepper indikator di atas (3 step, sesuai spec utama: Alamat → Kurir&Ongkir → Konfirmasi&Bayar). Auth Gate (Login/Register/Guest) muncul sebelum stepper kalau belum login.
- **Step 1**: Form alamat. User login: dropdown alamat tersimpan (`Address`) + opsi "tambah alamat baru". Guest: form bebas (tanpa simpan ke tabel `addresses`, sesuai spec §5).
- **Step 2**: Tombol eksplisit "Cek Ongkir" (bukan auto-trigger tiap ganti dropdown — sesuai spec §7 cost-saving). List kurir+harga muncul sebagai radio button setelah API response.
- **Step 3**: Ringkasan order (item, alamat, kurir, total), tombol "Bayar" → loading state → Midtrans Snap popup overlay (§6 spec utama).

### 5.6 Order History

- List order, tiap card: `order_number`, tanggal, badge status berwarna (lihat §3), total.
- Klik card → expand/detail: list item, alamat, tracking number (kalau ada).
- Kalau status `completed` dan item belum dirating → tombol bintang rating muncul di tiap item (1x submit, hilang setelah dirating — sesuai spec §3 unique constraint `order_item_id`).

## 6. Responsive — Mobile, Tablet, Desktop

Breakpoint pakai default Tailwind/Nuxt UI, jangan custom: `sm` 640px, `md` 768px, `lg` 1024px, `xl` 1280px. Mobile-first — desain default = mobile, lebar ditambah behaviornya di breakpoint atas.

| Komponen | Mobile (`<md`) | Tablet (`md`–`lg`) | Desktop (`≥lg`) |
|---|---|---|---|
| Nav header | Hamburger menu (`USlideover` dari kiri/kanan) | Hamburger menu tetap (nav penuh belum cukup tempat) | Nav horizontal penuh |
| Filter Katalog | `USlideover`/drawer dari bawah, dipanggil tombol "Filter" | Sidebar kolom kiri, collapsible | Sidebar kolom kiri, fixed |
| Grid produk | 2 kolom | 3 kolom | 4 kolom |
| Checkout stepper | Stacked, 1 step ketuker per tampilan (step lain collapsed) | Horizontal, semua step kelihatan | Horizontal, semua step kelihatan |
| Galeri produk | Carousel full-width, thumbnail di-scroll horizontal | Carousel + thumbnail strip di bawah | Carousel + thumbnail strip di samping/bawah |

Test manual wajib di 3 breakpoint ini tiap page baru selesai dibuat (resize browser atau device toolbar) — bukan cuma asumsi "Tailwind responsive otomatis aman".

## 7. Autentikasi & Middleware Storefront

Storefront punya 2 jenis pengunjung: **guest** (gak login) dan **user** (login via Sanctum token). Hampir semua page tetap bisa diakses guest (sesuai flow guest checkout di spec utama §3) — jadi middleware-nya tipis, bukan blanket "semua page butuh login".

- **State auth**: token Sanctum disimpan di Pinia store (`useAuthStore`), persisted ke `localStorage`. Token dikirim sebagai header `Authorization: Bearer <token>` di tiap request API yang butuh identitas (logout, lihat alamat tersimpan, order history versi logged-in).
- **`guest-only` middleware** — dipasang di halaman Login & Register (`definePageMeta({ middleware: 'guest-only' })`). Kalau user udah punya token valid, redirect ke homepage (gak masuk akal nampilin form login ke yang udah login).
- **`auth` middleware** — dipasang **hanya** di halaman yang murni butuh akun (mis. "Alamat Tersimpan" management, kalau dibuat nanti). Checkout, Cart, Detail Produk, Order History **tidak** dipasangi middleware ini — Order History sendiri punya 2 mode tampilan: logged-in (auto-fetch daftar order milik user) vs guest (form input manual `order_number` + email buat lookup, sesuai spec §3).
- **Refresh/expired token**: kalau API balas 401, Pinia store clear token + redirect ke halaman sebelumnya dalam mode guest (bukan force-redirect ke login, biar gak motong flow checkout guest yang sah).

## 8. Admin Panel (Filament) — Sengaja Tidak Dispek Detail

Filament v5 sudah punya behavior baku sendiri: sidebar nav otomatis dari Resource yang didaftarkan, tabel dengan pagination (10/25/50 per halaman, built-in selector), global search + per-kolom filter, form builder dengan validasi. **Jangan bangun ulang dari nol** — tiap Resource (Produk, Kategori, Order, dst, di plan-plan berikutnya) tinggal pakai konvensi Filament standar (`Table::columns()`, `Table::filters()`, `Form::schema()`).

Custom Blade/komponen di admin **hanya** kalau Filament gak nyediain out-of-the-box (jarang terjadi utk CRUD standar kayak yang kita butuh).

## 7. Yang Sengaja Dicoret (YAGNI)

- Mini-cart slideover/dropdown preview — cart akses langsung ke halaman Cart (sesuai flow asli, gak nambah UI pattern baru tanpa kebutuhan jelas).
- Image zoom/lightbox di detail produk.
- Infinite-scroll otomatis (dipilih Load More button, lebih simpel & terkontrol).
- Custom design system token (spacing scale, dll) — pakai default Tailwind/Nuxt UI, jangan override kecuali ada alasan konkret.
