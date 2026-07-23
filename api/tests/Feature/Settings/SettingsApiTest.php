<?php

namespace Tests\Feature\Settings;

use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_public_store_settings(): void
    {
        StoreSetting::create([
            'store_name' => 'Test Dr. Plant Store',
            'logo' => 'settings/logo.png',
            'address' => 'Jl. Test No. 123',
            'phone' => '+62 812-3456-7890',
            'whatsapp_number' => '6281234567890',
            'instagram_url' => 'https://instagram.com/test_drplant',
            'operating_hours' => 'Senin - Jumat, 09.00 - 18.00 WIB',
            'rajaongkir_origin_district_id' => 151,
            'about_content' => '<p>Cerita brand test.</p>',
        ]);

        $response = $this->getJson('/api/settings');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'store_name' => 'Test Dr. Plant Store',
                    'logo' => url('uploads/settings/logo.png'), // APP_URL is dynamic in config
                    'address' => 'Jl. Test No. 123',
                    'phone' => '+62 812-3456-7890',
                    'whatsapp_number' => '6281234567890',
                    'instagram_url' => 'https://instagram.com/test_drplant',
                    'operating_hours' => 'Senin - Jumat, 09.00 - 18.00 WIB',
                    'about_content' => '<p>Cerita brand test.</p>',
                ]
            ])
            ->assertJsonMissing(['rajaongkir_origin_district_id'])
            ->assertJsonMissing(['id']);
    }

    public function test_returns_default_settings_when_none_exists(): void
    {
        // Table is empty, should firstOrCreate a default row
        $response = $this->getJson('/api/settings');

        $response->assertStatus(200)
            ->assertJsonPath('data.store_name', 'Doctor Plant Indonesia');
    }
}
