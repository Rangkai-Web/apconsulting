<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => fake()->randomElement(['250ml', '500ml', '1kg']),
            'sku' => fake()->unique()->ean8(),
            'price' => fake()->numberBetween(10000, 100000),
            'stock' => fake()->numberBetween(0, 200),
            'weight' => fake()->numberBetween(100, 2000),
            'image' => null,
        ];
    }
}
