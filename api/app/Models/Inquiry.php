<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'full_name',
    'email',
    'phone',
    'company',
    'service_interest',
    'message',
    'status',
])]
class Inquiry extends Model
{
    use HasFactory;
}
