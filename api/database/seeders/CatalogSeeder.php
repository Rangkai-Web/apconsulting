<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Hidrasi & Pelembab' => 'hidrasi-pelembab',
            'Sheet Mask' => 'sheet-mask',
            'Perawatan Alpine' => 'perawatan-alpine',
        ];

        $categoryModels = [];
        foreach ($categories as $name => $slug) {
            $categoryModels[$slug] = Category::create(['name' => $name, 'slug' => $slug]);
        }

        $products = [
            [
                'category' => 'hidrasi-pelembab',
                'name' => 'Alpine Moisture Cream',
                'slug' => 'alpine-moisture-cream',
                'description' => 'Krim pelembab dengan ekstrak tumbuhan alpine untuk hidrasi mendalam.',
                'variants' => [
                    ['name' => '50ml', 'sku' => 'AMC-50', 'price' => 89000, 'stock' => 40, 'weight' => 80],
                    ['name' => '100ml', 'sku' => 'AMC-100', 'price' => 149000, 'stock' => 25, 'weight' => 150],
                ],
            ],
            [
                'category' => 'sheet-mask',
                'name' => 'Glacier Hydrating Sheet Mask',
                'slug' => 'glacier-hydrating-sheet-mask',
                'description' => 'Sheet mask dengan essence glacier water, melembabkan instan dalam 15 menit.',
                'variants' => [
                    ['name' => '1 lembar', 'sku' => 'GHS-1', 'price' => 15000, 'stock' => 100, 'weight' => 30],
                    ['name' => 'Box isi 5', 'sku' => 'GHS-5', 'price' => 70000, 'stock' => 30, 'weight' => 150],
                ],
            ],
            [
                'category' => 'perawatan-alpine',
                'name' => 'Alpine Botanical Serum',
                'slug' => 'alpine-botanical-serum',
                'description' => 'Serum dengan kandungan botanical alpine untuk kulit lebih kenyal dan cerah.',
                'variants' => [
                    ['name' => '30ml', 'sku' => 'ABS-30', 'price' => 165000, 'stock' => 20, 'weight' => 60],
                ],
            ],
        ];

        foreach ($products as $data) {
            $product = Product::create([
                'category_id' => $categoryModels[$data['category']]->id,
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'images' => [],
                'is_active' => true,
            ]);

            foreach ($data['variants'] as $variant) {
                $product->variants()->create($variant);
            }
        }
    }
}
