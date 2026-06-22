<?php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_all_categories(): void
    {
        Category::factory()->create(['name' => 'Pupuk', 'slug' => 'pupuk']);
        Category::factory()->create(['name' => 'Bibit', 'slug' => 'bibit']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['slug' => 'pupuk'])
            ->assertJsonFragment(['slug' => 'bibit']);
    }
}
