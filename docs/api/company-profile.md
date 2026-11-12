# Company Profile API Reference

Endpoint publik REST API untuk website Company Profile AP Consulting. Semua endpoint mendukung lokalisasi dua bahasa (`lang=id` atau `lang=en`).

## Endpoints Summary

| Method | Endpoint | Query Param | Auth | Description |
|---|---|---|---|---|
| `GET` | `/api/faqs` | `lang=id\|en` | None | Mengambil daftar FAQ aktif (bilingual ID/EN) |
| `GET` | `/api/articles` | `lang=id\|en`, `category`, `search`, `page` | None | Mengambil daftar artikel terbit (bilingual ID/EN) |
| `GET` | `/api/articles/{slug}` | `lang=id\|en` | None | Detail artikel berdasarkan slug ID atau EN |
| `GET` | `/api/team` | - | None | Mengambil daftar tim profesional aktif |
| `GET` | `/api/settings` | - | None | Mengambil pengaturan kontak & media sosial |
| `POST` | `/api/contact` | - | None | Mengirim pesan dari form kontak (throttled: 10/min) |

---

## 1. GET /api/faqs

Mengembalikan daftar FAQ dengan bahasa yang disesuaikan via query `lang` (default: `id`).

**Query Parameters:**
- `lang` (optional): `id` (Bahasa Indonesia) atau `en` (English).

**Response Bahasa Indonesia (`GET /api/faqs?lang=id`):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "question": "Apa saja layanan utama yang ditawarkan oleh AP Consulting?",
      "answer": "AP Consulting menawarkan layanan konsultasi perpajakan berbasis hukum, akuntansi & pembukuan, serta penyelesaian permasalahan perpajakan untuk badan usaha maupun perorangan.",
      "category": "Umum",
      "sort_order": 1,
      "is_active": true
    }
  ]
}
```

**Response English (`GET /api/faqs?lang=en`):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "question": "What core services does AP Consulting provide?",
      "answer": "AP Consulting provides legal-based tax consulting, accounting & bookkeeping, and tax compliance & dispute resolution for corporate and individual clients.",
      "category": "Umum",
      "sort_order": 1,
      "is_active": true
    }
  ]
}
```

---

## 2. GET /api/articles

Mengembalikan daftar artikel terbit dengan bahasa yang disesuaikan (`lang=id|en`).

**Query Parameters:**
- `lang` (optional): `id` atau `en`
- `category` (optional): Filter kategori artikel
- `search` (optional): Kata kunci pencarian judul/konten
- `page` (optional): Nomor halaman (default: `1`)

**Response English (`GET /api/articles?lang=en`):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Corporate Tax Reporting Guide 2026",
      "slug": "corporate-tax-reporting-guide-2026",
      "excerpt": "A comprehensive summary of tax reporting...",
      "content": "<p>Full content in English...</p>",
      "cover_image": "articles/cover-1.jpg",
      "category": "Perpajakan",
      "author_name": "Admin AP Consulting",
      "published_at": "2026-09-09T12:00:00.000000Z",
      "views_count": 150
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

---

## 3. GET /api/articles/{slug}

Mengembalikan detail artikel berdasarkan `slug_id` atau `slug_en`.

**Query Parameters:**
- `lang` (optional): `id` atau `en`

**Response (`GET /api/articles/corporate-tax-reporting-guide-2026?lang=en`):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "title": "Corporate Tax Reporting Guide 2026",
    "slug": "corporate-tax-reporting-guide-2026",
    "excerpt": "A comprehensive summary...",
    "content": "<p>Full content...</p>",
    "cover_image": "articles/cover-1.jpg",
    "category": "Perpajakan",
    "author_name": "Admin AP Consulting",
    "published_at": "2026-09-09T12:00:00.000000Z",
    "views_count": 151
  }
}
```

---

## 4. GET /api/team

Mengembalikan daftar anggota tim profesional AP Consulting.

---

## 5. GET /api/settings

Mengembalikan informasi kontak kantor dan media sosial.

---

## 6. POST /api/contact

Mengirim pesan dari formulir kontak. Pesan tersimpan di database `inquiries` dan mengembalikan URL WhatsApp terformat dengan sapaan resmi AP Consulting.
