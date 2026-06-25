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
}
