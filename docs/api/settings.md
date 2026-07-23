# Settings API

## GET /api/settings

Fetch public store settings for storefront rendering.

**Auth:** none

**Response 200**
```json
{
  "data": {
    "store_name": "Doctor Plant Indonesia",
    "logo": "http://localhost:8000/uploads/settings/logo.png",
    "address": "1st Floor, Jl. Artha Gading Sel. No.1, RT.18/RW.8, Klp. Gading Bar., Kec. Klp. Gading, Jkt Utara, Jakarta 14240",
    "phone": "+62 812-9504-0884",
    "whatsapp_number": "6281295040884",
    "instagram_url": "https://www.instagram.com/drplant_official_id/",
    "operating_hours": "Senin - Minggu\n10.00 - 22.00 WIB",
    "about_content": "<p>Doctor Plant adalah merek skincare asal Tiongkok...</p>"
  }
}
```

`about_content` — HTML string dari RichEditor (admin panel Settings), dipakai storefront halaman `/about`. `null` kalau belum diisi admin (storefront fallback ke teks default).
