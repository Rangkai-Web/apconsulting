# Ratings API

Requires Sanctum authentication (`Bearer <token>`).

---

## POST /api/order-items/{id}/rating

Allows a user to submit a star rating (1-5) for a purchased item. The order must be `completed`, owned by the user, and the item must not have been rated yet.

**Request Body**
```json
{
  "stars": 5
}
```

**Response 201**
```json
{
  "message": "Terima kasih atas rating Anda!",
  "rating": {
    "stars": 5,
    "rating_avg": 4.5,
    "rating_count": 12
  }
}
```
