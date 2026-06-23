<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CatalogSeeder::class,
        ]);

        \App\Models\StoreSetting::firstOrCreate([], [
            'store_name' => 'Doctor Plant Indonesia',
            'logo' => null,
            'address' => '1st Floor, Jl. Artha Gading Sel. No.1, RT.18/RW.8, Klp. Gading Bar., Kec. Klp. Gading, Jkt Utara, Jakarta 14240',
            'phone' => '+62 812-9504-0884',
            'whatsapp_number' => '6281295040884',
            'instagram_url' => 'https://www.instagram.com/drplant_official_id/',
            'operating_hours' => 'Senin - Minggu, 10.00 - 22.00 WIB',
            'rajaongkir_origin_district_id' => 151,
        ]);

        \App\Models\Admin::firstOrCreate([
            'email' => 'admin@drplant.id',
        ], [
            'name' => 'Admin Dr. Plant',
            'password' => bcrypt('password'),
        ]);
    }
}
