<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name_id', 'name_en', 'sort_order', 'is_active'])]
class ServiceCategory extends Model
{
    use HasFactory;

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
