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
    'instagram_url',
    'operating_hours',
    'rajaongkir_origin_district_id',
])]
class StoreSetting extends Model
{
    protected $table = 'store_settings';
}
