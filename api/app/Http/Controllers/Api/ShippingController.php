<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RajaongkirDistrict;
use App\Models\StoreSetting;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ShippingController extends Controller
{
    public function __construct(private RajaOngkirService $rajaOngkir)
    {
    }

    public function cost(Request $request)
    {
        $validated = $request->validate([
            'district_id' => ['required', 'integer', 'exists:rajaongkir_districts,id'],
            'total_weight' => ['required', 'integer', 'min:1'],
        ]);

        $origin = StoreSetting::firstOrCreate([], ['store_name' => 'BbystarGO'])->rajaongkir_origin_district_id;

        if (! $origin) {
            return response()->json(['message' => 'Toko belum mengatur lokasi asal pengiriman'], 422);
        }

        $cacheKey = "shipping-cost:{$origin}:{$validated['district_id']}:{$validated['total_weight']}";
        $ttl = now()->addHours((int) config('rajaongkir.cache_ttl_hours'));

        try {
            $couriers = Cache::remember($cacheKey, $ttl, fn () => $this->rajaOngkir->calculateDomesticCost(
                (int) $origin,
                (int) $validated['district_id'],
                (int) $validated['total_weight'],
            ));
        } catch (Throwable $e) {
            Log::warning('RajaOngkir cost lookup failed, using fallback pricing: '.$e->getMessage());

            $couriers = $this->getFallbackShippingCosts((int) $validated['district_id'], (int) $validated['total_weight']);
        }

        if (empty($couriers)) {
            $couriers = $this->getFallbackShippingCosts((int) $validated['district_id'], (int) $validated['total_weight']);
        }

        return response()->json(['couriers' => $couriers]);
    }

    private function getFallbackShippingCosts(int $districtId, int $weightGrams): array
    {
        $district = RajaongkirDistrict::with('city.province')->find($districtId);
        $provinceName = $district?->city?->province?->name ?? '';

        $isJava = (bool) preg_match('/(jawa|jakarta|yogyakarta|jogja|banten)/i', $provinceName);

        $baseRate = $isJava ? 20000 : 35000;
        $weightKg = (int) max(1, ceil($weightGrams / 1000));
        $cost = $baseRate * $weightKg;

        return [
            [
                'courier' => 'jnt',
                'service' => 'EZ',
                'cost' => $cost,
                'etd' => '2-3 Hari',
            ],
            [
                'courier' => 'pos',
                'service' => 'Pos Reguler',
                'cost' => $cost,
                'etd' => '2-4 Hari',
            ],
        ];
    }

    public function provinces()
    {
        return response()->json([
            'data' => \App\Models\RajaongkirProvince::orderBy('name')->get()
        ]);
    }

    public function cities(Request $request)
    {
        $validated = $request->validate([
            'province_id' => ['required', 'integer', 'exists:rajaongkir_provinces,id'],
        ]);

        return response()->json([
            'data' => \App\Models\RajaongkirCity::where('province_id', $validated['province_id'])->orderBy('name')->get()
        ]);
    }

    public function districts(Request $request)
    {
        $validated = $request->validate([
            'city_id' => ['required', 'integer', 'exists:rajaongkir_cities,id'],
        ]);

        return response()->json([
            'data' => \App\Models\RajaongkirDistrict::where('city_id', $validated['city_id'])->orderBy('name')->get()
        ]);
    }
}
