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
            'store_name' => 'BbystarGO',
            'logo' => null,
            'address' => '',
            'phone' => '',
            'whatsapp_number' => '',
            'instagram_url' => '',
            'operating_hours' => '',
            'rajaongkir_origin_district_id' => null,
        ]);

        \App\Models\Admin::firstOrCreate([
            'email' => 'admin@bbystargo.co.id',
        ], [
            'name' => 'Admin BbystarGO',
            'password' => bcrypt('AdminBbystarGO123@'),
        ]);

        // Seed default banners
        $banners = [
            [
                'title' => 'Belanja Tanpa Batas Negara',
                'subtitle' => 'Titip beli produk favoritmu dari Korea, China, Jepang, dan negara lain — aman sampai ke tanganmu.',
                'image' => 'https://images.unsplash.com/photo-1607082349566-187342175e2f?auto=format&fit=crop&w=1600&h=600&q=80',
                'link' => '/katalog',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Ready Stock, Kirim Langsung',
                'subtitle' => 'Barang sudah tersedia di gudang kami — pesan hari ini, diproses tanpa perlu menunggu impor.',
                'image' => 'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?auto=format&fit=crop&w=1600&h=600&q=80',
                'link' => '/katalog',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Pre Order Terpercaya',
                'subtitle' => 'Kami urus pemesanan langsung dari negara asal sampai bersih ke tanganmu.',
                'image' => 'https://images.unsplash.com/photo-1591370874773-6702e8f12fd8?auto=format&fit=crop&w=1600&h=600&q=80',
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
