<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticates_an_admin_via_the_admin_guard_separate_from_the_customer_guard(): void
    {
        $admin = Admin::factory()->create(['password' => bcrypt('secret123')]);

        $ok = Auth::guard('admin')->attempt([
            'email' => $admin->email,
            'password' => 'secret123',
        ]);

        $this->assertTrue($ok);
        $this->assertEquals($admin->id, Auth::guard('admin')->user()->id);
        $this->assertFalse(Auth::guard('web')->check());
    }
}
