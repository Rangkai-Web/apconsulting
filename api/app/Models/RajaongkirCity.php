<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id', 'province_id', 'name'])]
class RajaongkirCity extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';

    public function province(): BelongsTo
    {
        return $this->belongsTo(RajaongkirProvince::class, 'province_id');
    }

    public function districts(): HasMany
    {
        return $this->hasMany(RajaongkirDistrict::class, 'city_id');
    }
}
