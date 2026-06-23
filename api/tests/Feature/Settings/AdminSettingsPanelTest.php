<?php

namespace Tests\Feature\Settings;

use App\Models\Admin;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_page(): void
    {
        $response = $this->get('/admin/settings-page');

        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_admin_can_access_settings_page(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/settings-page');

        $response->assertStatus(200)
            ->assertSee('Pengaturan Toko')
            ->assertSee('Nama Toko')
            ->assertSee('Logo Toko');
    }
}
