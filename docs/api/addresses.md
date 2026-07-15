# Address API

Endpoints for managing member shipping addresses. All endpoints require Sanctum authentication (`Bearer <token>`).

---

## GET /api/addresses

Retrieves all saved addresses for the authenticated member.

**Response 200**
```json
{
  "data": [
    {
      "id": 1,
      "label": "Rumah",
      "recipient_name": "Budi Santoso",
      "phone": "0812345678",
      "province": "Jawa Barat",
      "city": "Bandung",
      "district": "Coblong",
      "postal_code": "40135",
      "full_address": "Apartment Dago Suite No. 10B",
      "is_default": true,
      "rajaongkir_district_id": 501
    }
  ]
}
```

---

## POST /api/addresses

Saves a new shipping address. If it's the first saved address, it is forced to be the default.

**Request Body**
```json
{
  "label": "Kantor",
  "recipient_name": "Budi Santoso",
  "phone": "0812345678",
  "province": "Jawa Barat",
  "city": "Bandung",
  "district": "Coblong",
  "postal_code": "40135",
  "full_address": "Jl. Ganesha No. 10",
  "is_default": true,
  "rajaongkir_district_id": 501
}
```

**Response 201**
```json
{
  "data": {
    "id": 2,
    "label": "Kantor",
    "recipient_name": "Budi Santoso",
    "phone": "0812345678",
    "province": "Jawa Barat",
    "city": "Bandung",
    "district": "Coblong",
    "postal_code": "40135",
    "full_address": "Jl. Ganesha No. 10",
    "is_default": true,
    "rajaongkir_district_id": 501
  }
}
```

---

## PUT /api/addresses/{id}

Updates an existing saved address.

**Request Body** (Same as POST)

**Response 200** (Same structure as POST)

---

## DELETE /api/addresses/{id}

Deletes a saved address. If the deleted address was the default address, the default status is automatically transferred to another remaining address if possible.

**Response 200**
```json
{
  "message": "Alamat berhasil dihapus."
}
```
