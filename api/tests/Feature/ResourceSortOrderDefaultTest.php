<?php

namespace Tests\Feature;

use App\Filament\Resources\FaqResource\Pages\CreateFaq;
use App\Filament\Resources\ServiceCategoryResource\Pages\CreateServiceCategory;
use App\Filament\Resources\TeamResource\Pages\CreateTeam;
use App\Models\Admin;
use App\Models\Faq;
use App\Models\ServiceCategory;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResourceSortOrderDefaultTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = Admin::create([
            'name' => 'Admin PCG',
            'email' => 'admin@pcgmitra.co.id',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($admin, 'admin');
    }

    public function test_team_create_form_defaults_sort_order_to_next_number(): void
    {
        // When no teams exist, default should be 1
        Livewire::test(CreateTeam::class)
            ->assertSchemaStateSet(['sort_order' => 1]);

        // Create a team with sort_order = 5
        Team::create([
            'name' => 'Existing Member',
            'position' => 'Consultant',
            'sort_order' => 5,
            'is_active' => true,
        ]);

        // Next team should default to 5 + 1 = 6
        Livewire::test(CreateTeam::class)
            ->assertSchemaStateSet(['sort_order' => 6]);
    }

    public function test_faq_create_form_defaults_sort_order_to_next_number(): void
    {
        Livewire::test(CreateFaq::class)
            ->assertSchemaStateSet(['sort_order' => 1]);

        Faq::create([
            'question_id' => 'Pertanyaan 1',
            'answer_id' => 'Jawaban 1',
            'question_en' => 'Question 1',
            'answer_en' => 'Answer 1',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        Livewire::test(CreateFaq::class)
            ->assertSchemaStateSet(['sort_order' => 4]);
    }

    public function test_service_category_create_form_defaults_sort_order_to_next_number(): void
    {
        Livewire::test(CreateServiceCategory::class)
            ->assertSchemaStateSet(['sort_order' => 1]);

        ServiceCategory::create([
            'slug' => 'test-service',
            'name_id' => 'Layanan 1',
            'name_en' => 'Service 1',
            'tagline_id' => 'Tagline 1',
            'tagline_en' => 'Tagline 1',
            'description_id' => 'Desc 1',
            'description_en' => 'Desc 1',
            'scope_items_id' => ['Item 1'],
            'scope_items_en' => ['Item 1'],
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Livewire::test(CreateServiceCategory::class)
            ->assertSchemaStateSet(['sort_order' => 3]);
    }

    public function test_team_create_form_rejects_duplicate_sort_order(): void
    {
        Team::create([
            'name' => 'Existing Member',
            'position' => 'Consultant',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => 'New Member',
                'position' => 'Partner',
                'sort_order' => 1,
            ])
            ->call('create')
            ->assertHasFormErrors(['sort_order' => 'unique']);
    }
}
