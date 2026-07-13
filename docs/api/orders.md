# Orders API

Endpoints for placing and tracking orders.

---

## POST /api/orders

Submits a checkout order from the cart. Open to both authenticated members and guest checkouts.

- For authenticated members: `address_id` is required (or full custom `shipping_address` object).
- For guests: `guest_name`, `guest_email`, `guest_phone`, and `shipping_address` are required.

**Request Body (Member example)**
```json
{
  "items": [
    { "variant_id": 1, "qty": 2 }
  ],
  "address_id": 1,
  "courier": "jne",
  "courier_service": "REG",
  "shipping_cost": 15000
}
```

**Request Body (Guest example)**
```json
{
  "guest_name": "John Doe",
  "guest_email": "john@example.com",
  "guest_phone": "0812950001",
  "items": [
    { "variant_id": 1, "qty": 1 }
  ],
  "shipping_address": {
    "recipient_name": "John Doe",
    "phone": "0812950001",
    "province": "Jawa Barat",
    "city": "Bandung",
    "district": "Coblong",
    "postal_code": "40135",
    "full_address": "Jl. Dago No. 100",
    "rajaongkir_district_id": 501
  },
  "courier": "jne",
  "courier_service": "REG",
  "shipping_cost": 15000
}
```

**Response 201**
```json
{
  "order_number": "ORD-20260625-ABCDEF",
  "snap_token": "mock-snap-token-xyz-12345"
}
```

**`shipping_cost` is not trusted from the client.** It's re-derived server-side from `rajaongkir_district_id` + cart weight + `courier`/`courier_service`, reusing the same cache as `POST /api/shipping/cost` (so a "Cek Ongkir" call made earlier in checkout is not re-billed). The submitted `shipping_cost` value is accepted for validation shape only and then discarded. This means `shipping_address.rajaongkir_district_id` must resolve to a real district — a missing/invalid value or a `courier`/`courier_service` combo that no longer returns a quote fails with:

**Response 422**
```json
{ "message": "The shipping address.rajaongkir district id field is required.", "errors": { "shipping_address": ["Kecamatan tujuan pengiriman tidak valid untuk cek ongkir."] } }
```
or
```json
{ "errors": { "courier_service": ["Kurir/layanan yang dipilih tidak lagi tersedia, cek ongkir ulang."] } }
```

---

## GET /api/orders

Lists order history for the authenticated member. Requires Sanctum.

**Response 200**
```json
{
  "data": [
    {
      "id": 1,
      "order_number": "ORD-20260625-ABCDEF",
      "status": "pending",
      "courier": "jne",
      "courier_service": "REG",
      "shipping_cost": 15000,
      "subtotal": 200000,
      "total": 215000,
      "tracking_number": null,
      "created_at": "2026-06-25T11:34:52.000000Z",
      "items": [
        {
          "id": 1,
          "product_variant_id": 1,
          "product_name": "Sunscreen",
          "variant_name": "50ml",
          "price": 100000,
          "qty": 2,
          "subtotal": 200000
        }
      ]
    }
  ]
}
```

---

## GET /api/orders/{order_number}

Gets details of an order. For members, must own the order. Guests should use `/orders/lookup`.

**Response 200** (Same structure as individual order entry above)

---

## POST /api/orders/{order_number}/snap-token

Requests a fresh Midtrans Snap token for a pending order (e.g. if the user closed the payment modal and wants to retry).

**Response 200**
```json
{
  "snap_token": "new-mock-snap-token-98765"
}
```

---

## GET /api/orders/lookup

Allows guests to track and retrieve their order status using their order number and email.

**Query Parameters**
- `order_number`: string (required)
- `email`: string (required)

**Response 200** (Same structure as individual order entry above)

---

## POST /api/webhooks/midtrans

Receives Midtrans payment notification callbacks and updates order status accordingly. Not called by the storefront directly — configured as the notification URL in the Midtrans dashboard.

**Auth:** none (verified instead via Midtrans `signature_key`, skipped only when `MIDTRANS_SERVER_KEY` is empty, e.g. local/testing)

Maps `transaction_status`/`fraud_status` to an order status: `capture`+`accept` or `settlement` → `paid`, `capture`+`challenge` or `pending` → `pending`, `deny`/`cancel` → `cancelled`, `expire` → `expired`. Idempotent — ignores cancel/expire callbacks for orders already `paid`/`processing`/`shipped`/`completed`, and releases reserved stock when a paid order transitions to `cancelled`/`expired`. On transition to `paid`, notifies all admins via Filament database notification and email (`OrderPaidNotification`).

**Response 200**
```json
{
  "message": "Webhook processed successfully.",
  "order_number": "ORD-20260625-ABCDEF",
  "status": "paid"
}
```

**Response 403** — invalid signature. **Response 404** — order not found for the given `order_id`.
