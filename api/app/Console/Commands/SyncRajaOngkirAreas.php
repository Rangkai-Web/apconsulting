<?php

namespace App\Console\Commands;

use App\Models\RajaongkirCity;
use App\Models\RajaongkirDistrict;
use App\Models\RajaongkirProvince;
use App\Services\RajaOngkirService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncRajaOngkirAreas extends Command
{
    protected $signature = 'rajaongkir:sync-areas
        {--fresh : Re-fetch cities/districts even for provinces/cities already synced}
        {--province= : Comma-separated RajaOngkir province IDs to sync, in priority order (default: all, ID order)}';

    protected $description = 'One-time/occasional sync of RajaOngkir province, city, and district data into local cache tables.';

    private bool $fresh = false;

    private bool $stopEarly = false;

    public function handle(RajaOngkirService $rajaOngkir): int
    {
        $this->fresh = (bool) $this->option('fresh');

        $this->info('Fetching provinces...');
        $provinces = $rajaOngkir->fetchProvinces();

        foreach ($provinces as $province) {
            RajaongkirProvince::updateOrCreate(['id' => $province['id']], ['name' => $province['name']]);
        }
        $this->info(count($provinces).' provinces synced.');

        if ($provinceIds = $this->option('province')) {
            $priority = array_map('intval', explode(',', $provinceIds));
            $byId = collect($provinces)->keyBy('id');
            $provinces = array_values(array_filter(array_map(fn ($id) => $byId->get($id), $priority)));
        }

        $provinceBar = $this->output->createProgressBar(count($provinces));
        $provinceBar->start();

        foreach ($provinces as $province) {
            $this->syncCitiesForProvince($rajaOngkir, $province);
            $provinceBar->advance();

            if ($this->stopEarly) {
                $this->newLine();
                $this->warn('Stopping early: RajaOngkir rate limit (429) hit, further calls today are unlikely to succeed.');
                break;
            }
        }

        $provinceBar->finish();
        $this->newLine();

        $this->info(sprintf(
            'Done. %d provinces, %d cities, %d districts in local cache.',
            RajaongkirProvince::count(),
            RajaongkirCity::count(),
            RajaongkirDistrict::count(),
        ));

        return self::SUCCESS;
    }

    private function syncCitiesForProvince(RajaOngkirService $rajaOngkir, array $province): void
    {
        $hasCities = RajaongkirCity::where('province_id', $province['id'])->exists();

        if ($hasCities && ! $this->fresh) {
            return;
        }

        try {
            $cities = $rajaOngkir->fetchCities($province['id']);
        } catch (Throwable $e) {
            Log::warning("rajaongkir:sync-areas: failed to fetch cities for province {$province['id']}: {$e->getMessage()}");
            $this->stopEarly = str_contains($e->getMessage(), '429');

            return;
        }

        usleep(1_000_000);

        foreach ($cities as $city) {
            RajaongkirCity::updateOrCreate(
                ['id' => $city['id']],
                ['name' => $city['name'], 'province_id' => $province['id']],
            );
        }

        foreach ($cities as $city) {
            $this->syncDistrictsForCity($rajaOngkir, $city);

            if ($this->stopEarly) {
                break;
            }
        }
    }

    private function syncDistrictsForCity(RajaOngkirService $rajaOngkir, array $city): void
    {
        $hasDistricts = RajaongkirDistrict::where('city_id', $city['id'])->exists();

        if ($hasDistricts && ! $this->fresh) {
            return;
        }

        try {
            $districts = $rajaOngkir->fetchDistricts($city['id']);
        } catch (Throwable $e) {
            Log::warning("rajaongkir:sync-areas: failed to fetch districts for city {$city['id']}: {$e->getMessage()}");
            $this->stopEarly = str_contains($e->getMessage(), '429');

            return;
        }

        foreach ($districts as $district) {
            RajaongkirDistrict::updateOrCreate(
                ['id' => $district['id']],
                ['name' => $district['name'], 'city_id' => $city['id']],
            );
        }

        usleep(1_000_000);
    }
}
