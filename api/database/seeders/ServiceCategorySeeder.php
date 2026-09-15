<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_id' => 'Layanan Perpajakan',
                'name_en' => 'Taxation Services',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name_id' => 'Akuntansi & Pembukuan',
                'name_en' => 'Accounting & Bookkeeping',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name_id' => 'Dukungan Administrasi Bisnis',
                'name_en' => 'Business Administration Support',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name_id' => 'Layanan Keuangan Lainnya',
                'name_en' => 'Other Financial Services',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name_id' => 'Pertanyaan Umum',
                'name_en' => 'General Inquiry',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['name_en' => $category['name_en']],
                $category
            );
        }
    }
}
