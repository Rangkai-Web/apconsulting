<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_returns_ok_status_from_the_health_check_endpoint(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function test_throttles_api_routes_after_300_requests_per_minute(): void
    {
        for ($i = 0; $i < 300; $i++) {
            $this->getJson('/api/health')->assertStatus(200);
        }

        $this->getJson('/api/health')->assertStatus(429);
    }
}
