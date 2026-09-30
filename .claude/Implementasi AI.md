# Rencana Implementasi AI Business Intelligence & Monitoring (GA4 Integration)

Dokumen ini berisi rencana implementasi fitur AI Business Intelligence pada website Company Profile. AI bertindak sebagai **Virtual Business Analyst** yang menghubungkan data **Google Analytics 4 (GA4)** dan data **Inquiry/Contact Lead** internal.

---

## 🎯 1. Tujuan Implementasi AI

1. **Rangkuman Eksekutif Otomatis**: Menyajikan analisa statistik performa website dalam bahasa bisnis yang santun & profesional di Dashboard Filament Admin tanpa perlu membuka GA4 yang rumit.
2. **Identifikasi Asal Lead & Calon Klien**: Menganalisis artikel/halaman layanan mana yang paling menarik minat calon klien dan sumber mana (SEO, Medsos, Direct) yang menghasilkan formulir kontak/inquiry terbanyak.
3. **Pusat Tanya Jawab Data (Interactive AI Assistant)**: Memberikan fasilitas bagi pemilik web untuk melakukan tanya-jawab (_chat_) interaktif seputar tren statistik website.

---

## 🚀 2. Fitur-Fitur AI yang Bekerja

### A. Automatic Weekly/Daily Business Digest (Widget Dashboard)

- Widget khusus di halaman utama Filament Admin Panel.
- Otomatis menguraikan:
  - **Traffic Overview**: Tren kenaikan/penurunan pengunjung minggu ini.
  - **Top Content**: Artikel & Layanan paling banyak dibaca.
  - **Conversion/Leads**: Jumlah calon klien yang mengisi form kontak.
  - **Actionable Advice**: 2-3 saran dari AI untuk tim marketing (contoh: _"Artikel 'Layanan X' tinggi visitor tapi sepi inquiry, pertimbangkan menambah tombol CTA"_).

### B. Interactive AI Chat Assistant (Filament Chat Panel)

- Halaman/Slide-over Chat interaktif di dalam Filament Admin.
- Pemilik web/admin bisa bertanya langsung dengan Bahasa Indonesia natural, seperti:
  - _"Pengunjung minggu ini paling banyak dari kota mana?"_
  - _"Berapa banyak orang yang isi form kontak dalam 7 hari terakhir?"_
  - _"Artikel mana yang trennya paling bagus bulan ini?"_

---

## 🔄 3. Alur Kerja (Flow & Interaksi)

```mermaid
flowchart TD
    subgraph Data Sources
        GA[Google Analytics 4 Data API]
        DB[(Database Laravel: Inquiries & Articles)]
    end

    subgraph Backend Logic
        Cron[Scheduled Cron Job / Data Fetcher]
        Service[AnalyticsAggregatorService]
        AI Engine[AI Provider API - Gemini / OpenAI]
    end

    subgraph User Interface Filament
        Widget[Dashboard AI Widget]
        Chat[Interactive AI Chat Drawer]
    end

    GA -->|Daily Metrics| Cron
    DB -->|Lead & Article Counts| Cron
    Cron --> Service
    Service -->|Formatted JSON Prompt| AI Engine

    AI Engine -->|Generated Summary| Widget

    User([Admin / Business Owner]) <-->|Nanya via Chat| Chat
    Chat <-->|Fetch Context Data & Prompt| AI Engine
```

### Flow 1: Laporan Otomatis (Background Job)

1. Setiap Senin pukul 07:00 pagi, Laravel Cron mengambil metrics 7 hari terakhir dari **GA4 Data API** + database **Inquiries**.
2. Service mengagregasikan data menjadi format ringkas (JSON), lalu dikirim ke **AI API**.
3. Hasil respon AI disimpan di database lokal dan ditampilkan di Widget Dashboard Admin.

### Flow 2: Chat Interaktif dengan AI (User Initiated)

1. Admin membuka panel **"Ask AI Analyst"** di Filament.
2. Admin mengetik pertanyaan.
3. Backend menyertakan rangkuman data analitik terkini sebagai _context_ ke AI API.
4. AI merespon jawaban secara _real-time_ ke panel chat.

---

## 🤖 4. Rekomendasi API AI & Estimasi Biaya (Cost Estimation)

### Pilihan API AI Utama: **Google Gemini 2.5 Flash API** _(Recommended)_

Why Gemini?

- **Sangat Cepat & Responsif**: Cocok untuk chat & background job.
- **Harga Terjangkau & Kuota Gratis**: Sangat efisien untuk penggunaan Company Profile.

### 💰 Estimasi Biaya Pemakaian per Bulan

| Provider & Model                                  | Harga Token (Input / Output per 1M)            | Estimasi Pemakaian (Company Profile)    | Biaya Estimasi per Bulan                                                            |
| :------------------------------------------------ | :--------------------------------------------- | :-------------------------------------- | :---------------------------------------------------------------------------------- |
| **Google Gemini 2.5 Flash** _(Rekomendasi Utama)_ | **$0.075** / 1M input<br>**$0.30** / 1M output | ~500k input token<br>~100k output token | **Gratis** _(Free Tier s.d 15 RPM)_ / **< $0.50** (sekitar Rp 5.000 - Rp 8.000/bln) |
| **OpenAI GPT-4o-mini**                            | **$0.15** / 1M input<br>**$0.60** / 1M output  | ~500k input token<br>~100k output token | **$1.00 - $2.00** (sekitar Rp 15.000 - Rp 30.000/bln)                               |
| **Anthropic Claude 3.5 Haiku**                    | **$0.80** / 1M input<br>**$4.00** / 1M output  | ~500k input token<br>~100k output token | **$3.00 - $5.00** (sekitar Rp 45.000 - Rp 75.000/bln)                               |

> **Kesimpulan Biaya**: Untuk skala Company Profile, penggunaan **Google Gemini API** hampir tidak berbiaya (atau kurang dari Rp 10.000,- per bulan), menjadikannya pilihan paling hemat dan aman bagi client.

---

## 🛠️ 5. Komponen yang Akan Dibuat dalam Project

### Backend (`api/`)

1. **Google Analytics Service**: Integrasi SDK `google/analytics-data` via Service Account.
2. **AI Service**: Driver Service untuk Gemini API (`GeminiService.php`).
3. **Scheduled Artisan Command**: `php artisan ai:generate-weekly-report`.
4. **Database Migration**: Tabel `ai_reports` dan `ai_chat_messages`.

### Admin Panel (`api/app/Filament/`)

1. **Dashboard Widget**: `AiBusinessInsightWidget.php`.
2. **Chat Page / Slide-over**: Page kustom `AiAnalystChat.php`.

---

## ❓ User Review & Confirmation

Mohon berikan masukan atau persetujuan untuk rencana di atas:

1. Apakah fitur **AI Dashboard Widget** + **Interactive Chat Panel** ini sudah sesuai dengan kebutuhan client?
2. Apakah rekomendasi **Google Gemini API** disetujui untuk digunakan sebagai provider AI-nya?
