<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id', 'name'])]
class RajaongkirProvince extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';

    public function cities(): HasMany
    {
        return $this->hasMany(RajaongkirCity::class, 'province_id');
    }
}
