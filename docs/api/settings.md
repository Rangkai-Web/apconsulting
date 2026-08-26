# Settings API

## GET /api/settings

Fetch public store settings for storefront rendering.

**Auth:** none

**Response 200**
```json
{
  "data": {
    "store_name": "BbystarGO",
    "logo": "http://localhost:8000/uploads/settings/logo.png",
    "address": "",
    "phone": "",
    "whatsapp_number": "",
    "instagram_url": "",
    "operating_hours": ""
  }
}
```
