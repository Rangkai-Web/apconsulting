# Admin Order Management (OrderResource) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Give admin a Filament `OrderResource` to see incoming orders, view full read-only order detail, and update status + input tracking number — closing the operational gap where customers can pay but admin can't process orders.

**Architecture:** A read-only `OrderResource` (List + View, no Create/Edit) renders order data via Filament table/infolist. A header `Action` on the View page handles status updates in a modal — the only mutation path. Stock-release-on-cancel logic is extracted from `WebhookController` into `Order::releaseStockIfApplicable()` so both the webhook and the new admin action share one implementation.

**Tech Stack:** Laravel 13, Filament v5 (`Filament\Schemas`, `Filament\Infolists\Components`, `Filament\Actions`), PHPUnit + Livewire testing helpers (`Livewire::test()->callAction()`).

## Global Constraints

- Spec: `docs/superpowers/specs/2026-06-30-order-resource-admin-design.md`.
- Order items, price, shipping address are snapshots — never live-joined or made editable (project CLAUDE.md). `OrderResource` has no Create page and no Edit page — only List and View.
- Status dropdown in the Update Status action has no transition restriction — admin can pick any of the 7 statuses (spec §3, explicit decision).
- `tracking_number` field is visible/required in the Update Status form only when the selected `status` (in the form, not necessarily the order's current status) is `shipped`.
- Stock release on cancel/expire only fires when `$oldStatus === 'pending'` — this guard must be identical in both `WebhookController` and the new admin action (spec §4). Cancelling an already-`paid`/`processing` order does NOT auto-release stock.
- Panel guard is `admin` (`AdminPanelProvider.php`), path prefix `/admin` — same as existing `ProductResource`/`CategoryResource`/`BannerResource`. No new auth/RBAC needed.
- Run tests with: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test` (run from `api/`).

---

### Task 1: Extract `Order::releaseStockIfApplicable()`, refactor `WebhookController`

**Files:**
- Modify: `api/app/Models/Order.php`
- Modify: `api/app/Http/Controllers/Api/WebhookController.php:86-94`
- Test: `api/tests/Feature/Order/OrderReleaseStockTest.php`

**Interfaces:**
- Produces: `App\Models\Order::releaseStockIfApplicable(string $oldStatus, string $newStatus): void`. No-op unless `$newStatus` is `cancelled` or `expired` AND `$oldStatus === 'pending'`. When it fires, increments `product_variants.stock` by each order item's `qty` for every item with a non-null `variant` relation, and logs via `Log::info`.

- [x] **Step 1: Write the failing test**

Create `api/tests/Feature/Order/OrderReleaseStockTest.php`:

```php
<?php

namespace Tests\Feature\Order;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderReleaseStockTest extends TestCase
{
    use RefreshDatabase;

    private function createOrderWithItem(string $status, int $qty = 2): array
    {
        $category = Category::create(['name' => 'Skincare', 'slug' => 'skincare']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sunscreen',
            'slug' => 'sunscreen',
            'description' => 'Test',
            'images' => ['sunscreen.png'],
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '50ml',
            'sku' => 'SUN-50',
            'price' => 100000,
            'stock' => 8,
            'weight' => 200,
        ]);
        $order = Order::create([
            'order_number' => 'ORD-RELEASE-TEST',
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => $status,
        ]);
        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'price' => 100000,
            'qty' => $qty,
            'subtotal' => 200000,
        ]);

        return [$order, $variant];
    }

    public function test_releases_stock_when_old_status_pending_and_new_status_cancelled(): void
    {
        [$order, $variant] = $this->createOrderWithItem('cancelled', qty: 2);

        $order->releaseStockIfApplicable('pending', 'cancelled');

        $this->assertEquals(10, $variant->fresh()->stock);
    }

    public function test_releases_stock_when_old_status_pending_and_new_status_expired(): void
    {
        [$order, $variant] = $this->createOrderWithItem('expired', qty: 2);

        $order->releaseStockIfApplicable('pending', 'expired');

        $this->assertEquals(10, $variant->fresh()->stock);
    }

    public function test_does_not_release_stock_when_old_status_is_not_pending(): void
    {
        [$order, $variant] = $this->createOrderWithItem('cancelled', qty: 2);

        $order->releaseStockIfApplicable('paid', 'cancelled');

        $this->assertEquals(8, $variant->fresh()->stock);
    }

    public function test_does_not_release_stock_when_new_status_is_not_cancelled_or_expired(): void
    {
        [$order, $variant] = $this->createOrderWithItem('processing', qty: 2);

        $order->releaseStockIfApplicable('pending', 'processing');

        $this->assertEquals(8, $variant->fresh()->stock);
    }
}
```

- [x] **Step 2: Run test to verify it fails**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=OrderReleaseStockTest`
Expected: FAIL with `Call to undefined method App\Models\Order::releaseStockIfApplicable()`.

- [x] **Step 3: Add the method to `Order`**

Read `api/app/Models/Order.php` first. Add `use Illuminate\Support\Facades\Log;` to the imports, and add this method to the class body (after the `items()` relation method):

```php
    public function releaseStockIfApplicable(string $oldStatus, string $newStatus): void
    {
        if (! in_array($newStatus, ['cancelled', 'expired']) || $oldStatus !== 'pending') {
            return;
        }

        foreach ($this->items()->with('variant')->get() as $item) {
            if ($item->variant) {
                $item->variant->increment('stock', $item->qty);
            }
        }

        Log::info("Released stock for cancelled/expired order {$this->order_number}.");
    }
```

- [x] **Step 4: Run test to verify it passes**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=OrderReleaseStockTest`
Expected: PASS (4 tests).

- [x] **Step 5: Refactor `WebhookController` to use the new method**

Read `api/app/Http/Controllers/Api/WebhookController.php` first. Replace lines 86-94:

```php
        // Release stocks back if order becomes cancelled or expired
        if (in_array($newStatus, ['cancelled', 'expired']) && $oldStatus === 'pending') {
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('stock', $item->qty);
                }
            }
            Log::info("Midtrans Webhook: Released stock for cancelled/expired order {$order->order_number}.");
        }
```

with:

```php
        $order->releaseStockIfApplicable($oldStatus, $newStatus);
```

- [x] **Step 6: Run the existing webhook tests to confirm no regression**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=WebhookControllerTest`
Expected: PASS (2 tests) — same behavior as before the refactor, log message text changes (no longer prefixed "Midtrans Webhook:") but that's not asserted by any test.

- [x] **Step 7: Commit**

```bash
git add api/app/Models/Order.php api/app/Http/Controllers/Api/WebhookController.php api/tests/Feature/Order/OrderReleaseStockTest.php
git commit -m "refactor(api): extract Order::releaseStockIfApplicable from WebhookController"
```

---

### Task 2: `OrderResource` — List + read-only View (no mutation yet)

**Files:**
- Create: `api/app/Filament/Resources/OrderResource.php`
- Create: `api/app/Filament/Resources/OrderResource/Pages/ListOrders.php`
- Create: `api/app/Filament/Resources/OrderResource/Pages/ViewOrder.php`
- Test: `api/tests/Feature/Order/OrderResourcePanelTest.php`

**Interfaces:**
- Consumes: `App\Models\Order` (existing model, relations `user()`, `items()`), `App\Models\OrderItem` (fields `product_name`, `variant_name`, `qty`, `price`, `subtotal`).
- Produces: `App\Filament\Resources\OrderResource::statusColor(string $status): string` — used by Task 3's status `Select` field for consistent coloring if needed, and reused internally by both table and infolist status badges. Routes: `GET /admin/orders` (list), `GET /admin/orders/{record}` (view).

- [x] **Step 1: Write the failing test**

Create `api/tests/Feature/Order/OrderResourcePanelTest.php`:

```php
<?php

namespace Tests\Feature\Order;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderResourcePanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_page(): void
    {
        $response = $this->get('/admin/orders');

        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_admin_can_view_order_list(): void
    {
        $admin = Admin::factory()->create();
        Order::create([
            'order_number' => 'ORD-LIST-TEST',
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/orders');

        $response->assertStatus(200)->assertSee('ORD-LIST-TEST');
    }

    public function test_authenticated_admin_can_view_order_detail_with_items_and_address(): void
    {
        $admin = Admin::factory()->create();

        $category = Category::create(['name' => 'Skincare', 'slug' => 'skincare']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sunscreen',
            'slug' => 'sunscreen',
            'description' => 'Test',
            'images' => ['sunscreen.png'],
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '50ml',
            'sku' => 'SUN-50',
            'price' => 100000,
            'stock' => 10,
            'weight' => 200,
        ]);
        $order = Order::create([
            'order_number' => 'ORD-VIEW-TEST',
            'guest_name' => 'Guest Tester',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => [
                'recipient_name' => 'Guest Tester',
                'phone' => '0899',
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Coblong',
                'postal_code' => '40123',
                'full_address' => 'Jl. Contoh No. 1',
            ],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => 'pending',
        ]);
        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'price' => 100000,
            'qty' => 2,
            'subtotal' => 200000,
        ]);

        $response = $this->actingAs($admin, 'admin')->get("/admin/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertSee('ORD-VIEW-TEST')
            ->assertSee('Guest Tester')
            ->assertSee('Sunscreen');
    }
}
```

- [x] **Step 2: Run test to verify it fails**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=OrderResourcePanelTest`
Expected: FAIL on all three tests — `/admin/orders` and `/admin/orders/{id}` routes don't exist yet (404), so `assertRedirect('/admin/login')` and `assertStatus(200)` both fail.

- [x] **Step 3: Create `OrderResource.php`**

Create `api/app/Filament/Resources/OrderResource.php`:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use App\Models\Order;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Pesanan';

    protected static ?string $modelLabel = 'Pesanan';

    protected static ?string $pluralModelLabel = 'Pesanan';

    protected static ?int $navigationSort = 5;

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'pending' => 'gray',
            'paid' => 'info',
            'processing' => 'warning',
            'shipped' => 'primary',
            'completed' => 'success',
            'cancelled', 'expired' => 'danger',
            default => 'gray',
        };
    }

    public static function statusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer')
                    ->label('Pelanggan')
                    ->state(fn (Order $record): string => $record->user?->name ?? $record->guest_name ?? '-'),
                TextColumn::make('customer_type')
                    ->label('Tipe')
                    ->state(fn (Order $record): string => $record->user_id ? 'Member' : 'Guest')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Member' ? 'success' : 'gray'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => self::statusColor($state)),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR'),
                TextColumn::make('courier')
                    ->label('Kurir')
                    ->formatStateUsing(fn (Order $record): string => $record->courier ? strtoupper($record->courier).' - '.$record->courier_service : '-'),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(self::statusOptions()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pesanan')
                ->schema([
                    TextEntry::make('order_number')->label('No. Pesanan'),
                    TextEntry::make('status')->label('Status')->badge()->color(fn (string $state): string => self::statusColor($state)),
                    TextEntry::make('created_at')->label('Tanggal Pesanan')->dateTime('d M Y H:i'),
                    TextEntry::make('courier')
                        ->label('Kurir')
                        ->formatStateUsing(fn (Order $record): string => $record->courier ? strtoupper($record->courier).' - '.$record->courier_service : '-'),
                    TextEntry::make('tracking_number')->label('No. Resi')->placeholder('-'),
                    TextEntry::make('shipped_at')->label('Dikirim Pada')->dateTime('d M Y H:i')->placeholder('-'),
                    TextEntry::make('completed_at')->label('Selesai Pada')->dateTime('d M Y H:i')->placeholder('-'),
                ])
                ->columns(2),
            Section::make('Pelanggan')
                ->schema([
                    TextEntry::make('customer_name')->label('Nama')->state(fn (Order $record): string => $record->user?->name ?? $record->guest_name ?? '-'),
                    TextEntry::make('customer_email')->label('Email')->state(fn (Order $record): string => $record->user?->email ?? $record->guest_email ?? '-'),
                    TextEntry::make('customer_phone')->label('Telepon')->state(fn (Order $record): string => $record->user?->phone ?? $record->guest_phone ?? '-'),
                    TextEntry::make('customer_type')->label('Tipe')->state(fn (Order $record): string => $record->user_id ? 'Member' : 'Guest')->badge(),
                ])
                ->columns(2),
            Section::make('Alamat Pengiriman')
                ->schema([
                    TextEntry::make('shipping_address.recipient_name')->label('Penerima'),
                    TextEntry::make('shipping_address.phone')->label('Telepon'),
                    TextEntry::make('shipping_address.full_address')->label('Alamat Lengkap')->columnSpanFull(),
                    TextEntry::make('shipping_address.district')->label('Kecamatan'),
                    TextEntry::make('shipping_address.city')->label('Kota/Kabupaten'),
                    TextEntry::make('shipping_address.province')->label('Provinsi'),
                    TextEntry::make('shipping_address.postal_code')->label('Kode Pos'),
                ])
                ->columns(2),
            Section::make('Item Pesanan')
                ->schema([
                    RepeatableEntry::make('items')
                        ->label('')
                        ->schema([
                            TextEntry::make('product_name')->label('Produk'),
                            TextEntry::make('variant_name')->label('Varian'),
                            TextEntry::make('qty')->label('Qty'),
                            TextEntry::make('price')->label('Harga')->money('IDR'),
                            TextEntry::make('subtotal')->label('Subtotal')->money('IDR'),
                        ])
                        ->columns(5),
                    TextEntry::make('subtotal')->label('Subtotal Pesanan')->money('IDR'),
                    TextEntry::make('shipping_cost')->label('Ongkos Kirim')->money('IDR'),
                    TextEntry::make('total')->label('Total')->money('IDR')->weight('bold'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }
}
```

- [x] **Step 4: Create `ListOrders.php`**

Create `api/app/Filament/Resources/OrderResource/Pages/ListOrders.php`:

```php
<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;
}
```

- [x] **Step 5: Create `ViewOrder.php`**

Create `api/app/Filament/Resources/OrderResource/Pages/ViewOrder.php`:

```php
<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;
}
```

- [x] **Step 6: Run test to verify it passes**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=OrderResourcePanelTest`
Expected: PASS (3 tests).

- [x] **Step 7: Commit**

```bash
git add api/app/Filament/Resources/OrderResource.php api/app/Filament/Resources/OrderResource/Pages/ListOrders.php api/app/Filament/Resources/OrderResource/Pages/ViewOrder.php api/tests/Feature/Order/OrderResourcePanelTest.php
git commit -m "feat(api): add read-only OrderResource (list + view) for admin"
```

---

### Task 3: "Update Status" action on the View page

**Files:**
- Modify: `api/app/Filament/Resources/OrderResource/Pages/ViewOrder.php`
- Test: `api/tests/Feature/Order/OrderUpdateStatusActionTest.php`

**Interfaces:**
- Consumes: `App\Filament\Resources\OrderResource::statusOptions(): array` (Task 2), `App\Models\Order::releaseStockIfApplicable(string $oldStatus, string $newStatus): void` (Task 1).

- [x] **Step 1: Write the failing test**

Create `api/tests/Feature/Order/OrderUpdateStatusActionTest.php`:

```php
<?php

namespace Tests\Feature\Order;

use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderUpdateStatusActionTest extends TestCase
{
    use RefreshDatabase;

    private function createOrderWithItem(string $status, int $stock = 8, int $qty = 2): array
    {
        $category = Category::create(['name' => 'Skincare', 'slug' => 'skincare']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sunscreen',
            'slug' => 'sunscreen',
            'description' => 'Test',
            'images' => ['sunscreen.png'],
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '50ml',
            'sku' => 'SUN-50',
            'price' => 100000,
            'stock' => $stock,
            'weight' => 200,
        ]);
        $order = Order::create([
            'order_number' => 'ORD-ACTION-TEST',
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => $status,
        ]);
        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'price' => 100000,
            'qty' => $qty,
            'subtotal' => 200000,
        ]);

        return [$order, $variant];
    }

    public function test_admin_can_update_status_to_shipped_with_tracking_number(): void
    {
        $admin = Admin::factory()->create();
        [$order] = $this->createOrderWithItem('paid');

        Livewire::actingAs($admin, 'admin')
            ->test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('updateStatus', data: [
                'status' => 'shipped',
                'tracking_number' => 'JNE123456',
            ])
            ->assertHasNoActionErrors();

        $fresh = $order->fresh();
        $this->assertEquals('shipped', $fresh->status);
        $this->assertEquals('JNE123456', $fresh->tracking_number);
        $this->assertNotNull($fresh->shipped_at);
    }

    public function test_releases_stock_when_cancelling_a_pending_order(): void
    {
        $admin = Admin::factory()->create();
        [$order, $variant] = $this->createOrderWithItem('pending', stock: 8, qty: 2);

        Livewire::actingAs($admin, 'admin')
            ->test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('updateStatus', data: ['status' => 'cancelled']);

        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals(10, $variant->fresh()->stock);
    }

    public function test_does_not_release_stock_when_cancelling_an_already_paid_order(): void
    {
        $admin = Admin::factory()->create();
        [$order, $variant] = $this->createOrderWithItem('paid', stock: 8, qty: 2);

        Livewire::actingAs($admin, 'admin')
            ->test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('updateStatus', data: ['status' => 'cancelled']);

        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals(8, $variant->fresh()->stock);
    }
}
```

- [x] **Step 2: Run test to verify it fails**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=OrderUpdateStatusActionTest`
Expected: FAIL — `callAction('updateStatus', ...)` finds no action with that name (the action doesn't exist on `ViewOrder` yet).

- [x] **Step 3: Add the action**

Replace the full contents of `api/app/Filament/Resources/OrderResource/Pages/ViewOrder.php` with:

```php
<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateStatus')
                ->label('Update Status')
                ->form([
                    Select::make('status')
                        ->label('Status')
                        ->options(OrderResource::statusOptions())
                        ->default(fn (Order $record): string => $record->status)
                        ->live()
                        ->required(),
                    TextInput::make('tracking_number')
                        ->label('Nomor Resi')
                        ->visible(fn (Get $get): bool => $get('status') === 'shipped')
                        ->required(fn (Get $get): bool => $get('status') === 'shipped'),
                ])
                ->action(function (array $data, Order $record): void {
                    $oldStatus = $record->status;
                    $record->status = $data['status'];

                    if ($data['status'] === 'shipped' && ! empty($data['tracking_number'])) {
                        $record->tracking_number = $data['tracking_number'];
                    }

                    if ($data['status'] === 'shipped' && $record->shipped_at === null) {
                        $record->shipped_at = now();
                    }

                    if ($data['status'] === 'completed' && $record->completed_at === null) {
                        $record->completed_at = now();
                    }

                    $record->save();
                    $record->releaseStockIfApplicable($oldStatus, $data['status']);

                    Notification::make()
                        ->title('Status pesanan diperbarui')
                        ->success()
                        ->send();
                }),
        ];
    }
}
```

- [x] **Step 4: Run test to verify it passes**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=OrderUpdateStatusActionTest`
Expected: PASS (3 tests).

- [x] **Step 5: Run the full suite**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test`
Expected: PASS, no regressions (pre-existing unrelated `SettingsApiTest::test_can_fetch_public_store_settings` failure, if still present, is not caused by this change).

- [x] **Step 6: Commit**

```bash
git add api/app/Filament/Resources/OrderResource/Pages/ViewOrder.php api/tests/Feature/Order/OrderUpdateStatusActionTest.php
git commit -m "feat(api): add Update Status action to admin OrderResource"
```

---

## Verification Plan

### Automated Tests
Run inside `api/`:
```bash
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test
```
Covers: `OrderReleaseStockTest` (4), `OrderResourcePanelTest` (3), `OrderUpdateStatusActionTest` (3), plus existing `WebhookControllerTest` (2) as a regression check on the Task 1 refactor.

### Manual Verification
1. Log in to `/admin` as an admin user.
2. Navigate to "Pesanan" in the nav — confirm the order list shows existing orders with status badges and correct customer/total/courier columns.
3. Click into an order — confirm all sections render (Informasi Pesanan, Pelanggan, Alamat Pengiriman, Item Pesanan) with correct snapshot data, and that there is no way to edit any of those fields directly.
4. Click "Update Status", select "Shipped" — confirm the "Nomor Resi" field appears and is required; submit with a tracking number — confirm the order's status/tracking number/shipped_at update and a success notification appears.
5. On a `pending` order, set status to "Cancelled" — confirm the related product variant's stock increases by the order item's qty.
6. On a `paid` order, set status to "Cancelled" — confirm stock does NOT change (this is the intentional guard from spec §4).
