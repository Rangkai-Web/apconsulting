<?php

namespace Tests\Feature\Settings;

use App\Models\Banner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_active_banners_sorted_by_created_at_desc(): void
    {
        // 1. Create banners and manually set created_at to bypass mass-assignment protection
        $bannerSecond = Banner::create([
            'title' => 'Banner Second',
            'subtitle' => 'This should be second',
            'image' => 'banners/second.jpg',
            'link' => '/katalog',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        $bannerSecond->created_at = now()->subMinutes(5);
        $bannerSecond->save();

        $bannerFirst = Banner::create([
            'title' => 'Banner First',
            'subtitle' => 'This should be first',
            'image' => 'banners/first.jpg',
            'link' => '/katalog',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $bannerFirst->created_at = now();
        $bannerFirst->save();

        // Inactive banner
        $bannerInactive = Banner::create([
            'title' => 'Banner Inactive',
            'subtitle' => 'Should not be seen',
            'image' => 'banners/inactive.jpg',
            'link' => '/katalog',
            'sort_order' => 0,
            'is_active' => false,
        ]);
        $bannerInactive->created_at = now()->addMinutes(5);
        $bannerInactive->save();

        // 2. Fetch banners
        $response = $this->getJson('/api/banners');

        // 3. Assertions
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.title', 'Banner First')
            ->assertJsonPath('data.1.title', 'Banner Second')
            ->assertJsonPath('data.0.image', url('uploads/banners/first.jpg'))
            ->assertJsonPath('data.1.image', url('uploads/banners/second.jpg'));
    }
}
