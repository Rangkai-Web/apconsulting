<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@pcgmitra.co.id'],
            [
                'name' => 'Admin PCG Mitra',
                'password' => Hash::make('PcG@dmin123!'),
            ]
        );
    }
}
