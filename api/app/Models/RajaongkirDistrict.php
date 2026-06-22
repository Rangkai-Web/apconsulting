<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id', 'city_id', 'name'])]
class RajaongkirDistrict extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';

    public function city(): BelongsTo
    {
        return $this->belongsTo(RajaongkirCity::class, 'city_id');
    }
}
