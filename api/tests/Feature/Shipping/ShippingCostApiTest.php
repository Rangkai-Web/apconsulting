<?php

namespace Tests\Feature\Shipping;

use App\Models\RajaongkirCity;
use App\Models\RajaongkirDistrict;
use App\Models\RajaongkirProvince;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShippingCostApiTest extends TestCase
{
    use RefreshDatabase;

    private function seedDestinationDistrict(int $id = 999): RajaongkirDistrict
    {
        RajaongkirProvince::create(['id' => 1, 'name' => 'Jawa Barat']);
        RajaongkirCity::create(['id' => 1, 'province_id' => 1, 'name' => 'Bandung']);

        return RajaongkirDistrict::create(['id' => $id, 'city_id' => 1, 'name' => 'Coblong']);
    }

    private function fakeRajaOngkirSuccess(): void
    {
        Http::fake([
            '*/calculate/district/domestic-cost' => Http::response([
                'meta' => ['code' => 200, 'message' => 'success'],
                'data' => [
                    ['code' => 'jne', 'service' => 'REG', 'description' => 'Layanan Reguler', 'cost' => 15000, 'etd' => '2-3'],
                ],
            ], 200),
        ]);
    }

    public function test_returns_courier_list_for_valid_district_and_weight(): void
    {
        $this->fakeRajaOngkirSuccess();
        StoreSetting::create(['store_name' => 'BbystarGO', 'rajaongkir_origin_district_id' => 501]);
        $district = $this->seedDestinationDistrict();

        $response = $this->postJson('/api/shipping/cost', [
            'district_id' => $district->id,
            'total_weight' => 1000,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('couriers.0.courier', 'jne')
            ->assertJsonPath('couriers.0.cost', 15000);
    }

    public function test_caches_result_for_same_district_and_weight(): void
    {
        $this->fakeRajaOngkirSuccess();
        StoreSetting::create(['store_name' => 'BbystarGO', 'rajaongkir_origin_district_id' => 501]);
        $district = $this->seedDestinationDistrict();

        $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 1000]);
        $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 1000]);

        Http::assertSentCount(1);
    }

    public function test_rejects_unknown_district_id(): void
    {
        StoreSetting::create(['store_name' => 'BbystarGO', 'rajaongkir_origin_district_id' => 501]);

        $response = $this->postJson('/api/shipping/cost', ['district_id' => 999999, 'total_weight' => 1000]);

        $response->assertStatus(422);
    }

    public function test_rejects_when_origin_not_configured(): void
    {
        $district = $this->seedDestinationDistrict();

        $response = $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 1000]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Toko belum mengatur lokasi asal pengiriman');
    }

    public function test_returns_fallback_rates_for_java_when_rajaongkir_call_fails(): void
    {
        Http::fake(['*/calculate/district/domestic-cost' => Http::response(['message' => 'Unauthorized'], 401)]);
        StoreSetting::create(['store_name' => 'BbystarGO', 'rajaongkir_origin_district_id' => 501]);
        $district = $this->seedDestinationDistrict(); // Java Barat

        $response = $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 1000]);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'couriers')
            ->assertJsonPath('couriers.0.courier', 'jnt')
            ->assertJsonPath('couriers.0.cost', 20000)
            ->assertJsonPath('couriers.1.courier', 'pos')
            ->assertJsonPath('couriers.1.cost', 20000);
    }

    public function test_returns_fallback_rates_for_outside_java_when_rajaongkir_call_fails(): void
    {
        Http::fake(['*/calculate/district/domestic-cost' => Http::response(['message' => 'Rate limit exceeded'], 429)]);
        StoreSetting::create(['store_name' => 'BbystarGO', 'rajaongkir_origin_district_id' => 501]);

        RajaongkirProvince::create(['id' => 2, 'name' => 'Bali']);
        RajaongkirCity::create(['id' => 2, 'province_id' => 2, 'name' => 'Denpasar']);
        $district = RajaongkirDistrict::create(['id' => 888, 'city_id' => 2, 'name' => 'Denpasar Barat']);

        $response = $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 1000]);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'couriers')
            ->assertJsonPath('couriers.0.courier', 'jnt')
            ->assertJsonPath('couriers.0.cost', 35000)
            ->assertJsonPath('couriers.1.courier', 'pos')
            ->assertJsonPath('couriers.1.cost', 35000);
    }

    public function test_scales_fallback_rates_by_weight(): void
    {
        Http::fake(['*/calculate/district/domestic-cost' => Http::response(['message' => 'Error'], 500)]);
        StoreSetting::create(['store_name' => 'BbystarGO', 'rajaongkir_origin_district_id' => 501]);
        $district = $this->seedDestinationDistrict(); // Java Barat

        $response = $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 2000]);

        $response->assertStatus(200)
            ->assertJsonPath('couriers.0.cost', 40000)
            ->assertJsonPath('couriers.1.cost', 40000);
    }

    public function test_throttles_after_10_requests_per_minute(): void
    {
        $this->fakeRajaOngkirSuccess();
        StoreSetting::create(['store_name' => 'BbystarGO', 'rajaongkir_origin_district_id' => 501]);
        $district = $this->seedDestinationDistrict();

        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 1000])
                ->assertStatus(200);
        }

        $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 1000])
            ->assertStatus(429);
    }
}
