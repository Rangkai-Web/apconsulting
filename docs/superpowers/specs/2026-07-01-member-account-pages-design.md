# Halaman Akun Member (Profil + Alamat) — Design

Status: approved, pending implementation plan.
Related: `docs/superpowers/specs/2026-06-19-toko-online-design.md` §5 (Profile & Address API).

## 1. Tujuan

Member butuh halaman untuk mengelola data diri (nama, telepon, password) dan alamat tersimpan (tambah/edit/hapus/set default). API sudah lengkap di backend — ini murni storefront UI.

## 2. Scope

**In:** `/akun/profil` (edit profile + ganti password), `/akun/alamat` (CRUD saved addresses), `profileService.ts`, `authStore.updateProfile()`, header dropdown links.

**Out:** Email change (tidak ada di API). Address autofill dari checkout ke saved addresses. Shared AddressForm component (YAGNI — checkout punya inline form sendiri). Vitest tests untuk Vue pages (pola existing tidak ada component tests).

## 3. Routing & Navigation

```
/akun/profil   → storefront/app/pages/akun/profil.vue   (auth middleware)
/akun/alamat   → storefront/app/pages/akun/alamat.vue   (auth middleware)
```

Header dropdown (`AppHeader.vue`) tambah 2 item di group pertama:
```
- Profil Saya    → /akun/profil
- Alamat Saya    → /akun/alamat
- Pesanan Saya   → /orders/history  (existing)
```

Tiap page render tab navigasi inline (dua tab: Profil | Alamat) menggunakan array link yang di-hardcode — bukan shared layout.

## 4. `/akun/profil`

### 4.1 Info Profil

Form fields:
- **Nama** (required, string max:255)
- **Telepon** (optional, string max:20)
- **Email** — ditampilkan read-only (tidak bisa diubah, tidak ada di `PUT /api/profile`)

Submit → `PUT /api/profile` → `profileService.updateProfile({ name, phone })`.

Setelah sukses: update `authStore.user.name` dan `authStore.user.phone` via action `updateProfile(data)` baru di auth store. Persist ke localStorage.

### 4.2 Ganti Password

Form fields terpisah dalam card/section berbeda:
- **Password Saat Ini** (required)
- **Password Baru** (required, min 8 karakter)
- **Konfirmasi Password Baru** (required, match dengan Password Baru)

Submit → `PUT /api/profile/password` → `profileService.updatePassword({ current_password, password, password_confirmation })`.

Error dari API (422 `current_password` wrong) ditampilkan inline di field "Password Saat Ini".

### 4.3 Service

Baru: `storefront/app/services/profileService.ts`

```ts
export const profileService = {
  async getProfile(): Promise<{ name: string; email: string; phone: string }>,
  async updateProfile(data: { name: string; phone?: string }): Promise<{ name: string; email: string; phone: string }>,
  async updatePassword(data: { current_password: string; password: string; password_confirmation: string }): Promise<void>,
}
```

Semua call pakai Bearer token dari `authStore.token` (sama persis pola `addressService.ts` — `getHeaders()` function).

### 4.4 Auth Store Update

Tambah action ke `storefront/app/store/auth.ts`:

```ts
updateProfile(data: { name: string; phone?: string }) {
  if (this.user) {
    this.user.name = data.name;
    this.user.phone = data.phone ?? this.user.phone;
    this.persist();
  }
}
```

`User` type di `~/types/index.ts` sudah punya `name`, `email`, `phone` — tidak perlu diubah.

## 5. `/akun/alamat`

### 5.1 List Addresses

Load dari `GET /api/addresses` via `addressService.getAddresses()` on mount.

Tiap address card tampilkan:
- Label (atau "Alamat" kalau kosong)
- Nama penerima
- Kota, Provinsi
- Badge "Utama" kalau `isDefault === true`
- Tombol: "Edit" (buka modal edit), "Hapus" (konfirmasi → delete)
- Tombol "Jadikan Utama" (kalau bukan default) → `PUT /api/addresses/{id}` dengan `is_default: true`

Tombol "+ Tambah Alamat" di atas list → buka UModal tambah.

### 5.2 Modal Tambah/Edit

Satu UModal reusable di dalam page (v-if mode, bukan komponen terpisah). State `modalMode: 'add' | 'edit'` + `editingAddress: Address | null`.

Form fields:
- **Label** (optional, contoh: "Rumah", "Kantor")
- **Nama Penerima** (required)
- **Telepon** (required)
- **Provinsi** → dropdown, load dari `addressService.getProvinces()` (cache di ref setelah load pertama)
- **Kota** → dropdown, cascade dari Provinsi, load dari `addressService.getCities(provinceId)`
- **Kecamatan** → dropdown, cascade dari Kota, load dari `addressService.getDistricts(cityId)`
- **Kode Pos** (required)
- **Alamat Lengkap** (required, textarea)
- **Jadikan Alamat Utama** (toggle/checkbox)

Submit tambah → `addressService.createAddress()`. Submit edit → `addressService.updateAddress(id, data)`. Reload list setelah sukses.

Cascade logic:
- Ubah Provinsi → reset Kota, Kecamatan, fetch cities baru
- Ubah Kota → reset Kecamatan, fetch districts baru
- `rajaongkir_district_id` diisi dari ID district yang dipilih

### 5.3 Hapus Address

Klik "Hapus" → inline konfirmasi singkat (UButton danger + konfirmasi text, atau UModal kecil). Confirm → `addressService.deleteAddress(id)` → reload list.

Tidak bisa hapus address kalau hanya tinggal satu (server-side: jika hanya 1 address, `is_default` di-force true tapi tidak diblock delete — client cukup reload dan tampilkan list kosong).

## 6. Files

| Action | Path |
|--------|------|
| Create | `storefront/app/pages/akun/profil.vue` |
| Create | `storefront/app/pages/akun/alamat.vue` |
| Create | `storefront/app/services/profileService.ts` |
| Modify | `storefront/app/store/auth.ts` — tambah `updateProfile()` action |
| Modify | `storefront/app/components/AppHeader.vue` — tambah 2 dropdown items |

## 7. UI Patterns

Ikut pola halaman existing:
- `definePageMeta({ middleware: 'auth' })` — sama seperti `/orders/history`
- `useToast()` untuk success/error feedback — sama seperti checkout dan order pages
- `UCard`, `UButton`, `UFormField`, `UInput`, `USelect`, `UModal` dari Nuxt UI v4
- Loading state dengan `ref(true)` → false setelah fetch
- Error display pakai toast + inline kalau validasi 422

## 8. Out of scope

- Perubahan email member (tidak ada endpoint)
- Upload foto profil
- Verifikasi email/telepon
- Shared `AddressForm.vue` component — checkout punya implementasi inline sendiri
- Vitest component tests — pola existing tidak punya
