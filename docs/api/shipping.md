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

**Response 422** — invalid `district_id`/`total_weight`, or store has no `rajaongkir_origin_district_id` configured:

```json
{ "message": "Toko belum mengatur lokasi asal pengiriman" }
```

**Response 503** — RajaOngkir call failed (timeout, non-2xx, malformed response):

```json
{ "message": "Gagal cek ongkir, coba lagi" }
```
