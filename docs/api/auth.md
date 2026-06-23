# Auth & Profile API

## POST /api/register
Register a new customer account.

**Auth:** none

**Parameters:**
- `name` (string, required)
- `email` (string, required, unique)
- `phone` (string, nullable)
- `password` (string, required, min:8, confirmed)

**Response 201**
```json
{
  "user": {
    "id": 1,
    "name": "Budi Santoso",
    "email": "budi@example.com",
    "phone": "081234567890"
  },
  "token": "1|abcdef123456..."
}
```

---

## POST /api/login
Log in to an existing customer account.

**Auth:** none

**Parameters:**
- `email` (string, required)
- `password` (string, required)

**Response 200**
```json
{
  "user": {
    "id": 1,
    "name": "Budi Santoso",
    "email": "budi@example.com",
    "phone": "081234567890"
  },
  "token": "2|abcdef123456..."
}
```

---

## POST /api/logout
Log out of the current customer session and revoke the active token.

**Auth:** `Bearer token` (`auth:sanctum`)

**Response 200**
```json
{
  "message": "Logged out"
}
```

---

## GET /api/profile
Retrieve the authenticated customer's profile details.

**Auth:** `Bearer token` (`auth:sanctum`)

**Response 200**
```json
{
  "data": {
    "id": 1,
    "name": "Budi Santoso",
    "email": "budi@example.com",
    "phone": "081234567890"
  }
}
```

---

## PUT /api/profile
Update the authenticated customer's profile details (excludes email).

**Auth:** `Bearer token` (`auth:sanctum`)

**Parameters:**
- `name` (string, required)
- `phone` (string, nullable)

**Response 200**
```json
{
  "data": {
    "id": 1,
    "name": "Budi Santoso Baru",
    "email": "budi@example.com",
    "phone": "08999999999"
  }
}
```

---

## PUT /api/profile/password
Change the authenticated customer's password.

**Auth:** `Bearer token` (`auth:sanctum`)

**Parameters:**
- `current_password` (string, required)
- `password` (string, required, confirmed, min:8)

**Response 200**
```json
{
  "message": "Password berhasil diperbarui."
}
```
