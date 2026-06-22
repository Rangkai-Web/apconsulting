<?php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_active_products_with_pagination_meta(): void
    {
        Product::factory()->count(10)->create(['is_active' => true])
            ->each(fn (Product $p) => ProductVariant::factory()->create(['product_id' => $p->id, 'price' => 10000, 'stock' => 5]));
        Product::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/products?page=1');

        $response->assertStatus(200)
            ->assertJsonCount(8, 'data')
            ->assertJsonPath('meta.has_more', true)
            ->assertJsonPath('meta.total', 10);
    }

    public function test_filters_by_category_slug(): void
    {
        $catA = Category::factory()->create(['slug' => 'pupuk']);
        $catB = Category::factory()->create(['slug' => 'pestisida']);
        $productA = Product::factory()->create(['category_id' => $catA->id]);
        ProductVariant::factory()->create(['product_id' => $productA->id]);
        $productB = Product::factory()->create(['category_id' => $catB->id]);
        ProductVariant::factory()->create(['product_id' => $productB->id]);

        $response = $this->getJson('/api/products?category=pupuk');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $productA->id);
    }

    public function test_searches_by_name(): void
    {
        $match = Product::factory()->create(['name' => 'Pupuk NPK Mutiara']);
        ProductVariant::factory()->create(['product_id' => $match->id]);
        $noMatch = Product::factory()->create(['name' => 'Sprayer Elektrik']);
        ProductVariant::factory()->create(['product_id' => $noMatch->id]);

        $response = $this->getJson('/api/products?search=npk');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    public function test_filters_by_ids(): void
    {
        $products = Product::factory()->count(3)->create();
        foreach ($products as $p) {
            ProductVariant::factory()->create(['product_id' => $p->id]);
        }

        $response = $this->getJson('/api/products?ids='.$products[0]->id.','.$products[2]->id);

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_sorts_by_price_ascending_using_cheapest_variant(): void
    {
        $expensive = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $expensive->id, 'price' => 90000]);
        $cheap = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $cheap->id, 'price' => 10000]);

        $response = $this->getJson('/api/products?sort=price_asc');

        $response->assertStatus(200)->assertJsonPath('data.0.id', $cheap->id);
    }

    public function test_shows_a_single_product_with_its_variants(): void
    {
        $product = Product::factory()->create(['slug' => 'pupuk-npk-mutiara']);
        ProductVariant::factory()->create(['product_id' => $product->id, 'name' => '1kg']);

        $response = $this->getJson('/api/products/pupuk-npk-mutiara');

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', 'pupuk-npk-mutiara')
            ->assertJsonCount(1, 'data.variants');
    }

    public function test_returns_404_for_unknown_slug(): void
    {
        $response = $this->getJson('/api/products/does-not-exist');

        $response->assertStatus(404);
    }

    public function test_returns_404_for_inactive_product(): void
    {
        Product::factory()->create(['slug' => 'inactive-product', 'is_active' => false]);

        $response = $this->getJson('/api/products/inactive-product');

        $response->assertStatus(404);
    }
}
