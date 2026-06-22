# Admin Catalog (Filament) + Public Catalog API Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Give the admin (Filament) a real way to create/manage categories, products, and variants, and expose that data to the storefront through a public REST API (`/api/categories`, `/api/products`, `/api/products/{slug}`) — replacing the storefront's dummy data with real backend data end-to-end.

**Architecture:** Filament v5 Resources (admin guard, already configured in `AdminPanelProvider`) manage `Category`/`Product`/`ProductVariant` (the variants nested as a Filament RelationManager on the Product edit page). A separate, unauthenticated `Api\CategoryController`/`Api\ProductController` pair serves the same tables as JSON for the Nuxt storefront. Product images upload to a custom `public_uploads` disk (plain `public/uploads/`, no `storage:link` — matches the shared-hosting deployment decision in the main spec §8).

**Tech Stack:** Laravel 13 (PHP 8.3+ Attribute-based models — this codebase uses `#[Fillable(...)]` not `protected $fillable`), Filament v5, PHPUnit class-based feature tests (`extends TestCase`, `use RefreshDatabase` — this codebase does **not** use Pest, despite earlier plans assuming it), MySQL/SQLite.

## Global Constraints

- Model conventions in this codebase use PHP attributes (`#[Fillable([...])]`, `#[Hidden([...])]`) above the class, not `protected $fillable` properties — follow this exact style for any new model code.
- Tests are PHPUnit classes extending `Tests\TestCase`, methods named `test_snake_case(): void`, using `Illuminate\Foundation\Testing\RefreshDatabase`. Test DB is in-memory SQLite (`phpunit.xml`) — no manual DB setup needed.
- Product images: max 5 per product, max 2MB per file, `jpg`/`jpeg`/`png`/`webp` only (main spec §5). Variant image: 1 file, same limits.
- No `storage:link` — uploaded files go straight into `public/uploads/`, served as plain static files (main spec §8 deployment constraint for shared hosting).
- Admin has exactly one role — no permission/policy logic needed on Filament Resources.
- Public catalog API has no auth — it's read-only storefront data.
- Snapshot/rating fields (`products.rating_avg`, `rating_count`) are read-only here — they're written by the (not-yet-built) Rating feature, not by this plan.

---

## File Structure

```
api/
├── config/filesystems.php                         (modified — add `public_uploads` disk)
├── public/uploads/.gitignore                       (new — keep folder, ignore uploaded files)
├── app/Filament/Resources/
│   ├── CategoryResource.php                        (new)
│   └── CategoryResource/Pages/
│       ├── ListCategories.php                      (new)
│       ├── CreateCategory.php                      (new)
│       └── EditCategory.php                        (new)
│   ├── ProductResource.php                         (new)
│   └── ProductResource/
│       ├── Pages/{ListProducts,CreateProduct,EditProduct}.php   (new)
│       └── RelationManagers/ProductVariantsRelationManager.php  (new)
├── app/Http/Controllers/Api/
│   ├── CategoryController.php                      (new)
│   └── ProductController.php                       (new)
├── app/Http/Resources/Api/
│   ├── CategoryResource.php                        (new — JSON shape, namespaced under Http\Resources\Api to not clash with Filament\Resources\CategoryResource)
│   ├── ProductResource.php                          (new)
│   └── ProductVariantResource.php                   (new)
├── database/seeders/CatalogSeeder.php               (new)
├── routes/api.php                                   (modified — add catalog routes)
└── tests/Feature/Catalog/
    ├── AdminCatalogPanelTest.php                    (new)
    ├── CategoryApiTest.php                           (new)
    └── ProductApiTest.php                            (new)
docs/api/catalog.md                                  (new)
storefront/app/services/catalogService.ts            (modified — call real API instead of dummy data)
```

---

### Task 1: `public_uploads` disk (no symlink) for product/variant images

**Files:**
- Modify: `api/config/filesystems.php`
- Create: `api/public/uploads/.gitignore`

**Interfaces:**
- Produces: filesystem disk name `public_uploads`, used by Task 3 (`ProductResource` images FileUpload) and Task 4 (`ProductVariantsRelationManager` image FileUpload).

- [ ] **Step 1: Add the disk**

```php
<?php
// api/config/filesystems.php — add inside the 'disks' array, after 'public'

'public_uploads' => [
    'driver' => 'local',
    'root' => public_path('uploads'),
    'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/uploads',
    'visibility' => 'public',
    'throw' => false,
    'report' => false,
],
```

- [ ] **Step 2: Create the upload directory with a keep-folder `.gitignore`**

```bash
mkdir -p api/public/uploads
```

```gitignore
# api/public/uploads/.gitignore
*
!.gitignore
```

- [ ] **Step 3: Verify the disk resolves**

Run: `cd api && php artisan tinker --execute="echo Storage::disk('public_uploads')->path('');"`
Expected: prints the absolute path ending in `public/uploads` (or `public/uploads/`), no exception.

- [ ] **Step 4: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia"
git add api/config/filesystems.php api/public/uploads/.gitignore
git commit -m "feat(api): add public_uploads disk for symlink-free image storage"
```

---

### Task 2: `CategoryResource` (Filament admin CRUD)

**Files:**
- Create: `api/app/Filament/Resources/CategoryResource.php`
- Create: `api/app/Filament/Resources/CategoryResource/Pages/ListCategories.php`
- Create: `api/app/Filament/Resources/CategoryResource/Pages/CreateCategory.php`
- Create: `api/app/Filament/Resources/CategoryResource/Pages/EditCategory.php`
- Test: `api/tests/Feature/Catalog/AdminCatalogPanelTest.php`

**Interfaces:**
- Consumes: `Category::class` (already exists, Foundation Task 5).
- Produces: `/admin/categories` (list/create/edit), discovered automatically by `AdminPanelProvider`'s `discoverResources()`.

- [ ] **Step 1: Write the failing test**

```php
<?php
// api/tests/Feature/Catalog/AdminCatalogPanelTest.php

namespace Tests\Feature\Catalog;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reach_the_categories_list_page(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/categories')
            ->assertSuccessful();
    }

    public function test_admin_can_reach_the_products_list_page(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/products')
            ->assertSuccessful();
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd api && php artisan test --filter=AdminCatalogPanelTest`
Expected: FAIL — `/admin/categories` 404s, Resource not registered yet

- [ ] **Step 3: Create the Resource**

```php
<?php
// api/app/Filament/Resources/CategoryResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Models\Category;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategori';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nama Kategori')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (string $context, $state, callable $set) {
                    if ($context === 'create') {
                        $set('slug', Str::slug($state));
                    }
                }),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Select::make('parent_id')
                ->label('Kategori Induk')
                ->options(
                    fn (?Category $record) => Category::query()
                        ->when($record, fn ($query) => $query->whereNot('id', $record->id))
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug'),
                TextColumn::make('parent.name')->label('Induk')->default('—'),
                TextColumn::make('products_count')->label('Jumlah Produk')->counts('products'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Category $record) {
                        if ($record->products()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Kategori masih punya produk')
                                ->body('Pindahkan atau hapus produk di kategori ini dulu sebelum menghapus kategori.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 4: Create the Pages**

```php
<?php
// api/app/Filament/Resources/CategoryResource/Pages/ListCategories.php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
```

```php
<?php
// api/app/Filament/Resources/CategoryResource/Pages/CreateCategory.php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
```

```php
<?php
// api/app/Filament/Resources/CategoryResource/Pages/EditCategory.php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

- [ ] **Step 5: Run the categories test to verify it passes** (products test still fails until Task 3)

Run: `cd api && php artisan test --filter=AdminCatalogPanelTest::test_admin_can_reach_the_categories_list_page`
Expected: PASS

- [ ] **Step 6: Write a failing test for the delete-guard (categories with products must not cascade-delete silently)**

```php
<?php
// api/tests/Feature/Catalog/AdminCatalogPanelTest.php — append inside the class

    public function test_deleting_a_category_with_products_is_blocked(): void
    {
        $admin = Admin::factory()->create();
        $category = \App\Models\Category::factory()->create();
        \App\Models\Product::factory()->create(['category_id' => $category->id]);

        \Livewire\Livewire::actingAs($admin, 'admin')
            ->test(\App\Filament\Resources\CategoryResource\Pages\ListCategories::class)
            ->callTableAction('delete', $category);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
```

- [ ] **Step 7: Run test to verify it fails**

Run: `cd api && php artisan test --filter=test_deleting_a_category_with_products_is_blocked`
Expected: FAIL — category gets deleted (no guard yet), `assertDatabaseHas` fails

- [ ] **Step 8: Run the full file to verify the guard (already written in Step 3 above) makes it pass**

Run: `cd api && php artisan test --filter=AdminCatalogPanelTest`
Expected: PASS (3 tests)

- [ ] **Step 9: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia"
git add api/app/Filament/Resources/CategoryResource.php api/app/Filament/Resources/CategoryResource/
git commit -m "feat(api): add Filament CategoryResource (admin CRUD)"
```

---

### Task 3: `ProductResource` (Filament admin CRUD with image upload)

**Files:**
- Create: `api/app/Filament/Resources/ProductResource.php`
- Create: `api/app/Filament/Resources/ProductResource/Pages/{ListProducts,CreateProduct,EditProduct}.php`

**Interfaces:**
- Consumes: `Product::class` (Foundation Task 5), `public_uploads` disk (Task 1).
- Produces: `/admin/products` (list/create/edit). `ProductResource::class` itself is consumed by Task 4 (the variants RelationManager attaches to this Resource's Edit page).

- [ ] **Step 1: Create the Resource**

```php
<?php
// api/app/Filament/Resources/ProductResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\RelationManagers\ProductVariantsRelationManager;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Produk';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('category_id')
                ->label('Kategori')
                ->options(fn () => Category::query()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('name')
                ->label('Nama Produk')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (string $context, $state, callable $set) {
                    if ($context === 'create') {
                        $set('slug', Str::slug($state));
                    }
                }),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(4),
            FileUpload::make('images')
                ->label('Gambar Produk')
                ->disk('public_uploads')
                ->directory('products')
                ->visibility('public')
                ->multiple()
                ->maxFiles(5)
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->reorderable()
                ->helperText('Maksimal 5 gambar, masing-masing maks. 2MB, format JPG/PNG/WEBP.'),
            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Gambar')
                    ->disk('public_uploads')
                    ->getStateUsing(fn (Product $record) => $record->images[0] ?? null)
                    ->circular(),
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Kategori'),
                TextColumn::make('variants_count')->label('Varian')->counts('variants'),
                TextColumn::make('rating_avg')->label('Rating')->formatStateUsing(
                    fn (?string $state) => $state ? number_format((float) $state, 1) : '—'
                ),
                ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductVariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 2: Create the Pages**

```php
<?php
// api/app/Filament/Resources/ProductResource/Pages/ListProducts.php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
```

```php
<?php
// api/app/Filament/Resources/ProductResource/Pages/CreateProduct.php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

```php
<?php
// api/app/Filament/Resources/ProductResource/Pages/EditProduct.php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

- [ ] **Step 3: Run the products panel test to verify it passes**

Run: `cd api && php artisan test --filter=AdminCatalogPanelTest`
Expected: PASS (both tests — categories from Task 2, products now too)

- [ ] **Step 4: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia"
git add api/app/Filament/Resources/ProductResource.php api/app/Filament/Resources/ProductResource/Pages/
git commit -m "feat(api): add Filament ProductResource with image upload"
```

---

### Task 4: `ProductVariantsRelationManager` (nested CRUD on Product edit page)

**Files:**
- Create: `api/app/Filament/Resources/ProductResource/RelationManagers/ProductVariantsRelationManager.php`

**Interfaces:**
- Consumes: `Product::variants()` relation (Foundation Task 5), `ProductResource::class` (Task 3), `public_uploads` disk (Task 1).
- Produces: nothing consumed by later tasks — this is the leaf of the admin catalog UI.

- [ ] **Step 1: Create the RelationManager**

```php
<?php
// api/app/Filament/Resources/ProductResource/RelationManagers/ProductVariantsRelationManager.php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Varian';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nama Varian')
                ->required()
                ->maxLength(255)
                ->helperText('Contoh: 250ml, 500ml, 1kg'),
            TextInput::make('sku')
                ->label('SKU')
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('price')
                ->label('Harga (Rp)')
                ->numeric()
                ->required()
                ->minValue(0),
            TextInput::make('stock')
                ->label('Stok')
                ->numeric()
                ->required()
                ->minValue(0)
                ->default(0),
            TextInput::make('weight')
                ->label('Berat (gram)')
                ->numeric()
                ->required()
                ->minValue(1)
                ->helperText('Wajib diisi — dipakai untuk hitung ongkir RajaOngkir.'),
            FileUpload::make('image')
                ->label('Gambar Varian (opsional)')
                ->disk('public_uploads')
                ->directory('variants')
                ->visibility('public')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->helperText('Kosongkan untuk pakai gambar produk utama.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Gambar')->disk('public_uploads')->circular(),
                TextColumn::make('name')->label('Nama'),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('price')->label('Harga')->money('IDR'),
                TextColumn::make('stock')->label('Stok'),
                TextColumn::make('weight')->label('Berat (gr)'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
```

- [ ] **Step 2: Verify the Product edit page loads with the relation manager**

Run: `cd api && php artisan test --filter=AdminCatalogPanelTest`
Expected: PASS (unchanged — relation manager only renders on the Edit page, not List; this step is a manual sanity check, run `php artisan serve` and open `/admin/products/{id}/edit` in a browser to confirm the "Varian" tab appears)

- [ ] **Step 3: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia"
git add api/app/Filament/Resources/ProductResource/RelationManagers/
git commit -m "feat(api): add ProductVariantsRelationManager nested on Product edit page"
```

---

### Task 5: `CatalogSeeder` (realistic sample data)

**Files:**
- Create: `api/database/seeders/CatalogSeeder.php`
- Modify: `api/database/seeders/DatabaseSeeder.php`

**Interfaces:**
- Consumes: `Category::class`, `Product::class`, `ProductVariant::class`.
- Produces: seeded rows so the admin panel and public API (Tasks 6-8) have real data to manually verify against, beyond factory-generated test data.

- [ ] **Step 1: Write the seeder**

```php
<?php
// api/database/seeders/CatalogSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Hidrasi & Pelembab' => 'hidrasi-pelembab',
            'Sheet Mask' => 'sheet-mask',
            'Perawatan Alpine' => 'perawatan-alpine',
        ];

        $categoryModels = [];
        foreach ($categories as $name => $slug) {
            $categoryModels[$slug] = Category::create(['name' => $name, 'slug' => $slug]);
        }

        $products = [
            [
                'category' => 'hidrasi-pelembab',
                'name' => 'Alpine Moisture Cream',
                'slug' => 'alpine-moisture-cream',
                'description' => 'Krim pelembab dengan ekstrak tumbuhan alpine untuk hidrasi mendalam.',
                'variants' => [
                    ['name' => '50ml', 'sku' => 'AMC-50', 'price' => 89000, 'stock' => 40, 'weight' => 80],
                    ['name' => '100ml', 'sku' => 'AMC-100', 'price' => 149000, 'stock' => 25, 'weight' => 150],
                ],
            ],
            [
                'category' => 'sheet-mask',
                'name' => 'Glacier Hydrating Sheet Mask',
                'slug' => 'glacier-hydrating-sheet-mask',
                'description' => 'Sheet mask dengan essence glacier water, melembabkan instan dalam 15 menit.',
                'variants' => [
                    ['name' => '1 lembar', 'sku' => 'GHS-1', 'price' => 15000, 'stock' => 100, 'weight' => 30],
                    ['name' => 'Box isi 5', 'sku' => 'GHS-5', 'price' => 70000, 'stock' => 30, 'weight' => 150],
                ],
            ],
            [
                'category' => 'perawatan-alpine',
                'name' => 'Alpine Botanical Serum',
                'slug' => 'alpine-botanical-serum',
                'description' => 'Serum dengan kandungan botanical alpine untuk kulit lebih kenyal dan cerah.',
                'variants' => [
                    ['name' => '30ml', 'sku' => 'ABS-30', 'price' => 165000, 'stock' => 20, 'weight' => 60],
                ],
            ],
        ];

        foreach ($products as $data) {
            $product = Product::create([
                'category_id' => $categoryModels[$data['category']]->id,
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'images' => [],
                'is_active' => true,
            ]);

            foreach ($data['variants'] as $variant) {
                $product->variants()->create($variant);
            }
        }
    }
}
```

- [ ] **Step 2: Wire it into `DatabaseSeeder`**

```php
<?php
// api/database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CatalogSeeder::class,
        ]);
    }
}
```

- [ ] **Step 3: Run the seeder against the local dev database**

Run: `cd api && php artisan db:seed`
Expected: no errors; `php artisan tinker --execute="echo App\Models\Product::count();"` prints `3`

- [ ] **Step 4: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia"
git add api/database/seeders/
git commit -m "feat(api): add CatalogSeeder with sample categories/products/variants"
```

---

### Task 6: Public API — `GET /api/categories`

**Files:**
- Create: `api/app/Http/Resources/Api/CategoryResource.php`
- Create: `api/app/Http/Controllers/Api/CategoryController.php`
- Modify: `api/routes/api.php`
- Test: `api/tests/Feature/Catalog/CategoryApiTest.php`

**Interfaces:**
- Consumes: `Category::class`.
- Produces: `GET /api/categories` → `{"data": [{"id", "name", "slug", "parent_id"}, ...]}`. Storefront's `catalogService.getCategories()` (Task 10) calls this.

- [ ] **Step 1: Write the failing test**

```php
<?php
// api/tests/Feature/Catalog/CategoryApiTest.php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_all_categories(): void
    {
        Category::factory()->create(['name' => 'Pupuk', 'slug' => 'pupuk']);
        Category::factory()->create(['name' => 'Bibit', 'slug' => 'bibit']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['slug' => 'pupuk'])
            ->assertJsonFragment(['slug' => 'bibit']);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd api && php artisan test --filter=CategoryApiTest`
Expected: FAIL — route not defined (404)

- [ ] **Step 3: Create the API Resource**

```php
<?php
// api/app/Http/Resources/Api/CategoryResource.php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
        ];
    }
}
```

- [ ] **Step 4: Create the Controller**

```php
<?php
// api/app/Http/Controllers/Api/CategoryController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(Category::query()->orderBy('name')->get());
    }
}
```

- [ ] **Step 5: Add the route**

```php
<?php
// api/routes/api.php — add below the existing routes, outside the auth:sanctum group

use App\Http\Controllers\Api\CategoryController;

Route::get('/categories', [CategoryController::class, 'index']);
```

- [ ] **Step 6: Run test to verify it passes**

Run: `cd api && php artisan test --filter=CategoryApiTest`
Expected: PASS

- [ ] **Step 7: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia"
git add api/app/Http/Resources/Api/CategoryResource.php api/app/Http/Controllers/Api/CategoryController.php api/routes/api.php
git commit -m "feat(api): add public GET /api/categories endpoint"
```

---

### Task 7: Public API — `GET /api/products` (filter/sort/search/pagination)

**Files:**
- Create: `api/app/Http/Resources/Api/ProductVariantResource.php`
- Create: `api/app/Http/Resources/Api/ProductResource.php`
- Create: `api/app/Http/Controllers/Api/ProductController.php`
- Modify: `api/routes/api.php`
- Test: `api/tests/Feature/Catalog/ProductApiTest.php`

**Interfaces:**
- Consumes: `Product::class`, `ProductVariant::class`, `Category::class`.
- Produces: `GET /api/products` → `{"data": [...], "meta": {"has_more", "next_page", "total"}}`. Storefront's `catalogService.getProducts()` (Task 10) calls this. Also supports `?ids=1,2,3` (used by wishlist/`getProductsByIds`).

- [ ] **Step 1: Write the failing tests**

```php
<?php
// api/tests/Feature/Catalog/ProductApiTest.php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_active_products_with_pagination_meta(): void
    {
        Product::factory()->count(10)->create(['is_active' => true])
            ->each(fn (Product $p) => ProductVariant::factory()->create(['product_id' => $p->id, 'price' => 10000, 'stock' => 5]));
        Product::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/products?page=1');

        $response->assertStatus(200)
            ->assertJsonCount(8, 'data')
            ->assertJsonPath('meta.has_more', true)
            ->assertJsonPath('meta.total', 10);
    }

    public function test_filters_by_category_slug(): void
    {
        $catA = Category::factory()->create(['slug' => 'pupuk']);
        $catB = Category::factory()->create(['slug' => 'pestisida']);
        $productA = Product::factory()->create(['category_id' => $catA->id]);
        ProductVariant::factory()->create(['product_id' => $productA->id]);
        $productB = Product::factory()->create(['category_id' => $catB->id]);
        ProductVariant::factory()->create(['product_id' => $productB->id]);

        $response = $this->getJson('/api/products?category=pupuk');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $productA->id);
    }

    public function test_searches_by_name(): void
    {
        $match = Product::factory()->create(['name' => 'Pupuk NPK Mutiara']);
        ProductVariant::factory()->create(['product_id' => $match->id]);
        $noMatch = Product::factory()->create(['name' => 'Sprayer Elektrik']);
        ProductVariant::factory()->create(['product_id' => $noMatch->id]);

        $response = $this->getJson('/api/products?search=npk');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    public function test_filters_by_ids(): void
    {
        $products = Product::factory()->count(3)->create();
        foreach ($products as $p) {
            ProductVariant::factory()->create(['product_id' => $p->id]);
        }

        $response = $this->getJson('/api/products?ids='.$products[0]->id.','.$products[2]->id);

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_sorts_by_price_ascending_using_cheapest_variant(): void
    {
        $expensive = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $expensive->id, 'price' => 90000]);
        $cheap = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $cheap->id, 'price' => 10000]);

        $response = $this->getJson('/api/products?sort=price_asc');

        $response->assertStatus(200)->assertJsonPath('data.0.id', $cheap->id);
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `cd api && php artisan test --filter=ProductApiTest`
Expected: FAIL — route not defined

- [ ] **Step 3: Create the API Resources**

```php
<?php
// api/app/Http/Resources/Api/ProductVariantResource.php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'weight' => $this->weight,
            'image' => $this->image ? asset('uploads/'.$this->image) : null,
        ];
    }
}
```

```php
<?php
// api/app/Http/Resources/Api/ProductResource.php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'images' => collect($this->images ?? [])->map(fn ($path) => asset('uploads/'.$path))->all(),
            'is_active' => $this->is_active,
            'rating_avg' => (float) $this->rating_avg,
            'rating_count' => $this->rating_count,
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
```

- [ ] **Step 4: Create the Controller**

```php
<?php
// api/app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private const PER_PAGE = 8;

    public function index(Request $request)
    {
        $query = Product::query()
            ->with('variants')
            ->where('is_active', true)
            ->withMin('variants', 'price');

        if ($request->filled('ids')) {
            $ids = array_filter(array_map('intval', explode(',', $request->string('ids'))));
            $query->whereIn('id', $ids);
        }

        if ($request->filled('category')) {
            $categoryId = Category::query()->where('slug', $request->string('category'))->value('id');
            $query->where('category_id', $categoryId ?? 0);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('min')) {
            $query->having('variants_min_price', '>=', (int) $request->input('min'));
        }

        if ($request->filled('max')) {
            $query->having('variants_min_price', '<=', (int) $request->input('max'));
        }

        match ($request->string('sort')->toString()) {
            'price_asc' => $query->orderBy('variants_min_price', 'asc'),
            'price_desc' => $query->orderBy('variants_min_price', 'desc'),
            'rating' => $query->orderBy('rating_avg', 'desc'),
            default => $query->orderBy('id', 'desc'),
        };

        $page = max(1, (int) $request->input('page', 1));
        $total = $query->clone()->count('products.id');
        $products = $query->forPage($page, self::PER_PAGE)->get();

        return ProductResource::collection($products)->additional([
            'meta' => [
                'has_more' => $page * self::PER_PAGE < $total,
                'next_page' => ($page * self::PER_PAGE < $total) ? $page + 1 : null,
                'total' => $total,
            ],
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::query()
            ->with('variants')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return new ProductResource($product);
    }
}
```

- [ ] **Step 5: Add the routes**

```php
<?php
// api/routes/api.php — add below the categories route

use App\Http\Controllers\Api\ProductController;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
```

- [ ] **Step 6: Run tests to verify they pass**

Run: `cd api && php artisan test --filter=ProductApiTest`
Expected: PASS (5 tests)

- [ ] **Step 7: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia"
git add api/app/Http/Resources/Api/ProductResource.php api/app/Http/Resources/Api/ProductVariantResource.php api/app/Http/Controllers/Api/ProductController.php api/routes/api.php
git commit -m "feat(api): add public GET /api/products with filter/sort/search/pagination"
```

---

### Task 8: Public API — `GET /api/products/{slug}` test coverage

**Files:**
- Test: `api/tests/Feature/Catalog/ProductApiTest.php` (append to the file from Task 7)

**Interfaces:**
- Consumes: `ProductController::show()` (already implemented in Task 7, Step 4 — this task is purely test coverage that was deferred to keep Task 7 focused on the list endpoint).

- [ ] **Step 1: Write the failing tests**

```php
<?php
// api/tests/Feature/Catalog/ProductApiTest.php — append these two test methods inside the class

    public function test_shows_a_single_product_with_its_variants(): void
    {
        $product = Product::factory()->create(['slug' => 'pupuk-npk-mutiara']);
        ProductVariant::factory()->create(['product_id' => $product->id, 'name' => '1kg']);

        $response = $this->getJson('/api/products/pupuk-npk-mutiara');

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', 'pupuk-npk-mutiara')
            ->assertJsonCount(1, 'data.variants');
    }

    public function test_returns_404_for_unknown_slug(): void
    {
        $response = $this->getJson('/api/products/does-not-exist');

        $response->assertStatus(404);
    }

    public function test_returns_404_for_inactive_product(): void
    {
        Product::factory()->create(['slug' => 'inactive-product', 'is_active' => false]);

        $response = $this->getJson('/api/products/inactive-product');

        $response->assertStatus(404);
    }
```

- [ ] **Step 2: Run tests to verify they pass**

Run: `cd api && php artisan test --filter=ProductApiTest`
Expected: PASS (8 tests total)

- [ ] **Step 3: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia"
git add api/tests/Feature/Catalog/ProductApiTest.php
git commit -m "test(api): cover GET /api/products/{slug} success and 404 cases"
```

---

### Task 9: `docs/api/catalog.md`

**Files:**
- Create: `docs/api/catalog.md`

**Interfaces:**
- Consumes: nothing (documentation only — required by the project convention in main spec §9).

- [ ] **Step 1: Write the doc**

```markdown
<!-- docs/api/catalog.md -->
# Catalog API

## GET /api/categories

Auth: none.

**Response 200**
```json
{
  "data": [
    { "id": 1, "name": "Pupuk", "slug": "pupuk", "parent_id": null }
  ]
}
```

## GET /api/products

Auth: none.

**Query params**
| Param | Type | Notes |
|---|---|---|
| `category` | string | category slug |
| `min`, `max` | int | price range, matched against cheapest variant |
| `search` | string | matches against product name |
| `sort` | `newest` (default) \| `price_asc` \| `price_desc` \| `rating` | |
| `page` | int | 8 products per page |
| `ids` | string | comma-separated product IDs, overrides other filters' product set |

**Response 200**
```json
{
  "data": [
    {
      "id": 1,
      "category_id": 1,
      "name": "Alpine Moisture Cream",
      "slug": "alpine-moisture-cream",
      "description": "...",
      "images": ["https://.../uploads/products/abc.jpg"],
      "is_active": true,
      "rating_avg": 4.6,
      "rating_count": 12,
      "variants": [
        { "id": 1, "name": "50ml", "sku": "AMC-50", "price": 89000, "stock": 40, "weight": 80, "image": null }
      ]
    }
  ],
  "meta": { "has_more": true, "next_page": 2, "total": 24 }
}
```

## GET /api/products/{slug}

Auth: none. 404 if not found or `is_active = false`.

**Response 200**: same shape as one item of `/api/products` `data[]`.
```

- [ ] **Step 2: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia"
git add docs/api/catalog.md
git commit -m "docs: add catalog API reference"
```

---

### Task 10: Storefront — swap dummy catalog data for the real API

**Files:**
- Modify: `storefront/app/services/catalogService.ts`

**Interfaces:**
- Consumes: `GET /api/categories`, `GET /api/products`, `GET /api/products/{slug}` (Tasks 6-8).
- Produces: same exported function signatures as before (`getCategories`, `getProducts`, `getFeaturedProducts`, `getProductBySlug`, `getProductsByIds`, `isOutOfStock`, `minPrice`) — every page/component (`ProductCard.vue`, `ProductGrid.vue`, `katalog/index.vue`, `produk/[slug].vue`, `wishlist/index.vue`, `store/catalog.ts`) keeps working unchanged.

- [ ] **Step 1: Replace the dummy implementation with real `$fetch` calls**

```typescript
// storefront/app/services/catalogService.ts

import type { Category, CatalogFilters, PaginatedResult, Product, ProductVariant } from '~/types'

function mapVariant(raw: any): ProductVariant {
  return {
    id: raw.id,
    productId: raw.product_id ?? 0,
    name: raw.name,
    sku: raw.sku,
    price: raw.price,
    stock: raw.stock,
    weight: raw.weight,
    image: raw.image,
  }
}

function mapProduct(raw: any): Product {
  return {
    id: raw.id,
    categoryId: raw.category_id,
    name: raw.name,
    slug: raw.slug,
    description: raw.description,
    images: raw.images ?? [],
    isActive: raw.is_active,
    ratingAvg: raw.rating_avg,
    ratingCount: raw.rating_count,
    variants: (raw.variants ?? []).map(mapVariant),
  }
}

function mapCategory(raw: any): Category {
  return {
    id: raw.id,
    name: raw.name,
    slug: raw.slug,
    parentId: raw.parent_id,
  }
}

function apiBase(): string {
  return useRuntimeConfig().public.apiBase
}

function minPrice(product: Product): number {
  return Math.min(...product.variants.map((v) => v.price))
}

function isOutOfStock(product: Product): boolean {
  return product.variants.every((v) => v.stock === 0)
}

export const catalogService = {
  async getCategories(): Promise<Category[]> {
    const response = await $fetch<{ data: any[] }>('/categories', { baseURL: apiBase() })
    return response.data.map(mapCategory)
  },

  async getProducts(filters: Partial<CatalogFilters> = {}): Promise<PaginatedResult<Product>> {
    const response = await $fetch<{ data: any[]; meta: { has_more: boolean; next_page: number | null; total: number } }>(
      '/products',
      {
        baseURL: apiBase(),
        query: {
          category: filters.category ?? undefined,
          min: filters.minPrice ?? undefined,
          max: filters.maxPrice ?? undefined,
          search: filters.search || undefined,
          sort: filters.sort ?? undefined,
          page: filters.page ?? 1,
        },
      },
    )

    return {
      data: response.data.map(mapProduct),
      hasMore: response.meta.has_more,
      nextPage: response.meta.next_page,
      total: response.meta.total,
    }
  },

  async getFeaturedProducts(limit = 4): Promise<Product[]> {
    const result = await catalogService.getProducts({ sort: 'rating', page: 1 })
    return result.data.slice(0, limit)
  },

  async getProductBySlug(slug: string): Promise<Product | null> {
    try {
      const response = await $fetch<{ data: any }>(`/products/${slug}`, { baseURL: apiBase() })
      return mapProduct(response.data)
    } catch {
      return null
    }
  },

  async getProductsByIds(ids: number[]): Promise<Product[]> {
    if (ids.length === 0) return []
    const response = await $fetch<{ data: any[] }>('/products', {
      baseURL: apiBase(),
      query: { ids: ids.join(',') },
    })
    return response.data.map(mapProduct)
  },

  isOutOfStock,
  minPrice,
}
```

- [ ] **Step 2: Delete the now-unused dummy data module**

```bash
rm "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia/storefront/app/services/dummyData.ts"
```

- [ ] **Step 3: Manually verify end to end**

Run in two terminals:
```bash
cd api && php artisan serve
```
```bash
cd storefront && npm run dev
```
Open `http://localhost:3000/katalog`. Expected: the 3 products seeded in Task 5 (`Alpine Moisture Cream`, `Glacier Hydrating Sheet Mask`, `Alpine Botanical Serum`) appear — not the old hardcoded dummy catalog. Click into a product detail page, confirm variants/price/stock render correctly.

- [ ] **Step 4: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/pt-doctor-plant-indonesia"
git add storefront/app/services/catalogService.ts
git rm storefront/app/services/dummyData.ts
git commit -m "feat(storefront): consume real catalog API instead of dummy data"
```

---

## Plan Self-Review Notes

- **Spec coverage:** §11.1 controllers → `CategoryController`/`ProductController` built (Tasks 6-7); `AddressController`/`OrderController`/`ShippingController`/`RatingController`/`WebhookController`/`ProfileController`/`SettingsController` intentionally **not** in this plan — they belong to the Cart & Checkout / Payment & Order / Admin Order plans per the project's plan sequence. §11.2 endpoints → `GET /api/categories`, `GET /api/products`, `GET /api/products/{slug}` built; all other listed endpoints out of scope here. §11.3 admin resources → `CategoryResource`, `ProductResource`, `ProductVariantsRelationManager` built; `OrderResource`, `CustomerResource`, `SettingsPage`, Dashboard widgets out of scope here.
- **Type consistency:** storefront `Product`/`ProductVariant`/`Category` types (camelCase) map 1:1 against the API's snake_case JSON via the `map*` functions in Task 10 — field names cross-checked against `app/Http/Resources/Api/*.php` in Tasks 6-7.
- **No placeholders:** every step has literal code, exact commands, and expected output.
- **Deviation from Foundation plan's assumptions:** Foundation plan (`2026-06-19-foundation.md`) assumed Pest and `protected $fillable`. Actual codebase uses PHPUnit classes and PHP attributes (`#[Fillable]`). This plan follows the **actual** codebase convention, not the original Foundation draft — Foundation's plan document is now stale on these two points and should not be used as a style reference for future plans.
- **Bug fixes applied during review (2026-06-22), before execution:**
  - `categories` → `products` FK is `cascadeOnDelete()` (Foundation Task 5, already migrated). Deleting a category through `CategoryResource` would silently delete every product under it. Fixed by adding a `before()` guard on `DeleteAction` (Task 2, Step 3 and Step 6-8) that blocks the delete and shows a notification when the category still has products. The underlying FK constraint itself is left as `cascadeOnDelete()` — not changed in this plan, since that needs a new migration and the app-level guard already prevents the dangerous path.
  - `CategoryResource`'s `parent_id` select did not exclude the record being edited, allowing a category to be set as its own parent. Fixed with a `whereNot('id', $record->id)` clause in the options query (Task 2, Step 3).
  - Confirmed `config/cors.php` was missing from the real `api/` codebase (Foundation Task 10 was never actually executed for the CORS step, even though the rest of Foundation was). Fixed directly in the live codebase (not just this plan) — added `api/config/cors.php` and `STOREFRONT_URL` to `.env`/`.env.example`, verified via a live CORS preflight request that `Access-Control-Allow-Origin` now returns correctly.
