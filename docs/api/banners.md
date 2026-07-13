# Banners API

## GET /api/banners

Fetch active homepage banners for storefront rendering, ordered by `created_at` descending. Managed in the admin panel via `BannerResource`.

**Auth:** none

**Response 200**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Diskon Akhir Tahun",
      "subtitle": "Semua produk pupuk organik",
      "image": "http://localhost:8000/uploads/banners/promo.jpg",
      "link": "/katalog?category=pupuk-organik",
      "sort_order": 0
    }
  ]
}
```

| Field | Type | Notes |
|---|---|---|
| `title` | string\|null | |
| `subtitle` | string\|null | |
| `image` | string | Absolute URL — resolved from storage disk, or passed through as-is if already a full URL |
| `link` | string\|null | Relative or absolute URL the banner should navigate to on click |
| `sort_order` | int | Display order hint; endpoint itself orders by `created_at desc` |

Only banners with `is_active = true` are returned.
