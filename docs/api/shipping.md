# Shipping API

## POST /api/shipping/cost

Auth: none.

Calculates shipping cost from the store's fixed origin to a destination district via RajaOngkir. Results are cached server-side per (origin, destination, weight) combination — repeat calls with the same inputs do not re-hit RajaOngkir within the cache window.

**Request body**

```json
{
  "district_id": 999,
  "total_weight": 1000
}
```

| Field | Type | Notes |
|---|---|---|
| `district_id` | int | RajaOngkir destination district ID, must exist in the local `rajaongkir_districts` cache table |
| `total_weight` | int | Total cart weight in grams, min 1 |

**Response 200**

```json
{
  "couriers": [
    { "courier": "jne", "service": "REG", "cost": 15000, "etd": "2-3" }
  ]
}
```

**Response 422** — two cases:

- Store has no `rajaongkir_origin_district_id` configured:
  ```json
  { "message": "Toko belum mengatur lokasi asal pengiriman" }
  ```
- Invalid `district_id`/`total_weight` (standard Laravel validation error shape):
  ```json
  { "message": "The district id field is required.", "errors": { "district_id": ["The district id field is required."] } }
  ```

**Response 503** — RajaOngkir call failed (timeout, non-2xx, malformed response):

```json
{ "message": "Gagal cek ongkir, coba lagi" }
```

---

## GET /api/shipping/provinces

Retrieves all provinces from the local cache database.

**Response 200**
```json
{
  "data": [
    { "id": 9, "name": "Jawa Barat" }
  ]
}
```

---

## GET /api/shipping/cities

Retrieves cities in a province.

**Query Parameters**
- `province_id`: int (required)

**Response 200**
```json
{
  "data": [
    { "id": 23, "province_id": 9, "name": "Bandung" }
  ]
}
```

---

## GET /api/shipping/districts

Retrieves districts in a city.

**Query Parameters**
- `city_id`: int (required)

**Response 200**
```json
{
  "data": [
    { "id": 501, "city_id": 23, "name": "Coblong" }
  ]
}
```
