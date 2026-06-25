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

        // Seed default banners
        $banners = [
            [
                'title' => 'Perawatan Kulit Alami Berbasis Alpine Plants',
                'subtitle' => 'Rangkaian produk hidrasi premium dari ekstrak tanaman pegunungan tinggi untuk kulit sehat berseri.',
                'image' => 'https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=1600&h=600&q=80',
                'link' => '/katalog',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Sheet Mask Mewah untuk Hidrasi Instan',
                'subtitle' => 'Mengembalikan kelembaban optimal dan menyegarkan kulit lelah dalam 15 menit.',
                'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=1600&h=600&q=80',
                'link' => '/katalog',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Layanan Facial Treatment Eksklusif',
                'subtitle' => 'Perawatan kulit mendalam dengan teknologi ultrasound dan terapis berpengalaman di klinik kami.',
                'image' => 'https://images.unsplash.com/photo-1519699047748-de8e457a634e?auto=format&fit=crop&w=1600&h=600&q=80',
                'link' => '/katalog',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $bannerData) {
            \App\Models\Banner::firstOrCreate(['title' => $bannerData['title']], $bannerData);
        }
    }
}
