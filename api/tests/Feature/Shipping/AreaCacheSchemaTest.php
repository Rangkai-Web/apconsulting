<?php

namespace Tests\Feature\Shipping;

use App\Models\RajaongkirCity;
use App\Models\RajaongkirDistrict;
use App\Models\RajaongkirProvince;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaCacheSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_nests_district_under_city_under_province_using_rajaongkir_native_ids(): void
    {
        $province = RajaongkirProvince::create(['id' => 9, 'name' => 'Jawa Barat']);
        $city = RajaongkirCity::create(['id' => 23, 'province_id' => $province->id, 'name' => 'Bandung']);
        $district = RajaongkirDistrict::create(['id' => 501, 'city_id' => $city->id, 'name' => 'Coblong']);

        $this->assertEquals($city->id, $district->city->id);
        $this->assertEquals($province->id, $city->province->id);
    }

    public function test_stores_a_single_store_settings_row_for_non_secret_store_info(): void
    {
        $settings = StoreSetting::create([
            'store_name' => 'BbystarGO',
            'address' => 'Jl. Contoh No. 1',
            'contact' => '081200000000',
            'rajaongkir_origin_district_id' => 501,
        ]);

        $this->assertEquals('BbystarGO', StoreSetting::first()->store_name);
        $this->assertEquals(501, $settings->rajaongkir_origin_district_id);
    }

    public function test_can_fetch_provinces_cities_and_districts(): void
    {
        $province = RajaongkirProvince::create(['id' => 9, 'name' => 'Jawa Barat']);
        $city = RajaongkirCity::create(['id' => 23, 'province_id' => $province->id, 'name' => 'Bandung']);
        $district = RajaongkirDistrict::create(['id' => 501, 'city_id' => $city->id, 'name' => 'Coblong']);

        $response = $this->getJson('/api/shipping/provinces');
        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', $province->id)
            ->assertJsonPath('data.0.name', 'Jawa Barat');

        $response = $this->getJson('/api/shipping/cities?province_id=' . $province->id);
        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', $city->id)
            ->assertJsonPath('data.0.name', 'Bandung');

        $response = $this->getJson('/api/shipping/districts?city_id=' . $city->id);
        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', $district->id)
            ->assertJsonPath('data.0.name', 'Coblong');
    }
}
