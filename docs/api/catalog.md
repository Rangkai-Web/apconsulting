# Catalog API

## GET /api/categories

Auth: none.

**Response 200**
```json
{
  "data": [
    { "id": 1, "name": "Pupuk", "slug": "pupuk", "parent_id": null }
  ]
}
```

## GET /api/products

Auth: none.

**Query params**
| Param | Type | Notes |
|---|---|---|
| `category` | string | category slug |
| `min`, `max` | int | price range, matched against cheapest variant |
| `search` | string | matches against product name |
| `sort` | `newest` (default) \| `price_asc` \| `price_desc` \| `rating` | |
| `page` | int | 8 products per page |
| `ids` | string | comma-separated product IDs, overrides other filters' product set |

**Response 200**
```json
{
  "data": [
    {
      "id": 1,
      "category_id": 1,
      "name": "Alpine Moisture Cream",
      "slug": "alpine-moisture-cream",
      "description": "...",
      "images": ["https://.../uploads/products/abc.jpg"],
      "is_active": true,
      "rating_avg": 4.6,
      "rating_count": 12,
      "variants": [
        { "id": 1, "name": "50ml", "sku": "AMC-50", "price": 89000, "stock": 40, "weight": 80, "image": null }
      ]
    }
  ],
  "meta": { "has_more": true, "next_page": 2, "total": 24 }
}
```

## GET /api/products/{slug}

Auth: none. 404 if not found or `is_active = false`.

**Response 200**: same shape as one item of `/api/products` `data[]`.
