<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class RajaOngkirService
{
    public function calculateDomesticCost(int $originDistrictId, int $destinationDistrictId, int $weightGrams): array
    {
        $response = Http::asForm()
            ->withHeaders(['key' => config('rajaongkir.api_key')])
            ->post(config('rajaongkir.base_url').'/calculate/domestic-cost', [
                'origin' => $originDistrictId,
                'destination' => $destinationDistrictId,
                'weight' => $weightGrams,
                'courier' => implode(':', config('rajaongkir.couriers')),
                'price' => 'lowest',
            ]);

        if ($response->failed()) {
            throw new RuntimeException('RajaOngkir request failed with status '.$response->status());
        }

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('RajaOngkir response missing data array');
        }

        return array_map(fn (array $item) => [
            'courier' => $item['code'],
            'service' => $item['service'],
            'cost' => (int) $item['cost'],
            'etd' => $item['etd'],
        ], $data);
    }
}
