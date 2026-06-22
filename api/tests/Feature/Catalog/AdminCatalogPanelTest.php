<?php

namespace Tests\Feature\Catalog;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reach_the_categories_list_page(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/categories')
            ->assertSuccessful();
    }

    public function test_admin_can_reach_the_products_list_page(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/products')
            ->assertSuccessful();
    }

    public function test_deleting_a_category_with_products_is_blocked(): void
    {
        $admin = Admin::factory()->create();
        $category = \App\Models\Category::factory()->create();
        \App\Models\Product::factory()->create(['category_id' => $category->id]);

        \Livewire\Livewire::actingAs($admin, 'admin')
            ->test(\App\Filament\Resources\CategoryResource\Pages\ListCategories::class)
            ->callTableAction('delete', $category);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
