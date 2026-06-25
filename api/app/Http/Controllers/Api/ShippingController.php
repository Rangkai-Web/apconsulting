<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        $origin = StoreSetting::firstOrCreate([], ['store_name' => 'Doctor Plant Indonesia'])->rajaongkir_origin_district_id;

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
            Log::error('RajaOngkir cost lookup failed: '.$e->getMessage());

            return response()->json(['message' => 'Gagal cek ongkir, coba lagi'], 503);
        }

        return response()->json(['couriers' => $couriers]);
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
