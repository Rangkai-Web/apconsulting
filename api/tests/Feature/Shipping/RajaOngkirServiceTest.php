<?php

namespace Tests\Feature\Shipping;

use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class RajaOngkirServiceTest extends TestCase
{
    public function test_normalizes_successful_response_into_courier_list(): void
    {
        Http::fake([
            '*/calculate/domestic-cost' => Http::response([
                'meta' => ['code' => 200, 'message' => 'success'],
                'data' => [
                    ['code' => 'jne', 'service' => 'REG', 'description' => 'Layanan Reguler', 'cost' => 15000, 'etd' => '2-3'],
                    ['code' => 'jnt', 'service' => 'EZ', 'description' => 'Economy', 'cost' => 12000, 'etd' => '3-4'],
                ],
            ], 200),
        ]);

        $result = (new RajaOngkirService())->calculateDomesticCost(501, 999, 1000);

        $this->assertEquals([
            ['courier' => 'jne', 'service' => 'REG', 'cost' => 15000, 'etd' => '2-3'],
            ['courier' => 'jnt', 'service' => 'EZ', 'cost' => 12000, 'etd' => '3-4'],
        ], $result);
    }

    public function test_throws_when_rajaongkir_returns_non_2xx(): void
    {
        Http::fake([
            '*/calculate/domestic-cost' => Http::response(['message' => 'Unauthorized'], 401),
        ]);

        $this->expectException(RuntimeException::class);

        (new RajaOngkirService())->calculateDomesticCost(501, 999, 1000);
    }

    public function test_throws_when_response_has_no_data_array(): void
    {
        Http::fake([
            '*/calculate/domestic-cost' => Http::response(['meta' => ['code' => 200]], 200),
        ]);

        $this->expectException(RuntimeException::class);

        (new RajaOngkirService())->calculateDomesticCost(501, 999, 1000);
    }

    public function test_fetch_provinces_normalizes_id_and_name(): void
    {
        Http::fake([
            '*/destination/province' => Http::response([
                'data' => [
                    ['id' => 1, 'name' => 'Jawa Barat'],
                    ['id' => 2, 'name' => 'Jawa Tengah'],
                ],
            ], 200),
        ]);

        $result = (new RajaOngkirService())->fetchProvinces();

        $this->assertEquals([
            ['id' => 1, 'name' => 'Jawa Barat'],
            ['id' => 2, 'name' => 'Jawa Tengah'],
        ], $result);
    }

    public function test_fetch_cities_hits_province_scoped_endpoint(): void
    {
        Http::fake([
            '*/destination/city/1' => Http::response([
                'data' => [['id' => 10, 'name' => 'Bandung']],
            ], 200),
        ]);

        $result = (new RajaOngkirService())->fetchCities(1);

        $this->assertEquals([['id' => 10, 'name' => 'Bandung']], $result);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/destination/city/1'));
    }

    public function test_fetch_districts_hits_city_scoped_endpoint(): void
    {
        Http::fake([
            '*/destination/district/10' => Http::response([
                'data' => [['id' => 100, 'name' => 'Coblong']],
            ], 200),
        ]);

        $result = (new RajaOngkirService())->fetchDistricts(10);

        $this->assertEquals([['id' => 100, 'name' => 'Coblong']], $result);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/destination/district/10'));
    }

    public function test_fetch_area_list_throws_on_non_2xx(): void
    {
        Http::fake([
            '*/destination/province' => Http::response(['message' => 'Unauthorized'], 401),
        ]);

        $this->expectException(RuntimeException::class);

        (new RajaOngkirService())->fetchProvinces();
    }
}
