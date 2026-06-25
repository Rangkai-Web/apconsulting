# Shipping Cost (RajaOngkir) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement `POST /api/shipping/cost` — a public endpoint that proxies to RajaOngkir to calculate shipping cost from the store's fixed origin to a user-supplied destination district, with caching to avoid redundant paid API calls.

**Architecture:** A `RajaOngkirService` wraps the external HTTP call and normalizes the response. `ShippingController@cost` validates input, resolves the store's origin district from `StoreSetting`, and wraps the service call in `Cache::remember()` keyed by origin+destination+weight. On any failure (validation aside), the controller returns a `503` with a fixed user-facing message — no auto-retry.

**Tech Stack:** Laravel 13, `Illuminate\Support\Facades\Http` (no new Composer package), Laravel cache (`database` driver, already configured), PHPUnit `Http::fake()`.

## Global Constraints

- Spec: `docs/superpowers/specs/2026-06-25-shipping-cost-rajaongkir-design.md`.
- `RAJAONGKIR_API_KEY`/secrets live only in `.env`, never in DB or admin UI (project CLAUDE.md, spec §5/§9).
- Area lookups (province/city/district) must never hit RajaOngkir live — only the local `rajaongkir_*` cache tables (project CLAUDE.md). This plan's validation rule (`exists:rajaongkir_districts,id`) enforces that by construction.
- `/cost`-equivalent RajaOngkir endpoint must only be called from this one explicit, user-triggered action — never from page render or unrelated requests (project CLAUDE.md).
- Couriers supported: JNE, J&T, SiCepat — stored as a config array (`config('rajaongkir.couriers')`), not hardcoded in the service.
- Cache TTL default 6 hours, configurable via `RAJAONGKIR_CACHE_TTL_HOURS`.
- Every task that adds/changes a `/api/...` endpoint must update `docs/api/<resource>.md` (project CLAUDE.md) — this plan creates `docs/api/shipping.md`.
- Run tests with: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test` (run from `api/`).

---

### Task 1: `RajaOngkirService` — HTTP client + response normalization

**Files:**
- Create: `api/config/rajaongkir.php`
- Modify: `api/.env.example`
- Create: `api/app/Services/RajaOngkirService.php`
- Test: `api/tests/Feature/Shipping/RajaOngkirServiceTest.php`

**Interfaces:**
- Produces: `App\Services\RajaOngkirService::calculateDomesticCost(int $originDistrictId, int $destinationDistrictId, int $weightGrams): array`. Returns a list of `['courier' => string, 'service' => string, 'cost' => int, 'etd' => string]`. Throws `\RuntimeException` on any non-2xx response or malformed body (no `data` array).
- Produces: config keys `rajaongkir.base_url`, `rajaongkir.api_key`, `rajaongkir.couriers` (array), `rajaongkir.cache_ttl_hours` (int) — consumed by Task 2.

- [ ] **Step 1: Write the failing test**

Create `api/tests/Feature/Shipping/RajaOngkirServiceTest.php`:

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=RajaOngkirServiceTest`
Expected: FAIL with `Class "App\Services\RajaOngkirService" not found`.

- [ ] **Step 3: Create the config file**

Create `api/config/rajaongkir.php`:

```php
<?php

return [
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
    'api_key' => env('RAJAONGKIR_API_KEY'),
    'couriers' => array_filter(explode(',', env('RAJAONGKIR_COURIERS', 'jne,jnt,sicepat'))),
    'cache_ttl_hours' => (int) env('RAJAONGKIR_CACHE_TTL_HOURS', 6),
];
```

- [ ] **Step 4: Add env placeholders**

Append to `api/.env.example` (after `STOREFRONT_URL=http://localhost:3000`):

```dotenv
RAJAONGKIR_API_KEY=
RAJAONGKIR_BASE_URL=https://rajaongkir.komerce.id/api/v1
RAJAONGKIR_COURIERS=jne,jnt,sicepat
RAJAONGKIR_CACHE_TTL_HOURS=6
```

- [ ] **Step 5: Implement `RajaOngkirService`**

Create `api/app/Services/RajaOngkirService.php`:

```php
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
```

- [ ] **Step 6: Run test to verify it passes**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=RajaOngkirServiceTest`
Expected: PASS (3 tests).

- [ ] **Step 7: Commit**

```bash
git add api/config/rajaongkir.php api/.env.example api/app/Services/RajaOngkirService.php api/tests/Feature/Shipping/RajaOngkirServiceTest.php
git commit -m "feat(api): add RajaOngkirService for domestic shipping cost lookup"
```

---

### Task 2: `ShippingController` — endpoint, validation, caching, error handling

**Files:**
- Create: `api/app/Http/Controllers/Api/ShippingController.php`
- Modify: `api/routes/api.php:7` (insert import), `api/routes/api.php:30` (append route after the settings route)
- Test: `api/tests/Feature/Shipping/ShippingCostApiTest.php`
- Create: `docs/api/shipping.md`

**Interfaces:**
- Consumes: `App\Services\RajaOngkirService::calculateDomesticCost(int, int, int): array` (Task 1). `App\Models\StoreSetting` (existing) — `rajaongkir_origin_district_id` field. `App\Models\RajaongkirDistrict` (existing) — used only via the `exists:rajaongkir_districts,id` validation rule.
- Produces: route `POST /api/shipping/cost`, public, no middleware. Response `200 { "couriers": [{ "courier": string, "service": string, "cost": int, "etd": string }] }`. Error responses: `422 { "message": string }` (validation or missing origin), `503 { "message": "Gagal cek ongkir, coba lagi" }` (RajaOngkir call failed).

- [ ] **Step 1: Write the failing test**

Create `api/tests/Feature/Shipping/ShippingCostApiTest.php`:

```php
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
            '*/calculate/domestic-cost' => Http::response([
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
        StoreSetting::create(['store_name' => 'Doctor Plant Indonesia', 'rajaongkir_origin_district_id' => 501]);
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
        StoreSetting::create(['store_name' => 'Doctor Plant Indonesia', 'rajaongkir_origin_district_id' => 501]);
        $district = $this->seedDestinationDistrict();

        $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 1000]);
        $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 1000]);

        Http::assertSentCount(1);
    }

    public function test_rejects_unknown_district_id(): void
    {
        StoreSetting::create(['store_name' => 'Doctor Plant Indonesia', 'rajaongkir_origin_district_id' => 501]);

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

    public function test_returns_503_when_rajaongkir_call_fails(): void
    {
        Http::fake(['*/calculate/domestic-cost' => Http::response(['message' => 'Unauthorized'], 401)]);
        StoreSetting::create(['store_name' => 'Doctor Plant Indonesia', 'rajaongkir_origin_district_id' => 501]);
        $district = $this->seedDestinationDistrict();

        $response = $this->postJson('/api/shipping/cost', ['district_id' => $district->id, 'total_weight' => 1000]);

        $response->assertStatus(503)
            ->assertJsonPath('message', 'Gagal cek ongkir, coba lagi');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=ShippingCostApiTest`
Expected: FAIL with 404 (route `/api/shipping/cost` not found) on every test.

- [ ] **Step 3: Implement `ShippingController`**

Create `api/app/Http/Controllers/Api/ShippingController.php`:

```php
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

        $origin = StoreSetting::first()?->rajaongkir_origin_district_id;

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
}
```

- [ ] **Step 4: Register the route**

Modify `api/routes/api.php` — add the import and route in the public section (after the settings route):

```php
use App\Http\Controllers\Api\ShippingController;
```

```php
// Public shipping route
Route::post('/shipping/cost', [ShippingController::class, 'cost']);
```

- [ ] **Step 5: Run test to verify it passes**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=ShippingCostApiTest`
Expected: PASS (5 tests).

- [ ] **Step 6: Write API documentation**

Create `docs/api/shipping.md`:

````markdown
# Shipping API

## POST /api/shipping/cost

Auth: none.

Calculates shipping cost from the store's fixed origin to a destination district via RajaOngkir. Results are cached server-side per (origin, destination, weight) combination — repeat calls with the same inputs do not re-hit RajaOngkir within the cache window.

**Request body**

```json
{
  "district_id": 999,
  "total_weight": 1000
}
```

| Field | Type | Notes |
|---|---|---|
| `district_id` | int | RajaOngkir destination district ID, must exist in the local `rajaongkir_districts` cache table |
| `total_weight` | int | Total cart weight in grams, min 1 |

**Response 200**

```json
{
  "couriers": [
    { "courier": "jne", "service": "REG", "cost": 15000, "etd": "2-3" }
  ]
}
```

**Response 422** — invalid `district_id`/`total_weight`, or store has no `rajaongkir_origin_district_id` configured:

```json
{ "message": "Toko belum mengatur lokasi asal pengiriman" }
```

**Response 503** — RajaOngkir call failed (timeout, non-2xx, malformed response):

```json
{ "message": "Gagal cek ongkir, coba lagi" }
```
````

- [ ] **Step 7: Run the full test suite**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test`
Expected: PASS, no regressions in existing suites.

- [ ] **Step 8: Commit**

```bash
git add api/app/Http/Controllers/Api/ShippingController.php api/routes/api.php api/tests/Feature/Shipping/ShippingCostApiTest.php docs/api/shipping.md
git commit -m "feat(api): add POST /api/shipping/cost endpoint with RajaOngkir cost caching"
```

---

## Verification Plan

### Automated Tests
Run inside `api/`:
```bash
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test
```
Covers: `RajaOngkirServiceTest` (response normalization, error handling) and `ShippingCostApiTest` (endpoint success, caching, validation, missing origin, upstream failure).

### Manual Verification
Not possible until a real RajaOngkir API key exists (none registered as of this plan). Once a key is obtained:
1. Set `RAJAONGKIR_API_KEY` in `api/.env`.
2. `curl -X POST http://localhost:8000/api/shipping/cost -H "Content-Type: application/json" -d '{"district_id": <real_district_id>, "total_weight": 1000}'` and confirm the response shape matches `RajaOngkirService::calculateDomesticCost()`'s parsing assumptions (field names `code`/`service`/`cost`/`etd` in `data[]`). If the live shape differs, only `RajaOngkirService` needs updating — the `/api/shipping/cost` contract stays the same.
