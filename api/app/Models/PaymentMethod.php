<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'label',
    'account_number',
    'account_holder',
    'qr_image',
    'instructions',
    'sort_order',
    'is_active',
])]
class PaymentMethod extends Model
{
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
