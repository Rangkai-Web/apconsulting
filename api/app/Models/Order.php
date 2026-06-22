<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_number', 'user_id', 'guest_name', 'guest_email', 'guest_phone',
    'shipping_address', 'courier', 'courier_service', 'shipping_cost',
    'subtotal', 'total', 'status', 'midtrans_order_id', 'midtrans_transaction_id',
    'tracking_number', 'shipped_at', 'completed_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'shipped_at' => 'datetime',
            'completed_at' => 'datetime',
            'subtotal' => 'integer',
            'total' => 'integer',
            'shipping_cost' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
