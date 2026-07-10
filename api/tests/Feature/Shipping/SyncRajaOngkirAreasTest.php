<?php

namespace Tests\Feature\Shipping;

use App\Models\RajaongkirCity;
use App\Models\RajaongkirDistrict;
use App\Models\RajaongkirProvince;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncRajaOngkirAreasTest extends TestCase
{
    use RefreshDatabase;

    private function fakeAreaTree(): void
    {
        Http::fake([
            '*/destination/province' => Http::response([
                'data' => [
                    ['id' => 1, 'name' => 'Jawa Barat'],
                    ['id' => 2, 'name' => 'Jawa Tengah'],
                ],
            ], 200),
            '*/destination/city/1' => Http::response([
                'data' => [['id' => 10, 'name' => 'Bandung']],
            ], 200),
            '*/destination/city/2' => Http::response([
                'data' => [['id' => 20, 'name' => 'Semarang']],
            ], 200),
            '*/destination/district/10' => Http::response([
                'data' => [['id' => 100, 'name' => 'Coblong']],
            ], 200),
            '*/destination/district/20' => Http::response([
                'data' => [['id' => 200, 'name' => 'Tembalang']],
            ], 200),
        ]);
    }

    public function test_syncs_full_area_tree_from_api(): void
    {
        $this->fakeAreaTree();

        $this->artisan('rajaongkir:sync-areas')->assertSuccessful();

        $this->assertDatabaseHas('rajaongkir_provinces', ['id' => 1, 'name' => 'Jawa Barat']);
        $this->assertDatabaseHas('rajaongkir_cities', ['id' => 10, 'province_id' => 1, 'name' => 'Bandung']);
        $this->assertDatabaseHas('rajaongkir_districts', ['id' => 100, 'city_id' => 10, 'name' => 'Coblong']);
        $this->assertEquals(2, RajaongkirProvince::count());
        $this->assertEquals(2, RajaongkirCity::count());
        $this->assertEquals(2, RajaongkirDistrict::count());
    }

    public function test_skips_already_synced_province_unless_fresh(): void
    {
        $this->fakeAreaTree();
        RajaongkirProvince::create(['id' => 1, 'name' => 'Jawa Barat']);
        RajaongkirCity::create(['id' => 10, 'province_id' => 1, 'name' => 'Bandung']);
        RajaongkirDistrict::create(['id' => 100, 'city_id' => 10, 'name' => 'Coblong']);

        $this->artisan('rajaongkir:sync-areas')->assertSuccessful();

        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/destination/city/1'));
        Http::assertSent(fn ($request) => str_contains($request->url(), '/destination/city/2'));
    }

    public function test_fresh_flag_refetches_already_synced_areas(): void
    {
        $this->fakeAreaTree();
        RajaongkirProvince::create(['id' => 1, 'name' => 'Jawa Barat']);
        RajaongkirCity::create(['id' => 10, 'province_id' => 1, 'name' => 'Bandung']);
        RajaongkirDistrict::create(['id' => 100, 'city_id' => 10, 'name' => 'Coblong']);

        $this->artisan('rajaongkir:sync-areas', ['--fresh' => true])->assertSuccessful();

        Http::assertSent(fn ($request) => str_contains($request->url(), '/destination/city/1'));
    }

    public function test_continues_when_one_province_fetch_fails(): void
    {
        Http::fake([
            '*/destination/province' => Http::response([
                'data' => [
                    ['id' => 1, 'name' => 'Jawa Barat'],
                    ['id' => 2, 'name' => 'Jawa Tengah'],
                ],
            ], 200),
            '*/destination/city/1' => Http::response(['message' => 'Server Error'], 500),
            '*/destination/city/2' => Http::response([
                'data' => [['id' => 20, 'name' => 'Semarang']],
            ], 200),
            '*/destination/district/20' => Http::response([
                'data' => [['id' => 200, 'name' => 'Tembalang']],
            ], 200),
        ]);

        $this->artisan('rajaongkir:sync-areas')->assertSuccessful();

        $this->assertDatabaseMissing('rajaongkir_cities', ['id' => 10]);
        $this->assertDatabaseHas('rajaongkir_cities', ['id' => 20, 'province_id' => 2]);
        $this->assertDatabaseHas('rajaongkir_districts', ['id' => 200, 'city_id' => 20]);
    }
}
