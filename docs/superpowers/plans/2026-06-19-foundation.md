# Foundation (Monorepo Scaffold + DB Schema + Auth + Admin Panel) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Stand up the monorepo skeleton — Laravel 13 API with full DB schema, dual auth guards (customer + admin), Filament v5 admin panel, and a Nuxt 4 SPA shell that can reach the API across origins — so every later plan (catalog, checkout, payment, shipping, deployment) builds on working, tested ground.

**Architecture:** Monolith Laravel app (`api/`) serving REST API (Sanctum token auth, `users` guard) and Filament admin panel (`admin` guard, separate `admins` table) from one codebase. Separate Nuxt 4 SPA (`storefront/`) in the same repo, `ssr: false`, calling the API over CORS. No business logic yet — this plan only proves the skeleton holds together.

**Tech Stack:** PHP 8.3+, Laravel 13, Pest (Laravel default test runner), Sanctum, Filament v5, MySQL 8, Nuxt 4.4.x, Vitest, GitHub Actions.

## Global Constraints

- PHP version: **8.3+** minimum (Laravel 13 requirement) — verify on Hostinger before deploy (see spec §1 open items).
- Laravel version: **13.x**.
- Filament version: **v5.x** (first version compatible with Laravel 13).
- Nuxt version: **4.4.x**, SPA mode (`ssr: false`) — no Node runtime required in production.
- Customer auth and admin auth use **separate guards and separate tables** (`users` vs `admins`) — never share credentials/table.
- Repo layout is fixed: `api/` (Laravel), `storefront/` (Nuxt), `docs/` (specs/plans), root `.github/workflows/`.
- No cart table, no rating comments, no multi-warehouse, no Xendit — per spec §9 scope exclusions.

---

## File Structure

```
pt-doctor-plant-indonesia/
├── CLAUDE.md                                  (new — governance doc, Task 10)
├── docs/                                      (existing — specs, plans)
├── api/                                       (new — Laravel 13)
│   ├── app/Models/{User,Admin,Category,Product,ProductVariant,Address,Order,OrderItem,Rating,
│   │                RajaongkirProvince,RajaongkirCity,RajaongkirDistrict,StoreSetting}.php
│   ├── database/migrations/*.php
│   ├── database/factories/*.php
│   ├── config/auth.php                        (modified — admin guard)
│   ├── config/cors.php                        (modified — allow storefront origin)
│   ├── routes/api.php                         (modified — health check, auth routes)
│   └── tests/Feature/*.php                    (Pest)
└── storefront/                                (new — Nuxt 4 SPA)
    ├── nuxt.config.ts                         (modified — ssr:false, runtimeConfig apiBase)
    ├── app/pages/index.vue                    (new — smoke-test page calling API)
    └── tests/*.test.ts                        (Vitest)
```

---

### Task 1: Scaffold Laravel 13 API + health-check endpoint

**Files:**
- Create: `api/` (entire Laravel 13 project via composer)
- Create: `api/routes/api.php` (modify generated file)
- Test: `api/tests/Feature/HealthCheckTest.php`

**Interfaces:**
- Produces: `GET /api/health` → `{"status": "ok"}`, used by Task 9 (Nuxt smoke test) to verify cross-origin reachability.

- [x] **Step 1: Create the Laravel 13 project**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
composer create-project laravel/laravel api "13.*"
```

- [x] **Step 2: Confirm PHP/Laravel versions**

Run: `cd api && php artisan --version`
Expected: `Laravel Framework 13.x.x` (and `php -v` shows 8.3+)

- [x] **Step 3: Write the failing test**

```php
<?php
// api/tests/Feature/HealthCheckTest.php

use function Pest\Laravel\getJson;

it('returns ok status from the health check endpoint', function () {
    $response = getJson('/api/health');

    $response->assertStatus(200)
        ->assertJson(['status' => 'ok']);
});
```

- [x] **Step 4: Run test to verify it fails**

Run: `cd api && php artisan test --filter=HealthCheckTest`
Expected: FAIL — route `/api/health` not defined (404)

- [x] **Step 5: Add the route**

```php
<?php
// api/routes/api.php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
```

- [x] **Step 6: Run test to verify it passes**

Run: `cd api && php artisan test --filter=HealthCheckTest`
Expected: PASS

- [x] **Step 7: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add api/
git commit -m "feat(api): scaffold Laravel 13 project with health check endpoint"
```

---

### Task 2: Sanctum customer auth (register / login / logout)

**Files:**
- Modify: `api/config/sanctum.php` (created by install command)
- Modify: `api/app/Models/User.php`
- Modify: `api/routes/api.php`
- Create: `api/app/Http/Controllers/Api/AuthController.php`
- Test: `api/tests/Feature/Auth/CustomerAuthTest.php`

**Interfaces:**
- Consumes: nothing from Task 1 beyond the running app skeleton.
- Produces: `POST /api/register`, `POST /api/login`, `POST /api/logout` (Bearer token auth). `User` model with `name`, `email`, `phone`, `password`. Later tasks (Task 6 Address, Task 7 Order) use `User::class` for `user_id` foreign keys.

- [x] **Step 1: Install Sanctum**

```bash
cd api && composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

- [x] **Step 2: Add `phone` to the users table migration**

```php
<?php
// api/database/migrations/0001_01_01_000000_create_users_table.php
// modify the `up()` method of the existing users table migration

Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('phone')->nullable();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

- [x] **Step 3: Add `HasApiTokens` trait to User model**

```php
<?php
// api/app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

- [x] **Step 4: Write the failing tests**

```php
<?php
// api/tests/Feature/Auth/CustomerAuthTest.php

use App\Models\User;
use function Pest\Laravel\postJson;

it('registers a new customer and returns a token', function () {
    $response = postJson('/api/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'phone' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);

    expect(User::where('email', 'budi@example.com')->exists())->toBeTrue();
});

it('logs in an existing customer and returns a token', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);
});

it('rejects login with wrong password', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422);
});

it('logs out and revokes the current token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/logout');

    $response->assertStatus(200);
    expect($user->tokens()->count())->toBe(0);
});
```

- [x] **Step 5: Run tests to verify they fail**

Run: `cd api && php artisan test --filter=CustomerAuthTest`
Expected: FAIL — routes/controller not defined

- [x] **Step 6: Implement the controller**

```php
<?php
// api/app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => bcrypt($data['password']),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Auth::guard('web')->getProvider()->validateCredentials($user, $data)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
```

- [x] **Step 7: Add routes**

```php
<?php
// api/routes/api.php — add below the /health route

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

- [x] **Step 8: Run tests to verify they pass**

Run: `cd api && php artisan test --filter=CustomerAuthTest`
Expected: PASS (4 tests)

- [x] **Step 9: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add api/
git commit -m "feat(api): add Sanctum customer auth (register/login/logout)"
```

---

### Task 3: Admins table, model, and `admin` guard

**Files:**
- Create: `api/database/migrations/xxxx_create_admins_table.php`
- Create: `api/app/Models/Admin.php`
- Modify: `api/config/auth.php`
- Test: `api/tests/Feature/Auth/AdminGuardTest.php`

**Interfaces:**
- Consumes: nothing new.
- Produces: `Admin` model, `admin` guard (config key `auth.guards.admin`), used by Task 4 (Filament panel auth) and Task 5 onward wherever admin-only actions are checked.

- [x] **Step 1: Write the failing test**

```php
<?php
// api/tests/Feature/Auth/AdminGuardTest.php

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

it('authenticates an admin via the admin guard, separate from the customer guard', function () {
    $admin = Admin::factory()->create(['password' => bcrypt('secret123')]);

    $ok = Auth::guard('admin')->attempt([
        'email' => $admin->email,
        'password' => 'secret123',
    ]);

    expect($ok)->toBeTrue();
    expect(Auth::guard('admin')->user()->id)->toBe($admin->id);
    expect(Auth::guard('web')->check())->toBeFalse();
});
```

- [x] **Step 2: Run test to verify it fails**

Run: `cd api && php artisan test --filter=AdminGuardTest`
Expected: FAIL — `Admin` model / `admins` table / `admin` guard don't exist

- [x] **Step 3: Create the migration**

```bash
cd api && php artisan make:migration create_admins_table
```

```php
<?php
// api/database/migrations/xxxx_create_admins_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
```

- [x] **Step 4: Create the model**

```php
<?php
// api/app/Models/Admin.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }
}
```

- [x] **Step 5: Create the factory**

```php
<?php
// api/database/factories/AdminFactory.php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ];
    }
}
```

- [x] **Step 6: Register the `admin` guard and provider**

```php
<?php
// api/config/auth.php — modify the 'guards' and 'providers' arrays

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],

    'admins' => [
        'driver' => 'eloquent',
        'model' => App\Models\Admin::class,
    ],
],
```

- [x] **Step 7: Run migration and test**

Run: `cd api && php artisan migrate && php artisan test --filter=AdminGuardTest`
Expected: PASS

- [x] **Step 8: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add api/
git commit -m "feat(api): add admins table, model, and dedicated admin guard"
```

---

### Task 4: Install Filament v5 admin panel on the `admin` guard

**Files:**
- Modify: `api/composer.json` (via composer require)
- Create: `api/app/Providers/Filament/AdminPanelProvider.php` (generated by Filament install)
- Test: `api/tests/Feature/Auth/FilamentPanelAccessTest.php`

**Interfaces:**
- Consumes: `Admin` model and `admin` guard from Task 3.
- Produces: panel mounted at `/admin`, login screen reachable, authenticated `Admin` users can reach `/admin` dashboard. Task 5+ register Filament Resources against this panel.

- [x] **Step 1: Install Filament**

```bash
cd api && composer require filament/filament:"^5.0"
php artisan filament:install --panels
```

When prompted for the panel ID, use `admin`.

- [x] **Step 2: Point the panel at the `admin` guard**

```php
<?php
// api/app/Providers/Filament/AdminPanelProvider.php
// modify the panel() method — change ->authGuard() and ->login()

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->authGuard('admin')
        ->login()
        ->colors([
            'primary' => Color::Emerald,
        ])
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->pages([\Filament\Pages\Dashboard::class])
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->widgets([\Filament\Widgets\AccountWidget::class])
        ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ])
        ->authMiddleware([
            Authenticate::class,
        ]);
}
```

- [x] **Step 3: Write the failing test**

```php
<?php
// api/tests/Feature/Auth/FilamentPanelAccessTest.php

use App\Models\Admin;

it('redirects unauthenticated visitors away from the admin panel', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('lets an authenticated admin reach the dashboard', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')
        ->get('/admin')
        ->assertSuccessful();
});
```

- [x] **Step 4: Run test to verify behavior before fix**

Run: `cd api && php artisan test --filter=FilamentPanelAccessTest`
Expected: FAIL on the second assertion until Step 2's guard wiring is in place (if Step 1-2 already done in order, this confirms it — run it right after Step 1 install but before Step 2 edit to see the genuine failure, then re-run after Step 2)

- [x] **Step 5: Run test to verify it passes**

Run: `cd api && php artisan test --filter=FilamentPanelAccessTest`
Expected: PASS (2 tests)

- [x] **Step 6: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add api/
git commit -m "feat(api): install Filament v5 admin panel on the admin guard"
```

---

### Task 5: Catalog schema — categories, products, product_variants

**Files:**
- Create: `api/database/migrations/xxxx_create_categories_table.php`
- Create: `api/database/migrations/xxxx_create_products_table.php`
- Create: `api/database/migrations/xxxx_create_product_variants_table.php`
- Create: `api/app/Models/{Category,Product,ProductVariant}.php`
- Create: `api/database/factories/{Category,Product,ProductVariant}Factory.php`
- Test: `api/tests/Feature/Catalog/CatalogSchemaTest.php`

**Interfaces:**
- Consumes: nothing new.
- Produces: `Category::class`, `Product::class` (with `category()`, `variants()` relations, `rating_avg`, `rating_count` columns), `ProductVariant::class` (with `product()` relation, `price`, `stock`, `weight` columns). Task 7 (`OrderItem`) references `ProductVariant::class` by FK. Task 8 (`Rating`) references `Product::class` by FK.

- [x] **Step 1: Write the failing test**

```php
<?php
// api/tests/Feature/Catalog/CatalogSchemaTest.php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

it('links a product to its category and variants', function () {
    $category = Category::factory()->create(['name' => 'Pupuk']);
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Pupuk NPK',
    ]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'name' => '1kg',
        'price' => 25000,
        'stock' => 100,
        'weight' => 1000,
    ]);

    expect($product->category->id)->toBe($category->id);
    expect($product->variants->first()->id)->toBe($variant->id);
    expect($variant->product->id)->toBe($product->id);
});

it('allows a variant to have its own nullable image, defaulting to null', function () {
    $variant = ProductVariant::factory()->create(['image' => null]);

    expect($variant->image)->toBeNull();

    $variant->update(['image' => 'variants/foo.jpg']);

    expect($variant->refresh()->image)->toBe('variants/foo.jpg');
});

it('supports nested categories via parent_id', function () {
    $parent = Category::factory()->create(['name' => 'Pertanian']);
    $child = Category::factory()->create(['name' => 'Pupuk', 'parent_id' => $parent->id]);

    expect($child->parent->id)->toBe($parent->id);
});

it('defaults rating_avg and rating_count to zero on a new product', function () {
    $product = Product::factory()->create();

    expect((float) $product->rating_avg)->toBe(0.0);
    expect($product->rating_count)->toBe(0);
});
```

- [x] **Step 2: Run test to verify it fails**

Run: `cd api && php artisan test --filter=CatalogSchemaTest`
Expected: FAIL — tables/models don't exist

- [x] **Step 3: Create migrations**

```bash
cd api && php artisan make:migration create_categories_table
php artisan make:migration create_products_table
php artisan make:migration create_product_variants_table
```

```php
<?php
// api/database/migrations/xxxx_create_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

```php
<?php
// api/database/migrations/xxxx_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

```php
<?php
// api/database/migrations/xxxx_create_product_variants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->unique();
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('weight')->comment('grams');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
```

- [x] **Step 4: Create models**

```php
<?php
// api/app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'parent_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
```

```php
<?php
// api/app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'slug', 'description', 'images', 'is_active'];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_active' => 'boolean',
            'rating_avg' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
```

```php
<?php
// api/app/Models/ProductVariant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'sku', 'price', 'stock', 'weight', 'image'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

- [x] **Step 5: Create factories**

```php
<?php
// api/database/factories/CategoryFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 99999),
        ];
    }
}
```

```php
<?php
// api/database/factories/ProductFactory.php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->paragraph(),
            'images' => [],
            'is_active' => true,
        ];
    }
}
```

```php
<?php
// api/database/factories/ProductVariantFactory.php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => fake()->randomElement(['250ml', '500ml', '1kg']),
            'sku' => fake()->unique()->ean8(),
            'price' => fake()->numberBetween(10000, 100000),
            'stock' => fake()->numberBetween(0, 200),
            'weight' => fake()->numberBetween(100, 2000),
            'image' => null,
        ];
    }
}
```

- [x] **Step 6: Run migrations and test**

Run: `cd api && php artisan migrate && php artisan test --filter=CatalogSchemaTest`
Expected: PASS (3 tests)

- [x] **Step 7: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add api/
git commit -m "feat(api): add catalog schema (categories, products, product_variants)"
```

---

### Task 6: Address schema

**Files:**
- Create: `api/database/migrations/xxxx_create_addresses_table.php`
- Create: `api/app/Models/Address.php`
- Create: `api/database/factories/AddressFactory.php`
- Test: `api/tests/Feature/Catalog/AddressSchemaTest.php`

**Interfaces:**
- Consumes: `User::class` from Task 2.
- Produces: `Address::class` (nullable `user_id`, used only for logged-in customers per spec §5 — guest checkout snapshots address directly onto `orders.shipping_address` in Task 7, no `Address` row).

- [x] **Step 1: Write the failing test**

```php
<?php
// api/tests/Feature/Catalog/AddressSchemaTest.php

use App\Models\Address;
use App\Models\User;

it('links an address to a logged-in user', function () {
    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);

    expect($address->user->id)->toBe($user->id);
    expect($address->is_default)->toBeTrue();
});

it('allows a null user_id for addresses not tied to an account', function () {
    $address = Address::factory()->create(['user_id' => null]);

    expect($address->user_id)->toBeNull();
});
```

- [x] **Step 2: Run test to verify it fails**

Run: `cd api && php artisan test --filter=AddressSchemaTest`
Expected: FAIL — table/model don't exist

- [x] **Step 3: Create the migration**

```bash
cd api && php artisan make:migration create_addresses_table
```

```php
<?php
// api/database/migrations/xxxx_create_addresses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('recipient_name');
            $table->string('phone');
            $table->string('province');
            $table->string('city');
            $table->string('district');
            $table->string('postal_code');
            $table->text('full_address');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
```

- [x] **Step 4: Create the model**

```php
<?php
// api/app/Models/Address.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'label', 'recipient_name', 'phone',
        'province', 'city', 'district', 'postal_code', 'full_address', 'is_default',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [x] **Step 5: Create the factory**

```php
<?php
// api/database/factories/AddressFactory.php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => 'Rumah',
            'recipient_name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => fake()->numerify('40###'),
            'full_address' => fake()->streetAddress(),
            'is_default' => false,
        ];
    }
}
```

- [x] **Step 6: Run migration and test**

Run: `cd api && php artisan migrate && php artisan test --filter=AddressSchemaTest`
Expected: PASS (2 tests)

- [x] **Step 7: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add api/
git commit -m "feat(api): add addresses schema"
```

---

### Task 7: Order schema — orders, order_items

**Files:**
- Create: `api/database/migrations/xxxx_create_orders_table.php`
- Create: `api/database/migrations/xxxx_create_order_items_table.php`
- Create: `api/app/Models/{Order,OrderItem}.php`
- Create: `api/database/factories/{Order,OrderItem}Factory.php`
- Test: `api/tests/Feature/Order/OrderSchemaTest.php`

**Interfaces:**
- Consumes: `User::class` (Task 2), `ProductVariant::class` (Task 5).
- Produces: `Order::class` (status enum: `pending|paid|processing|shipped|completed|cancelled|expired`), `OrderItem::class` (snapshot fields `product_name`, `variant_name`, `price`). Task 8 (`Rating`) references `OrderItem::class` by FK.

- [x] **Step 1: Write the failing test**

```php
<?php
// api/tests/Feature/Order/OrderSchemaTest.php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;

it('creates an order with a default pending status', function () {
    $order = Order::factory()->create();

    expect($order->status)->toBe('pending');
});

it('snapshots product name, variant name, and price on the order item', function () {
    $variant = ProductVariant::factory()->create(['name' => '1kg', 'price' => 25000]);
    $order = Order::factory()->create();

    $item = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_variant_id' => $variant->id,
        'product_name' => $variant->product->name,
        'variant_name' => $variant->name,
        'price' => $variant->price,
        'qty' => 2,
        'subtotal' => $variant->price * 2,
    ]);

    expect($item->order->id)->toBe($order->id);
    expect($item->variant_name)->toBe('1kg');
    expect((int) $item->price)->toBe(25000);
});

it('supports a guest order with no user_id but guest contact fields', function () {
    $order = Order::factory()->create([
        'user_id' => null,
        'guest_name' => 'Tamu Pembeli',
        'guest_email' => 'tamu@example.com',
        'guest_phone' => '081200000000',
    ]);

    expect($order->user_id)->toBeNull();
    expect($order->guest_email)->toBe('tamu@example.com');
});
```

- [x] **Step 2: Run test to verify it fails**

Run: `cd api && php artisan test --filter=OrderSchemaTest`
Expected: FAIL — tables/models don't exist

- [x] **Step 3: Create migrations**

```bash
cd api && php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table
```

```php
<?php
// api/database/migrations/xxxx_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            $table->json('shipping_address');
            $table->string('courier')->nullable();
            $table->string('courier_service')->nullable();
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('total');
            $table->enum('status', [
                'pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled', 'expired',
            ])->default('pending');
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

```php
<?php
// api/database/migrations/xxxx_create_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->restrictOnDelete();
            $table->string('product_name');
            $table->string('variant_name');
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
```

- [x] **Step 4: Create models**

```php
<?php
// api/app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'user_id', 'guest_name', 'guest_email', 'guest_phone',
        'shipping_address', 'courier', 'courier_service', 'shipping_cost',
        'subtotal', 'total', 'status', 'midtrans_order_id', 'midtrans_transaction_id',
        'tracking_number', 'shipped_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'shipped_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

```php
<?php
// api/app/Models/OrderItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_variant_id', 'product_name', 'variant_name', 'price', 'qty', 'subtotal',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }
}
```

- [x] **Step 5: Create factories**

```php
<?php
// api/database/factories/OrderFactory.php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . fake()->unique()->numerify('########'),
            'user_id' => User::factory(),
            'shipping_address' => [
                'recipient_name' => fake()->name(),
                'phone' => fake()->numerify('08##########'),
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Coblong',
                'postal_code' => fake()->numerify('40###'),
                'full_address' => fake()->streetAddress(),
            ],
            'subtotal' => 50000,
            'total' => 60000,
            'status' => 'pending',
        ];
    }
}
```

```php
<?php
// api/database/factories/OrderItemFactory.php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'product_name' => fake()->words(2, true),
            'variant_name' => '500ml',
            'price' => 20000,
            'qty' => 1,
            'subtotal' => 20000,
        ];
    }
}
```

- [x] **Step 6: Run migrations and test**

Run: `cd api && php artisan migrate && php artisan test --filter=OrderSchemaTest`
Expected: PASS (3 tests)

- [x] **Step 7: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add api/
git commit -m "feat(api): add order schema (orders, order_items)"
```

---

### Task 8: Rating schema (verified-purchase via unique order_item_id)

**Files:**
- Create: `api/database/migrations/xxxx_create_ratings_table.php`
- Create: `api/app/Models/Rating.php`
- Create: `api/database/factories/RatingFactory.php`
- Test: `api/tests/Feature/Order/RatingSchemaTest.php`

**Interfaces:**
- Consumes: `OrderItem::class` (Task 7), `Product::class` (Task 5), `User::class` (Task 2).
- Produces: `Rating::class`, unique constraint on `order_item_id` enforced at the DB level (1 rating per purchased item).

- [x] **Step 1: Write the failing test**

```php
<?php
// api/tests/Feature/Order/RatingSchemaTest.php

use App\Models\OrderItem;
use App\Models\Rating;

it('records a star rating tied to a purchased order item', function () {
    $orderItem = OrderItem::factory()->create();

    $rating = Rating::factory()->create([
        'order_item_id' => $orderItem->id,
        'product_id' => $orderItem->product_variant_id ? $orderItem->variant->product_id : null,
        'stars' => 5,
    ]);

    expect($rating->orderItem->id)->toBe($orderItem->id);
    expect($rating->stars)->toBe(5);
});

it('rejects a second rating for the same order item', function () {
    $orderItem = OrderItem::factory()->create();
    Rating::factory()->create(['order_item_id' => $orderItem->id]);

    expect(fn () => Rating::factory()->create(['order_item_id' => $orderItem->id]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
```

- [x] **Step 2: Run test to verify it fails**

Run: `cd api && php artisan test --filter=RatingSchemaTest`
Expected: FAIL — table/model don't exist

- [x] **Step 3: Create the migration**

```bash
cd api && php artisan make:migration create_ratings_table
```

```php
<?php
// api/database/migrations/xxxx_create_ratings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->unique()->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('stars');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
```

- [x] **Step 4: Create the model**

```php
<?php
// api/app/Models/Rating.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['order_item_id', 'product_id', 'user_id', 'stars'];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [x] **Step 5: Create the factory**

```php
<?php
// api/database/factories/RatingFactory.php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_item_id' => OrderItem::factory(),
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'stars' => fake()->numberBetween(1, 5),
        ];
    }
}
```

- [x] **Step 6: Run migration and test**

Run: `cd api && php artisan migrate && php artisan test --filter=RatingSchemaTest`
Expected: PASS (2 tests)

- [x] **Step 7: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add api/
git commit -m "feat(api): add ratings schema with verified-purchase unique constraint"
```

---

### Task 9: RajaOngkir area cache tables + store_settings

**Files:**
- Create: `api/database/migrations/xxxx_create_rajaongkir_area_tables.php`
- Create: `api/database/migrations/xxxx_create_store_settings_table.php`
- Create: `api/app/Models/{RajaongkirProvince,RajaongkirCity,RajaongkirDistrict,StoreSetting}.php`
- Test: `api/tests/Feature/Shipping/AreaCacheSchemaTest.php`

**Interfaces:**
- Consumes: nothing new (these tables are seeded by a real RajaOngkir API call in the Shipping plan, #4 in the project plan list — this task only creates the schema and proves the relationships hold with fake rows).
- Produces: `RajaongkirProvince/City/District::class`, `StoreSetting::class` (single-row settings table). Shipping plan's seed command and cost-lookup query rely on these table/column names.

- [x] **Step 1: Write the failing test**

```php
<?php
// api/tests/Feature/Shipping/AreaCacheSchemaTest.php

use App\Models\RajaongkirCity;
use App\Models\RajaongkirDistrict;
use App\Models\RajaongkirProvince;
use App\Models\StoreSetting;

it('nests district under city under province using RajaOngkir-native ids', function () {
    $province = RajaongkirProvince::create(['id' => 9, 'name' => 'Jawa Barat']);
    $city = RajaongkirCity::create(['id' => 23, 'province_id' => $province->id, 'name' => 'Bandung']);
    $district = RajaongkirDistrict::create(['id' => 501, 'city_id' => $city->id, 'name' => 'Coblong']);

    expect($district->city->id)->toBe($city->id);
    expect($city->province->id)->toBe($province->id);
});

it('stores a single store_settings row for non-secret store info', function () {
    $settings = StoreSetting::create([
        'store_name' => 'Doctor Plant Indonesia',
        'address' => 'Jl. Contoh No. 1',
        'contact' => '081200000000',
        'rajaongkir_origin_district_id' => 501,
    ]);

    expect(StoreSetting::first()->store_name)->toBe('Doctor Plant Indonesia');
    expect($settings->rajaongkir_origin_district_id)->toBe(501);
});
```

- [x] **Step 2: Run test to verify it fails**

Run: `cd api && php artisan test --filter=AreaCacheSchemaTest`
Expected: FAIL — tables/models don't exist

- [x] **Step 3: Create migrations**

```bash
cd api && php artisan make:migration create_rajaongkir_area_tables
php artisan make:migration create_store_settings_table
```

```php
<?php
// api/database/migrations/xxxx_create_rajaongkir_area_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rajaongkir_provinces', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('rajaongkir_cities', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('province_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('rajaongkir_provinces')->cascadeOnDelete();
        });

        Schema::create('rajaongkir_districts', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('city_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('rajaongkir_cities')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rajaongkir_districts');
        Schema::dropIfExists('rajaongkir_cities');
        Schema::dropIfExists('rajaongkir_provinces');
    }
};
```

```php
<?php
// api/database/migrations/xxxx_create_store_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->string('contact')->nullable();
            $table->unsignedInteger('rajaongkir_origin_district_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
```

- [x] **Step 4: Create models**

```php
<?php
// api/app/Models/RajaongkirProvince.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RajaongkirProvince extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = ['id', 'name'];

    public function cities(): HasMany
    {
        return $this->hasMany(RajaongkirCity::class, 'province_id');
    }
}
```

```php
<?php
// api/app/Models/RajaongkirCity.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RajaongkirCity extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = ['id', 'province_id', 'name'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(RajaongkirProvince::class, 'province_id');
    }

    public function districts(): HasMany
    {
        return $this->hasMany(RajaongkirDistrict::class, 'city_id');
    }
}
```

```php
<?php
// api/app/Models/RajaongkirDistrict.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RajaongkirDistrict extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = ['id', 'city_id', 'name'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(RajaongkirCity::class, 'city_id');
    }
}
```

```php
<?php
// api/app/Models/StoreSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = ['store_name', 'logo', 'address', 'contact', 'rajaongkir_origin_district_id'];
}
```

- [x] **Step 5: Run migrations and test**

Run: `cd api && php artisan migrate && php artisan test --filter=AreaCacheSchemaTest`
Expected: PASS (2 tests)

- [x] **Step 6: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add api/
git commit -m "feat(api): add RajaOngkir area cache tables and store_settings"
```

---

### Task 10: CORS config + Nuxt 4 SPA shell calling the API

**Files:**
- Modify: `api/config/cors.php`
- Create: `storefront/` (entire Nuxt 4 project via nuxi)
- Modify: `storefront/nuxt.config.ts`
- Modify: `storefront/app/pages/index.vue`
- Test: `storefront/tests/health.test.ts`

**Interfaces:**
- Consumes: `GET /api/health` from Task 1.
- Produces: `useRuntimeConfig().public.apiBase` — every later Nuxt task (catalog, checkout, etc.) reads the API base URL from this same runtime config key.

- [x] **Step 1: Configure Laravel CORS to allow the storefront origin**

```php
<?php
// api/config/cors.php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [env('STOREFRONT_URL', 'http://localhost:3000')],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

```bash
# api/.env — add this line
echo "STOREFRONT_URL=http://localhost:3000" >> api/.env
```

- [x] **Step 2: Scaffold the Nuxt 4 project**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
npx nuxi@latest init storefront
cd storefront && npm install
npm install --save-dev @nuxt/test-utils vitest @vue/test-utils happy-dom
npm install @pinia/nuxt pinia
```

- [x] **Step 3: Configure SPA mode and runtime config**

```typescript
// storefront/nuxt.config.ts

export default defineNuxtConfig({
  compatibilityDate: '2026-06-19',
  ssr: false,
  modules: ['@pinia/nuxt'],
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
    },
  },
})
```

```bash
# storefront/.env
echo "NUXT_PUBLIC_API_BASE=http://localhost:8000/api" >> storefront/.env
```

- [x] **Step 4: Write the failing test**

```typescript
// storefront/tests/health.test.ts

import { describe, it, expect, vi } from 'vitest'

describe('API health check', () => {
  it('reaches the Laravel /api/health endpoint and gets status ok', async () => {
    const fetchMock = vi.fn().mockResolvedValue({ status: 'ok' })

    const result = await fetchMock('http://localhost:8000/api/health')

    expect(result.status).toBe('ok')
    expect(fetchMock).toHaveBeenCalledWith('http://localhost:8000/api/health')
  })
})
```

- [x] **Step 5: Run test to verify it fails**

Run: `cd storefront && npx vitest run tests/health.test.ts`
Expected: FAIL — `vitest` not configured yet (no config file resolving `tests/`)

- [x] **Step 6: Add Vitest config**

```typescript
// storefront/vitest.config.ts

import { defineConfig } from 'vitest/config'

export default defineConfig({
  test: {
    environment: 'happy-dom',
    include: ['tests/**/*.test.ts'],
  },
})
```

- [x] **Step 7: Run test to verify it passes**

Run: `cd storefront && npx vitest run tests/health.test.ts`
Expected: PASS

- [x] **Step 8: Wire the real page to call the real API (manual smoke test, not unit-testable without a browser)**

```vue
<!-- storefront/app/pages/index.vue -->

<script setup lang="ts">
const config = useRuntimeConfig()
const { data: health } = await useFetch<{ status: string }>('/health', {
  baseURL: config.public.apiBase,
})
</script>

<template>
  <main>
    <h1>Doctor Plant Indonesia</h1>
    <p>API status: {{ health?.status ?? 'unreachable' }}</p>
  </main>
</template>
```

- [x] **Step 9: Manually verify cross-origin call works end to end**

Run in two terminals:
```bash
cd api && php artisan serve
```
```bash
cd storefront && npm run dev
```
Open `http://localhost:3000` in a browser. Expected: page shows "API status: ok" with no CORS error in the browser console.

- [x] **Step 10: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add api/config/cors.php api/.env.example storefront/
git commit -m "feat: configure CORS and scaffold Nuxt 4 SPA shell calling the API"
```

---

### Task 11: CLAUDE.md governance doc + GitHub Actions CI skeleton

**Files:**
- Create: `CLAUDE.md` (repo root)
- Create: `.github/workflows/api-tests.yml`
- Create: `.github/workflows/storefront-tests.yml`

**Interfaces:**
- Consumes: nothing (documentation + CI wiring only).
- Produces: nothing consumed by later plan tasks — this is the terminal task of the Foundation plan.

- [x] **Step 1: Write CLAUDE.md**

```markdown
<!-- CLAUDE.md -->
# PT Doctor Plant Indonesia — Toko Online

Monorepo: `api/` (Laravel 13 + Filament v5 admin) + `storefront/` (Nuxt 4 SPA).
Full design: `docs/superpowers/specs/2026-06-19-toko-online-design.md`.
Storefront UI/UX guidelines: `docs/superpowers/specs/2026-06-19-storefront-ui-design.md`.
Implementation plans: `docs/superpowers/plans/`.
REST API reference (kept in sync per endpoint task): `docs/api/`.

## Stack
- Backend: Laravel 13, PHP 8.3+, MySQL, Pest for tests, Sanctum (customer auth, `users` guard),
  separate `admin` guard + `admins` table for Filament v5.
- Frontend: Nuxt 4.4.x, `ssr: false` (SPA only — no Node runtime in production), Nuxt UI v4 component
  library, Pinia for cart/auth state, Vitest for tests.
- Payment: Midtrans Snap (popup) only. Shipping: RajaOngkir, single origin warehouse.

## Conventions
- Snapshot fields (`order_items.product_name/variant_name/price`) are intentional — never replace
  with a live join to `products`/`product_variants`. Order history must not change retroactively.
- Cart has no database table by design — it is client-side only (Pinia + localStorage).
- Customer and admin auth never share a table or guard. Don't merge `users` and `admins`.
- RajaOngkir `/cost` endpoint is billed per call — never call it outside an explicit user-initiated
  "Cek Ongkir" action. Area lookups (province/city/district) must come from the local
  `rajaongkir_*` cache tables, never a live API call.
- Any task that adds or changes a `/api/...` endpoint must update the matching file in `docs/api/`
  (one file per resource: `auth.md`, `catalog.md`, `cart-checkout.md`, `orders.md`, `shipping.md`).
  Plain markdown, not OpenAPI — method+path, auth requirement, example request/response, status codes.
- Product images: max 5 per product (`products.images` json array), max 2MB per file, jpg/png/webp
  only — enforced in the Filament `FileUpload` field, not at the DB layer. Variant image is a single
  optional file, same limits; storefront falls back to the product's first image when a variant has none.
- Storefront pages requiring a logged-in account (e.g. saved-address management) use an `auth` route
  middleware; guest checkout is allowed everywhere else, so don't gate checkout/cart behind `auth`.
  Login/register pages use a `guest-only` middleware that redirects already-authenticated users home.

## Build status tracking
Each implementation plan in `docs/superpowers/plans/` tracks its own task checkboxes. A task's
checkbox being checked means that code is considered done and locked — don't regenerate or
refactor a completed task's files unless the user explicitly asks for a change. Check the relevant
plan file before modifying any file under `api/` or `storefront/`.
```

- [x] **Step 2: Write the API CI workflow**

```yaml
# .github/workflows/api-tests.yml

name: API Tests

on:
  push:
    paths:
      - 'api/**'
  pull_request:
    paths:
      - 'api/**'

jobs:
  test:
    runs-on: ubuntu-latest
    defaults:
      run:
        working-directory: api
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --prefer-dist --no-progress
      - run: cp .env.example .env
      - run: php artisan key:generate
      - run: touch database/database.sqlite
      - run: php artisan test
        env:
          DB_CONNECTION: sqlite
          DB_DATABASE: database/database.sqlite
```

- [x] **Step 3: Write the storefront CI workflow**

```yaml
# .github/workflows/storefront-tests.yml

name: Storefront Tests

on:
  push:
    paths:
      - 'storefront/**'
  pull_request:
    paths:
      - 'storefront/**'

jobs:
  test:
    runs-on: ubuntu-latest
    defaults:
      run:
        working-directory: storefront
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '22'
      - run: npm install
      - run: npx vitest run
```

- [x] **Step 4: Verify the API workflow passes locally with the SQLite path it uses in CI**

Run: `cd api && cp .env.example .env.ci && DB_CONNECTION=sqlite DB_DATABASE=database/database.sqlite php artisan test --env=ci`
Expected: PASS (all tests from Tasks 1–9 run against SQLite, confirming the CI job's DB driver works)

- [x] **Step 5: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/PT Doctor Plant Indonesia"
git add CLAUDE.md .github/
git commit -m "docs: add CLAUDE.md governance doc and CI workflow skeletons"
```

---

## Plan Self-Review Notes

- **Spec coverage:** Architecture (§2) → Tasks 1, 3, 4, 10. Auth split → Tasks 2–3. Catalog schema (§5) → Task 5. Address/Order/Rating schema (§5) → Tasks 6–8. RajaOngkir cache + settings (§5/§7) → Task 9. CORS/Nuxt shell (§2/§8) → Task 10. CLAUDE.md/progress tracking (requested separately by user) → Task 11. Payment (Midtrans), shipping cost calls, admin Filament Resources, checkout flow, and deployment are intentionally **out of scope** for this plan — they belong to Plans 2–8 listed in the project's plan sequence.
- **Type consistency:** `ProductVariant.weight` (grams, unsigned int) matches the spec's RajaOngkir weight requirement. `Order.status` enum values match spec §4 exactly (`pending/paid/processing/shipped/completed/cancelled/expired`). `OrderItem.product_variant_id` FK name matches what Task 8's `Rating` test references via `$orderItem->variant`.
- **No placeholders:** every step has literal code or an exact command with expected output.
