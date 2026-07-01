# Panduan Setup Layanan Eksternal — PT Doctor Plant Indonesia

Dokumen ini berisi langkah-langkah yang perlu dilakukan client untuk mengaktifkan toko online secara penuh: ongkir real-time (RajaOngkir), pembayaran online (Midtrans), dan email notifikasi (Resend).

---

## 1. RajaOngkir — Cek Ongkir Real-Time

### Paket yang direkomendasikan: **Starter (Gratis)**

Cukup untuk toko kecil. Limit 100 hit API per hari — artinya 100x customer klik tombol "Cek Ongkir". Untuk awal, ini sudah sangat cukup. Kalau pesanan mulai ramai (ratusan per hari), baru upgrade ke **Pro (Rp 149.000/bulan)**.

Enterprise tidak perlu — fitur tambahnya (buat resi, pickup otomatis) untuk gudang besar.

### Cara daftar

1. Buka [rajaongkir.com](https://rajaongkir.com) → klik **Daftar Sekarang** di paket Starter
2. Isi data: nama, email, nomor HP, nama toko
3. Verifikasi email
4. Masuk ke dashboard → menu **API Key** → copy API Key

### Konfigurasi di server

Setelah dapat API Key, isi di file `.env` server (Hostinger):

```
RAJAONGKIR_API_KEY=isi_api_key_kamu_di_sini
```

---

## 2. Midtrans — Pembayaran Online

### Metode yang direkomendasikan untuk awal

| Metode | Biaya | Keterangan |
|--------|-------|------------|
| **Bank Transfer (VA)** | Rp 4.000/transaksi | BCA, BNI, BRI, Mandiri — paling umum di Indonesia |
| **QRIS** | 0,7%/transaksi | Satu QR untuk semua e-wallet (GoPay, ShopeePay, Dana, OVO, dll) |

Aktifkan keduanya — Bank Transfer untuk customer yang lebih suka transfer bank, QRIS untuk yang pakai e-wallet. Ini cover hampir semua segmen pembeli.

GoPay/ShopeePay langsung bisa diaktifkan nanti (biaya 2%/transaksi), tapi QRIS sudah bisa terima keduanya jadi tidak wajib dari awal.

### Cara daftar

1. Buka [dashboard.midtrans.com](https://dashboard.midtrans.com) → klik **Sign Up**
2. Isi data bisnis:
   - Nama lengkap pemilik
   - Email aktif
   - Nomor HP
   - Nama toko/bisnis
3. Verifikasi email → masuk dashboard

### Aktivasi akun (KYC)

Untuk bisa menerima pembayaran real (bukan sandbox), Midtrans memerlukan verifikasi bisnis:
- KTP pemilik
- NPWP (kalau ada) — tidak wajib untuk perseorangan
- Nomor rekening bank untuk pencairan dana
- Foto/scan dokumen legalitas (bisa pakai NIB dari OSS kalau ada)

Proses verifikasi biasanya 1–3 hari kerja.

### Cara ambil API Key

Setelah akun aktif:

1. Dashboard Midtrans → menu **Settings** → **Access Keys**
2. Ada dua mode:
   - **Sandbox** (untuk testing) — pakai dulu ini saat development
   - **Production** (live, uang nyata) — aktifkan setelah testing selesai
3. Copy **Server Key** dan **Client Key**

### Konfigurasi di server

```
MIDTRANS_SERVER_KEY=isi_server_key_kamu
MIDTRANS_CLIENT_KEY=isi_client_key_kamu
MIDTRANS_IS_PRODUCTION=false   # ganti ke true kalau sudah siap live
```

### Testing pembayaran (Sandbox)

Saat `MIDTRANS_IS_PRODUCTION=false`, gunakan akun/kartu test dari [dokumentasi Midtrans](https://docs.midtrans.com/docs/testing-payment):
- VA BCA sandbox: bayar berapa saja → otomatis sukses
- Nomor kartu test: `4811 1111 1111 1114` (Visa), CVV: `123`, expired: tanggal mana saja di masa depan

---

## 3. Resend — Email Notifikasi Admin

Resend digunakan untuk mengirim email ke admin setiap kali ada pesanan baru yang dibayar.

### Cara daftar

1. Buka [resend.com](https://resend.com) → **Sign Up** (gratis sampai 3.000 email/bulan)
2. Verifikasi email
3. Masuk dashboard

### Verifikasi domain (wajib)

Email hanya bisa dikirim dari domain yang sudah diverifikasi. Misalnya kalau mau kirim dari `noreply@drplant.id`:

1. Dashboard Resend → menu **Domains** → **Add Domain**
2. Masukkan domain: `drplant.id`
3. Resend akan memberikan beberapa record DNS (DKIM, SPF)
4. Tambahkan record DNS tersebut di panel hosting domain (Niagahoster, Cloudflare, dll)
5. Klik **Verify** → tunggu sampai status hijau (bisa 5–30 menit)

### Cara ambil API Key

Dashboard Resend → menu **API Keys** → **Create API Key** → copy

### Konfigurasi di server

```
RESEND_API_KEY=isi_api_key_resend_kamu
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="noreply@drplant.id"   # ganti dengan domain yang sudah diverifikasi
MAIL_FROM_NAME="Doctor Plant Indonesia"
```

---

## 4. Cron Job di Hostinger (Pesanan Expired Otomatis)

Sistem secara otomatis mengexpire pesanan yang tidak dibayar dalam 24 jam dan mengembalikan stok. Tapi ini butuh satu setting di Hostinger:

1. Login Hostinger → **hPanel** → **Advanced** → **Cron Jobs**
2. Klik **New Cron Job**
3. Pilih interval: **Every minute** (atau pilih custom `* * * * *`)
4. Isi command (sesuaikan path ke folder Laravel):
   ```
   cd /home/username/public_html/api && php artisan schedule:run >> /dev/null 2>&1
   ```
   Ganti `username` dan path sesuai akun Hostinger kamu.
5. Simpan

Tanpa cron ini, pesanan tidak akan expired otomatis.

---

## Urutan yang disarankan

1. **Daftar RajaOngkir Starter** → isi `RAJAONGKIR_API_KEY` → test cek ongkir di toko
2. **Daftar Midtrans** → isi key dengan mode Sandbox → test checkout sampai selesai
3. **Daftar Resend** → verifikasi domain → isi key → test email notifikasi
4. **Set cron job** di Hostinger
5. Kalau semua sudah oke di Sandbox → ganti `MIDTRANS_IS_PRODUCTION=true` → **LIVE**

---

## Ringkasan env yang perlu diisi di server

```dotenv
# RajaOngkir
RAJAONGKIR_API_KEY=

# Midtrans
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

# Resend
RESEND_API_KEY=
MAIL_MAILER=resend
MAIL_FROM_ADDRESS=noreply@drplant.id
MAIL_FROM_NAME="Doctor Plant Indonesia"
```

Kalau ada pertanyaan, hubungi developer.
