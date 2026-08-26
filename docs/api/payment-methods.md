# Payment Methods API

## GET /api/payment-methods

Fetch active payment methods (bank/e-wallet/QRIS) for storefront checkout and order-detail display, ordered by `sort_order`. Managed in the admin panel via `PaymentMethodResource`.

**Auth:** none

**Response 200**
```json
{
  "data": [
    {
      "id": 1,
      "label": "BCA",
      "account_number": "8950505266",
      "account_holder": "Yulie Sarida H T",
      "qr_image": null,
      "instructions": "Transfer sesuai nominal tagihan.",
      "sort_order": 1
    }
  ]
}
```

| Field | Type | Notes |
|---|---|---|
| `label` | string | Method name shown to customer, e.g. "BCA", "ShopeePay", "QRIS" |
| `account_number` | string\|null | Bank account / e-wallet phone number |
| `account_holder` | string\|null | Account holder name |
| `qr_image` | string\|null | Absolute URL to a QR code image, or `null` |
| `instructions` | string\|null | Free-text extra instructions |
| `sort_order` | int | Display order hint; endpoint itself orders by `sort_order` ascending |

Only methods with `is_active = true` are returned.
