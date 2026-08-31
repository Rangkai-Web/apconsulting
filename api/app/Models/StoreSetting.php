<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'store_name',
    'logo',
    'address',
    'phone',
    'whatsapp_number',
    'line_url',
    'operating_hours',
    'rajaongkir_origin_district_id',
    'socials',
    'dp_rules',
])]
class StoreSetting extends Model
{
    protected $table = 'store_settings';

    protected $casts = [
        'socials' => 'array',
        'dp_rules' => 'array',
    ];
}
