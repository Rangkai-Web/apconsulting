<?php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_links_a_product_to_its_category_and_variants(): void
    {
        $category = Category::factory()->create(['name' => 'Pupuk']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Pupuk NPK',
        ]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'name' => '1kg',
            'price' => 25000,
            'stock' => 100,
            'weight' => 1000,
        ]);

        $this->assertEquals($category->id, $product->category->id);
        $this->assertEquals($variant->id, $product->variants->first()->id);
        $this->assertEquals($product->id, $variant->product->id);
    }

    public function test_allows_a_variant_to_have_its_own_nullable_image_defaulting_to_null(): void
    {
        $variant = ProductVariant::factory()->create(['image' => null]);

        $this->assertNull($variant->image);

        $variant->update(['image' => 'variants/foo.jpg']);

        $this->assertEquals('variants/foo.jpg', $variant->refresh()->image);
    }

    public function test_supports_nested_categories_via_parent_id(): void
    {
        $parent = Category::factory()->create(['name' => 'Pertanian']);
        $child = Category::factory()->create(['name' => 'Pupuk', 'parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent->id);
    }

    public function test_defaults_rating_avg_and_rating_count_to_zero_on_a_new_product(): void
    {
        $product = Product::factory()->create();

        $this->assertEquals(0.00, (float) $product->rating_avg);
        $this->assertEquals(0, $product->rating_count);
    }
}
