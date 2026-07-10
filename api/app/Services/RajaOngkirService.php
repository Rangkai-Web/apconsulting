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
            'courier' => $item['code'] ?? '',
            'service' => $item['service'] ?? '',
            'cost' => (int) ($item['cost'] ?? 0),
            'etd' => $item['etd'] ?? '',
        ], $data);
    }

    public function fetchProvinces(): array
    {
        return $this->fetchAreaList('/destination/province');
    }

    public function fetchCities(int $provinceId): array
    {
        return $this->fetchAreaList("/destination/city/{$provinceId}");
    }

    public function fetchDistricts(int $cityId): array
    {
        return $this->fetchAreaList("/destination/district/{$cityId}");
    }

    private function fetchAreaList(string $path): array
    {
        try {
            $response = Http::withHeaders(['key' => config('rajaongkir.api_key')])
                ->retry(5, 1000, fn ($exception) => $exception instanceof \Illuminate\Http\Client\RequestException
                    && $exception->response->status() === 429)
                ->get(config('rajaongkir.base_url').$path);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            throw new RuntimeException("RajaOngkir request to {$path} failed with status ".$e->response->status());
        }

        if ($response->failed()) {
            throw new RuntimeException("RajaOngkir request to {$path} failed with status ".$response->status());
        }

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException("RajaOngkir response for {$path} missing data array");
        }

        return array_map(fn (array $item) => [
            'id' => (int) $item['id'],
            'name' => (string) $item['name'],
        ], $data);
    }
}
