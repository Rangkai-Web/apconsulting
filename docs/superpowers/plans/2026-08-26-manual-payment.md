# Manual Payment (Ready Stock) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the (never-configured) Midtrans Snap integration with a manual bank-transfer payment flow: customer uploads a transfer proof, admin verifies it in the Filament panel, WhatsApp/LINE are link-out confirmation channels only.

**Architecture:** Split the single `orders.status` column into `order_status` (fulfillment) and `payment_status` (money) so payment verification and fulfillment progress can move independently — this also means the PO sub-project (later) can extend `order_status` without touching payment logic. A new `payment_methods` table (modeled exactly on the existing `banners` table/resource pair) lets admin manage bank/e-wallet/QRIS entries without code changes. Midtrans code is deleted outright, not feature-flagged — it was never live for this store.

**Tech Stack:** Laravel 13 + Filament v5 (`api/`), Nuxt 4 SPA + Pinia (`storefront/`), PHPUnit (backend tests), Vitest (frontend unit tests, storefront only tests pure logic — no component tests exist in this codebase, verify UI pages manually in the browser per project convention).

**Spec:** `docs/superpowers/specs/2026-08-26-manual-payment-design.md`

## Global Constraints

- File uploads (payment proof, QRIS images): jpg/png/webp only, max 2MB — same convention as product images and banners (`CLAUDE.md`).
- Never call the RajaOngkir `/cost` endpoint outside an explicit user "Cek Ongkir" action — unaffected by this plan, but no task here should touch that flow.
- Snapshot fields on `order_items` (`product_name`/`variant_name`/`price`) are never replaced with live joins — unaffected, not touched by this plan.
- Every task that adds/changes a `/api/...` endpoint must update the matching `docs/api/*.md` file.
- Run backend tests with: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=<TestClass>` per task; full suite (`php artisan test`) only in the final task, per user's explicit request to avoid burning tokens on repeated full-suite runs mid-plan.
- Run frontend unit tests with: `cd storefront && npx vitest run <path>`.

---

## Task 1: Rename `orders.status` → `order_status`, add payment fields, drop Midtrans columns

**Files:**
- Create: `api/database/migrations/2026_08_27_000001_split_order_status_and_add_payment_fields.php`
- Modify: `api/app/Models/Order.php` (Fillable list)
- Modify: `api/database/factories/OrderFactory.php`
- Modify: `api/app/Http/Resources/Api/OrderResource.php`
- Modify: `api/app/Filament/Resources/OrderResource.php` (table column + infolist entry, `statusOptions()`/`statusColor()` drop `'paid'`)
- Modify: `api/tests/Feature/Order/OrderSchemaTest.php`
- Modify: `api/tests/Feature/Order/OrderReleaseStockTest.php`
- Modify: `api/tests/Feature/Order/OrderResourcePanelTest.php`
- Modify: `api/tests/Feature/Order/OrderApiTest.php`
- Modify: `api/tests/Feature/Order/OrderUpdateStatusActionTest.php`
- Modify: `api/tests/Feature/Order/WebhookControllerTest.php`
- Modify: `api/tests/Feature/Order/OrderPaidEmailNotificationTest.php`

**Interfaces:**
- Produces: `orders.order_status` (string, values used in this sub-project: `pending, processing, shipped, completed, cancelled, expired`), `orders.payment_status` (string, default `unpaid`, values: `unpaid, awaiting_verification, paid, rejected, refunded`), `orders.payment_proof` (string, nullable), `orders.payment_proof_uploaded_at` (timestamp, nullable), `orders.payment_rejected_reason` (string, nullable). `Order::releaseStockIfApplicable(string $oldStatus, string $newStatus): void` unchanged signature, still called with `order_status` values.

This is a pure rename+add — no new behavior yet, so there's no new test to write first. Instead: write the migration, then fix every existing reference so the full `Order`-related suite goes green again.

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('status', 'order_status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('order_status');
            $table->string('payment_proof')->nullable()->after('shipping_cost');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof');
            $table->string('payment_rejected_reason')->nullable()->after('payment_proof_uploaded_at');
            $table->dropColumn(['midtrans_order_id', 'midtrans_transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_proof', 'payment_proof_uploaded_at', 'payment_rejected_reason']);
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('order_status', 'status');
        });
    }
};
```

- [ ] **Step 2: Run the migration**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan migrate`
Expected: `split_order_status_and_add_payment_fields ... DONE`

- [ ] **Step 3: Update `Order` model's Fillable list**

In `api/app/Models/Order.php`, replace the `#[Fillable([...])]` attribute list:

```php
#[Fillable([
    'order_number', 'user_id', 'guest_name', 'guest_email', 'guest_phone',
    'shipping_address', 'courier', 'courier_service', 'shipping_cost',
    'subtotal', 'total', 'order_status', 'payment_status',
    'payment_proof', 'payment_proof_uploaded_at', 'payment_rejected_reason',
    'tracking_number', 'shipped_at', 'completed_at',
])]
```

(Dropped `'status'`, `'midtrans_order_id'`, `'midtrans_transaction_id'`. Added the four new payment fields.)

- [ ] **Step 4: Update `OrderFactory`**

In `api/database/factories/OrderFactory.php`, change:
```php
'status' => 'pending',
```
to:
```php
'order_status' => 'pending',
```

- [ ] **Step 5: Fix `OrderSchemaTest.php`**

Change `test_creates_an_order_with_a_default_pending_status`:
```php
public function test_creates_an_order_with_a_default_pending_status(): void
{
    $order = Order::factory()->create();

    $this->assertEquals('pending', $order->order_status);
    $this->assertEquals('unpaid', $order->payment_status);
}
```

- [ ] **Step 6: Fix `OrderReleaseStockTest.php`**

Open the file, replace every `'status' => $status` in `Order::create([...])` fixtures with `'order_status' => $status`. Replace every `$order->status` / `$order->fresh()->status` assertion with `$order->order_status` / `$order->fresh()->order_status`.

- [ ] **Step 7: Fix `OrderResourcePanelTest.php`**

Replace all three occurrences of `'status' => 'pending',` in `Order::create([...])` with `'order_status' => 'pending',`.

- [ ] **Step 8: Fix `OrderApiTest.php`**

In `test_member_can_checkout_successfully` and `test_guest_can_checkout_successfully`, change:
```php
$this->assertDatabaseHas('orders', [
    'user_id' => $user->id,
    'total' => 215000,
    'status' => 'pending',
]);
```
to:
```php
$this->assertDatabaseHas('orders', [
    'user_id' => $user->id,
    'total' => 215000,
    'order_status' => 'pending',
    'payment_status' => 'unpaid',
]);
```
(same for the guest test, drop the `'status'` key there too if present). In `test_user_can_lookup_guest_order`, change `Order::create([... 'status' => 'pending'])` to `'order_status' => 'pending'`. Leave the `snap_token` assertions untouched — that's Task 5's job.

- [ ] **Step 9: Fix `OrderUpdateStatusActionTest.php`**

In `createOrderWithItem()`, change `'status' => $status` to `'order_status' => $status`. In every test method, change `$order->fresh()->status` / `$fresh->status` to `$order->fresh()->order_status` / `$fresh->order_status`. Leave the `callAction('updateStatus', data: ['status' => 'shipped', ...])` calls unchanged — the form field is still named `status` internally (Task 6 touches the action body, not this test's calling convention).

- [ ] **Step 10: Fix `WebhookControllerTest.php`**

Replace `'status' => 'pending',` in `createOrderAndItems()` with `'order_status' => 'pending',`. Replace `$order->fresh()->status` in both test methods with `$order->fresh()->order_status`. (This whole file is deleted in Task 5 — this fix just keeps the suite green until then.)

- [ ] **Step 11: Fix `OrderPaidEmailNotificationTest.php`**

Read the file first (`api/tests/Feature/Order/OrderPaidEmailNotificationTest.php`) — replace every `'status' => $status` fixture key with `'order_status' => $status`, and every `->status` assertion on an `Order` with `->order_status`. (This file's *trigger mechanism* — the Midtrans webhook — is deleted in Task 5 and its coverage is rebuilt in Task 6's `AdminPaymentVerificationTest.php`; this step only keeps it compiling and green in the meantime.)

- [ ] **Step 12: Update the API `OrderResource`**

In `api/app/Http/Resources/Api/OrderResource.php`, change:
```php
'status' => $this->status,
```
to:
```php
'status' => $this->order_status,
```
(JSON key stays `status` for now — Task 4 renames the JSON key to `order_status` and adds the new payment fields, to keep this task's diff to the rename only.)

- [ ] **Step 13: Update the Filament `OrderResource`**

In `api/app/Filament/Resources/OrderResource.php`:
- `statusOptions()`: remove the `'paid' => 'Paid',` line.
- `statusColor()`: remove the `'paid' => 'info',` line.
- Table: change `TextColumn::make('status')` to `TextColumn::make('order_status')`.
- Infolist: change `TextEntry::make('status')->label('Status')` to `TextEntry::make('order_status')->label('Status')`.

- [ ] **Step 14: Run the full Order-related test suite**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=Order`
Expected: PASS (all `Order*` test classes green — `OrderApiTest`, `OrderSchemaTest`, `OrderReleaseStockTest`, `OrderResourcePanelTest`, `OrderUpdateStatusActionTest`, `WebhookControllerTest`, `OrderPaidEmailNotificationTest`, `ExpirePendingOrdersCommandTest`).

If `ExpirePendingOrdersCommandTest` fails, open `api/app/Console/Commands/ExpirePendingOrders.php` and change `Order::where('status', 'pending')` to `Order::where('order_status', 'pending')`, and in the command body `$order->status = 'expired'` to `$order->order_status = 'expired'`. Also fix `api/tests/Feature/Order/ExpirePendingOrdersCommandTest.php`'s fixtures (`'status' =>` → `'order_status' =>`) the same way as Step 6. (This is a mechanical instance of the same rename that Task 1 must not skip, even though the *logic* of this command is finished in Task 5.)

- [ ] **Step 15: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment"
git add api/database/migrations/2026_08_27_000001_split_order_status_and_add_payment_fields.php \
  api/app/Models/Order.php api/database/factories/OrderFactory.php \
  api/app/Http/Resources/Api/OrderResource.php api/app/Filament/Resources/OrderResource.php \
  api/app/Console/Commands/ExpirePendingOrders.php \
  api/tests/Feature/Order/
git commit -m "refactor(orders): split status into order_status + payment_status"
```

---

## Task 2: Payment methods (admin CRUD + public API)

**Files:**
- Create: `api/database/migrations/2026_08_27_000002_create_payment_methods_table.php`
- Create: `api/app/Models/PaymentMethod.php`
- Create: `api/app/Filament/Resources/PaymentMethodResource.php`
- Create: `api/app/Filament/Resources/PaymentMethodResource/Pages/ListPaymentMethods.php`
- Create: `api/app/Filament/Resources/PaymentMethodResource/Pages/CreatePaymentMethod.php`
- Create: `api/app/Filament/Resources/PaymentMethodResource/Pages/EditPaymentMethod.php`
- Create: `api/app/Http/Controllers/Api/PaymentMethodController.php`
- Create: `api/app/Http/Resources/Api/PaymentMethodResource.php`
- Modify: `api/routes/api.php`
- Create: `docs/api/payment-methods.md`
- Test: `api/tests/Feature/Settings/PaymentMethodApiTest.php`

**Interfaces:**
- Produces: `GET /api/payment-methods` → `{ "data": [{ "id", "label", "account_number", "account_holder", "qr_image" (absolute URL or null), "instructions", "sort_order" }] }`, active-only, ordered by `sort_order`.

- [ ] **Step 1: Write the failing API test**

```php
<?php

namespace Tests\Feature\Settings;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_active_payment_methods_sorted_by_sort_order(): void
    {
        PaymentMethod::create([
            'label' => 'ShopeePay',
            'account_number' => '081229475305',
            'account_holder' => 'yuliesarida',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        PaymentMethod::create([
            'label' => 'BCA',
            'account_number' => '8950505266',
            'account_holder' => 'Yulie Sarida H T',
            'qr_image' => 'payment-methods/bca-qr.png',
            'instructions' => 'Transfer sesuai nominal tagihan.',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        PaymentMethod::create([
            'label' => 'Rekening Lama',
            'account_number' => '0000000000',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/payment-methods');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.label', 'BCA')
            ->assertJsonPath('data.0.qr_image', url('uploads/payment-methods/bca-qr.png'))
            ->assertJsonPath('data.1.label', 'ShopeePay')
            ->assertJsonPath('data.1.qr_image', null);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=PaymentMethodApiTest`
Expected: FAIL (class `App\Models\PaymentMethod` not found / route not found)

- [ ] **Step 3: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('qr_image')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
```

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan migrate`

- [ ] **Step 4: Create the model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'label',
    'account_number',
    'account_holder',
    'qr_image',
    'instructions',
    'sort_order',
    'is_active',
])]
class PaymentMethod extends Model
{
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
```

- [ ] **Step 5: Create the API resource**

```php
<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'account_number' => $this->account_number,
            'account_holder' => $this->account_holder,
            'qr_image' => $this->qr_image ? Storage::disk('public_uploads')->url($this->qr_image) : null,
            'instructions' => $this->instructions,
            'sort_order' => $this->sort_order,
        ];
    }
}
```

- [ ] **Step 6: Create the controller**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PaymentMethodResource;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return PaymentMethodResource::collection($methods);
    }
}
```

- [ ] **Step 7: Add the route**

In `api/routes/api.php`, add near the `/banners` route:
```php
use App\Http\Controllers\Api\PaymentMethodController;

// Public payment methods route
Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
```

- [ ] **Step 8: Run test to verify it passes**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=PaymentMethodApiTest`
Expected: PASS

- [ ] **Step 9: Build the Filament admin resource**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages\CreatePaymentMethod;
use App\Filament\Resources\PaymentMethodResource\Pages\EditPaymentMethod;
use App\Filament\Resources\PaymentMethodResource\Pages\ListPaymentMethods;
use App\Models\PaymentMethod;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Metode Pembayaran';

    protected static ?string $modelLabel = 'Metode Pembayaran';

    protected static ?string $pluralModelLabel = 'Metode Pembayaran';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('label')
                ->label('Nama Metode')
                ->placeholder('Contoh: BCA, ShopeePay, QRIS')
                ->required()
                ->maxLength(255),
            TextInput::make('account_number')
                ->label('Nomor Rekening / Nomor Akun')
                ->maxLength(255),
            TextInput::make('account_holder')
                ->label('Atas Nama')
                ->maxLength(255),
            FileUpload::make('qr_image')
                ->label('Gambar QR (Opsional)')
                ->disk('public_uploads')
                ->directory('payment-methods')
                ->image()
                ->maxSize(2048)
                ->helperText('Format: JPG, PNG, WEBP. Maksimal 2MB.'),
            Textarea::make('instructions')
                ->label('Instruksi Tambahan')
                ->rows(3),
            TextInput::make('sort_order')
                ->label('Urutan Tampilan')
                ->numeric()
                ->default(0)
                ->required(),
            Toggle::make('is_active')
                ->label('Status Aktif')
                ->default(true)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('qr_image')
                    ->label('QR')
                    ->disk('public_uploads')
                    ->square(),
                TextColumn::make('label')
                    ->label('Nama Metode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_number')
                    ->label('Nomor Rekening/Akun'),
                TextColumn::make('account_holder')
                    ->label('Atas Nama'),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentMethods::route('/'),
            'create' => CreatePaymentMethod::route('/create'),
            'edit' => EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 10: Create the three page classes**

```php
<?php
// api/app/Filament/Resources/PaymentMethodResource/Pages/ListPaymentMethods.php
namespace App\Filament\Resources\PaymentMethodResource\Pages;

use App\Filament\Resources\PaymentMethodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaymentMethods extends ListRecords
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
```

```php
<?php
// api/app/Filament/Resources/PaymentMethodResource/Pages/CreatePaymentMethod.php
namespace App\Filament\Resources\PaymentMethodResource\Pages;

use App\Filament\Resources\PaymentMethodResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentMethod extends CreateRecord
{
    protected static string $resource = PaymentMethodResource::class;

    public function canCreateAnother(): bool
    {
        return false;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

```php
<?php
// api/app/Filament/Resources/PaymentMethodResource/Pages/EditPaymentMethod.php
namespace App\Filament\Resources\PaymentMethodResource\Pages;

use App\Filament\Resources\PaymentMethodResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPaymentMethod extends EditRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

(This mirrors `BannerResource`'s three page classes exactly — verified against `api/app/Filament/Resources/BannerResource/Pages/*.php`.)

- [ ] **Step 11: Write the API doc**

Create `docs/api/payment-methods.md` following `docs/api/banners.md`'s shape:

```markdown
# Payment Methods API

## GET /api/payment-methods

Fetch active payment methods (bank/e-wallet/QRIS) for storefront checkout and order-detail display, ordered by `sort_order`. Managed in the admin panel via `PaymentMethodResource`.

**Auth:** none

**Response 200**
```json
{
  "data": [
    {
      "id": 1,
      "label": "BCA",
      "account_number": "8950505266",
      "account_holder": "Yulie Sarida H T",
      "qr_image": null,
      "instructions": "Transfer sesuai nominal tagihan.",
      "sort_order": 1
    }
  ]
}
```

| Field | Type | Notes |
|---|---|---|
| `label` | string | Method name shown to customer, e.g. "BCA", "ShopeePay", "QRIS" |
| `account_number` | string\|null | Bank account / e-wallet phone number |
| `account_holder` | string\|null | Account holder name |
| `qr_image` | string\|null | Absolute URL to a QR code image, or `null` |
| `instructions` | string\|null | Free-text extra instructions |
| `sort_order` | int | Display order hint; endpoint itself orders by `sort_order` ascending |

Only methods with `is_active = true` are returned.
```

- [ ] **Step 12: Run full relevant test suite**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=PaymentMethodApiTest`
Expected: PASS

- [ ] **Step 13: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment"
git add api/database/migrations/2026_08_27_000002_create_payment_methods_table.php \
  api/app/Models/PaymentMethod.php api/app/Filament/Resources/PaymentMethodResource.php \
  api/app/Filament/Resources/PaymentMethodResource/ \
  api/app/Http/Controllers/Api/PaymentMethodController.php \
  api/app/Http/Resources/Api/PaymentMethodResource.php \
  api/routes/api.php docs/api/payment-methods.md \
  api/tests/Feature/Settings/PaymentMethodApiTest.php
git commit -m "feat(payment-methods): add admin CRUD and public listing API"
```

---

## Task 3: `store_settings.line_url`

**Files:**
- Create: `api/database/migrations/2026_08_27_000003_add_line_url_to_store_settings_table.php`
- Modify: `api/app/Models/StoreSetting.php`
- Modify: `api/app/Http/Resources/Api/SettingsResource.php`
- Modify: `api/app/Filament/Pages/SettingsPage.php`
- Modify: `api/tests/Feature/Settings/SettingsApiTest.php`

**Interfaces:**
- Produces: `store_settings.line_url` (string, nullable), exposed as `line_url` in `GET /api/settings`.

- [ ] **Step 1: Write the failing test**

In `api/tests/Feature/Settings/SettingsApiTest.php`, update `test_can_fetch_public_store_settings`: add `'line_url' => 'https://line.me/ti/p/@bbystargo',` to the `StoreSetting::create([...])` fixture, and add `'line_url' => 'https://line.me/ti/p/@bbystargo',` to the `assertJson(['data' => [...]])` block.

- [ ] **Step 2: Run test to verify it fails**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=SettingsApiTest`
Expected: FAIL (`line_url` not a fillable/known column, or missing from JSON)

- [ ] **Step 3: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('line_url')->nullable()->after('whatsapp_number');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn('line_url');
        });
    }
};
```

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan migrate`

- [ ] **Step 4: Add `line_url` to `StoreSetting`'s Fillable list**

In `api/app/Models/StoreSetting.php`, add `'line_url',` after `'whatsapp_number',` in the `#[Fillable([...])]` list.

- [ ] **Step 5: Add `line_url` to `SettingsResource`**

In `api/app/Http/Resources/Api/SettingsResource.php`, add `'line_url' => $this->line_url,` after `'whatsapp_number' => $this->whatsapp_number,`.

- [ ] **Step 6: Add the admin form field**

In `api/app/Filament/Pages/SettingsPage.php`, after the `TextInput::make('whatsapp_number')` block, add:
```php
TextInput::make('line_url')
    ->label('Link LINE OA')
    ->url()
    ->placeholder('https://line.me/ti/p/@namaakun')
    ->maxLength(255)
    ->helperText('Link resmi LINE Official Account toko, dipakai sebagai tombol konfirmasi pembayaran.'),
```

- [ ] **Step 7: Run test to verify it passes**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=SettingsApiTest`
Expected: PASS

- [ ] **Step 8: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment"
git add api/database/migrations/2026_08_27_000003_add_line_url_to_store_settings_table.php \
  api/app/Models/StoreSetting.php api/app/Http/Resources/Api/SettingsResource.php \
  api/app/Filament/Pages/SettingsPage.php api/tests/Feature/Settings/SettingsApiTest.php
git commit -m "feat(settings): add LINE OA link for payment confirmation"
```

---

## Task 4: Payment proof upload endpoint + order API payment fields

**Files:**
- Modify: `api/app/Http/Controllers/Api/OrderController.php`
- Modify: `api/app/Http/Resources/Api/OrderResource.php`
- Modify: `api/routes/api.php`
- Test: `api/tests/Feature/Order/PaymentProofUploadTest.php`

**Interfaces:**
- Consumes: `Order::$fillable` from Task 1 (`payment_status`, `payment_proof`, `payment_proof_uploaded_at`, `payment_rejected_reason`).
- Produces: `POST /api/orders/{order_number}/payment-proof` (multipart, field `proof`, optional `guest_email` for guest orders) → `{ "data": <OrderResource> }`. `OrderResource` (API) response gains keys `order_status`, `payment_status`, `payment_proof` (absolute URL or null), `payment_rejected_reason`; the old `status` key is removed (breaking change — Task 7 updates the frontend to match in the same plan run).

- [ ] **Step 1: Write the failing tests**

```php
<?php

namespace Tests\Feature\Order;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PaymentProofUploadTest extends TestCase
{
    use RefreshDatabase;

    private function createOrder(?int $userId, ?string $guestEmail, string $paymentStatus = 'unpaid'): Order
    {
        $category = Category::create(['name' => 'Umum', 'slug' => 'umum']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Test',
            'slug' => 'produk-test',
            'description' => 'Test',
            'images' => ['produk.png'],
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Default',
            'sku' => 'PROD-1',
            'price' => 100000,
            'stock' => 10,
            'weight' => 200,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-PROOF-' . uniqid(),
            'user_id' => $userId,
            'guest_name' => $userId ? null : 'Guest',
            'guest_email' => $userId ? null : $guestEmail,
            'guest_phone' => $userId ? null : '0899',
            'shipping_address' => ['recipient_name' => 'Test'],
            'subtotal' => 100000,
            'total' => 100000,
            'order_status' => 'pending',
            'payment_status' => $paymentStatus,
        ]);
        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'price' => 100000,
            'qty' => 1,
            'subtotal' => 100000,
        ]);

        return $order;
    }

    public function test_member_can_upload_payment_proof_on_own_order(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user->id, null);

        $response = $this->actingAs($user, 'sanctum')->postJson(
            "/api/orders/{$order->order_number}/payment-proof",
            ['proof' => UploadedFile::fake()->image('bukti.jpg')->size(500)],
        );

        $response->assertStatus(200)
            ->assertJsonPath('data.payment_status', 'awaiting_verification');

        $fresh = $order->fresh();
        $this->assertEquals('awaiting_verification', $fresh->payment_status);
        $this->assertNotNull($fresh->payment_proof);
        $this->assertNotNull($fresh->payment_proof_uploaded_at);
    }

    public function test_guest_can_upload_with_matching_email(): void
    {
        $order = $this->createOrder(null, 'guest@example.com');

        $response = $this->postJson(
            "/api/orders/{$order->order_number}/payment-proof",
            [
                'proof' => UploadedFile::fake()->image('bukti.jpg')->size(500),
                'guest_email' => 'guest@example.com',
            ],
        );

        $response->assertStatus(200);
        $this->assertEquals('awaiting_verification', $order->fresh()->payment_status);
    }

    public function test_guest_upload_with_wrong_email_is_forbidden(): void
    {
        $order = $this->createOrder(null, 'guest@example.com');

        $response = $this->postJson(
            "/api/orders/{$order->order_number}/payment-proof",
            [
                'proof' => UploadedFile::fake()->image('bukti.jpg')->size(500),
                'guest_email' => 'wrong@example.com',
            ],
        );

        $response->assertStatus(403);
        $this->assertEquals('unpaid', $order->fresh()->payment_status);
    }

    public function test_member_cannot_upload_on_someone_elses_order(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $order = $this->createOrder($owner->id, null);

        $response = $this->actingAs($intruder, 'sanctum')->postJson(
            "/api/orders/{$order->order_number}/payment-proof",
            ['proof' => UploadedFile::fake()->image('bukti.jpg')->size(500)],
        );

        $response->assertStatus(403);
    }

    public function test_cannot_upload_when_already_awaiting_verification(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user->id, null, 'awaiting_verification');

        $response = $this->actingAs($user, 'sanctum')->postJson(
            "/api/orders/{$order->order_number}/payment-proof",
            ['proof' => UploadedFile::fake()->image('bukti.jpg')->size(500)],
        );

        $response->assertStatus(422);
    }

    public function test_can_reupload_after_rejection(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user->id, null, 'rejected');
        $order->update(['payment_rejected_reason' => 'Nominal tidak sesuai']);

        $response = $this->actingAs($user, 'sanctum')->postJson(
            "/api/orders/{$order->order_number}/payment-proof",
            ['proof' => UploadedFile::fake()->image('bukti.jpg')->size(500)],
        );

        $response->assertStatus(200);
        $fresh = $order->fresh();
        $this->assertEquals('awaiting_verification', $fresh->payment_status);
        $this->assertNull($fresh->payment_rejected_reason);
    }

    public function test_rejects_non_image_file(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user->id, null);

        $response = $this->actingAs($user, 'sanctum')->postJson(
            "/api/orders/{$order->order_number}/payment-proof",
            ['proof' => UploadedFile::fake()->create('bukti.pdf', 500, 'application/pdf')],
        );

        $response->assertStatus(422)->assertJsonValidationErrors(['proof']);
    }

    public function test_rejects_oversized_file(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user->id, null);

        $response = $this->actingAs($user, 'sanctum')->postJson(
            "/api/orders/{$order->order_number}/payment-proof",
            ['proof' => UploadedFile::fake()->image('bukti.jpg')->size(3000)],
        );

        $response->assertStatus(422)->assertJsonValidationErrors(['proof']);
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=PaymentProofUploadTest`
Expected: FAIL (404 — route doesn't exist yet)

- [ ] **Step 3: Add `uploadPaymentProof()` to `OrderController`**

Add this method (no new imports needed — `abort_if` is a global helper, and `$request->file('proof')->store(...)` is a method on the uploaded file, not the `Storage` facade):

```php
public function uploadPaymentProof(Request $request, $orderNumber)
{
    $order = Order::where('order_number', $orderNumber)->firstOrFail();

    if ($request->user()) {
        abort_if($order->user_id !== $request->user()->id, 403, 'Unauthorized.');
    } else {
        $validatedEmail = $request->validate(['guest_email' => ['required', 'email']]);
        abort_if($order->guest_email !== $validatedEmail['guest_email'], 403, 'Unauthorized.');
    }

    abort_if(
        ! in_array($order->payment_status, ['unpaid', 'rejected']),
        422,
        'Bukti pembayaran sudah diunggah untuk pesanan ini.',
    );

    $request->validate([
        'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ]);

    $path = $request->file('proof')->store('payment-proofs', 'public_uploads');

    $order->update([
        'payment_proof' => $path,
        'payment_proof_uploaded_at' => now(),
        'payment_status' => 'awaiting_verification',
        'payment_rejected_reason' => null,
    ]);

    return response()->json(['data' => new OrderResource($order)]);
}
```

- [ ] **Step 4: Add the route**

In `api/routes/api.php`, add near the existing public order routes:
```php
Route::post('/orders/{order_number}/payment-proof', [OrderController::class, 'uploadPaymentProof']);
```

- [ ] **Step 5: Update `OrderResource` (API) to expose the payment fields**

Replace the full `toArray()` body in `api/app/Http/Resources/Api/OrderResource.php`:

```php
<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
            'payment_proof' => $this->payment_proof ? Storage::disk('public_uploads')->url($this->payment_proof) : null,
            'payment_rejected_reason' => $this->payment_rejected_reason,
            'courier' => $this->courier,
            'courier_service' => $this->courier_service,
            'shipping_cost' => $this->shipping_cost,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'tracking_number' => $this->tracking_number,
            'shipping_address' => $this->shipping_address,
            'created_at' => $this->created_at,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
```

- [ ] **Step 6: Run tests to verify they pass**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=PaymentProofUploadTest`
Expected: PASS

- [ ] **Step 7: Fix `OrderApiTest.php`'s now-stale `status` key assertions**

The old `status` JSON key is gone. In `api/tests/Feature/Order/OrderApiTest.php`, `test_user_can_lookup_guest_order` currently does `->assertJsonMissingPath('data.midtrans_order_id')` — that's still valid (field genuinely gone). No other test in this file asserts the `status` JSON key directly (they check `assertDatabaseHas`, not the response body), so no further change needed here. Confirm by running:

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=OrderApiTest`
Expected: PASS

- [ ] **Step 8: Update `docs/api/orders.md`**

Add a new section after `## GET /api/orders/lookup`:

```markdown
---

## POST /api/orders/{order_number}/payment-proof

Uploads a transfer proof for a pending manual payment. Sets `payment_status` to `awaiting_verification`.

**Auth:** none for guests (must supply matching `guest_email`); Sanctum member must own the order.

**Request:** `multipart/form-data`

| Field | Type | Notes |
|---|---|---|
| `proof` | file | Required. jpg/png/webp, max 2MB |
| `guest_email` | string | Required only for guest orders — must match the order's `guest_email` |

**Response 200** — `{ "data": <order, same shape as GET /orders/{order_number}> }`

**Response 403** — email/ownership mismatch. **Response 422** — `payment_status` already `awaiting_verification`/`paid`, or invalid file.
```

Leave the rest of `orders.md` untouched — Task 5 rewrites the checkout response shape and removes the webhook section.

- [ ] **Step 9: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment"
git add api/app/Http/Controllers/Api/OrderController.php api/app/Http/Resources/Api/OrderResource.php \
  api/routes/api.php api/tests/Feature/Order/PaymentProofUploadTest.php docs/api/orders.md
git commit -m "feat(orders): payment proof upload endpoint"
```

---

## Task 5: Retire Midtrans

**Files:**
- Delete: `api/app/Http/Controllers/Api/WebhookController.php`
- Delete: `api/config/midtrans.php`
- Delete: `api/tests/Feature/Order/WebhookControllerTest.php`
- Delete: `api/tests/Feature/Order/OrderPaidEmailNotificationTest.php` (coverage rebuilt in Task 6)
- Create: `api/config/orders.php`
- Modify: `api/routes/api.php`
- Modify: `api/app/Http/Controllers/Api/OrderController.php`
- Modify: `api/app/Console/Commands/ExpirePendingOrders.php`
- Modify: `api/tests/Feature/Order/ExpirePendingOrdersCommandTest.php`
- Modify: `api/tests/Feature/Order/OrderApiTest.php`
- Modify: `docs/api/orders.md`
- Modify: `CLAUDE.md`

**Interfaces:**
- Produces: `config('orders.expire_hours')` replaces `config('midtrans.order_expire_hours')`. `POST /api/orders` response becomes `{ "order_number": "..." }` (no `snap_token`).

- [ ] **Step 1: Create `config/orders.php`**

```php
<?php

return [
    'expire_hours' => (int) env('ORDER_EXPIRE_HOURS', 24),
];
```

- [ ] **Step 2: Delete `config/midtrans.php`**

```bash
rm "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/api/config/midtrans.php"
```

- [ ] **Step 3: Delete `WebhookController` and its route**

```bash
rm "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/api/app/Http/Controllers/Api/WebhookController.php"
```

In `api/routes/api.php`, remove the `use App\Http\Controllers\Api\WebhookController;` import and the `Route::post('/webhooks/midtrans', [WebhookController::class, 'midtrans']);` line.

- [ ] **Step 4: Delete `WebhookControllerTest.php` and `OrderPaidEmailNotificationTest.php`**

```bash
rm "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/api/tests/Feature/Order/WebhookControllerTest.php"
rm "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/api/tests/Feature/Order/OrderPaidEmailNotificationTest.php"
```

- [ ] **Step 5: Remove Snap-token code from `OrderController`**

Open `api/app/Http/Controllers/Api/OrderController.php`:
- Delete the `requestSnapToken()` public method entirely.
- Delete the `getMidtransSnapToken()` private method entirely.
- In `store()`, delete the line `$snapToken = $this->getMidtransSnapToken($order);` and change the success response from:
```php
return response()->json([
    'order_number' => $order->order_number,
    'snap_token' => $snapToken,
], 201);
```
to:
```php
return response()->json([
    'order_number' => $order->order_number,
], 201);
```
- Remove the now-unused `use Illuminate\Support\Facades\Http;` import (only `getMidtransSnapToken()` used it — confirm nothing else in the file calls `Http::` before removing).

- [ ] **Step 6: Remove the snap-token route**

In `api/routes/api.php`, remove:
```php
Route::post('/orders/{order_number}/snap-token', [OrderController::class, 'requestSnapToken']);
```

- [ ] **Step 7: Update `ExpirePendingOrders`**

Replace the full command body:

```php
<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire-pending';

    protected $description = 'Expire pending orders whose payment was never completed or verified, and release their reserved stock.';

    public function handle(): void
    {
        $threshold = now()->subHours((int) config('orders.expire_hours'));

        $orders = Order::where('order_status', 'pending')
            ->where('payment_status', '!=', 'awaiting_verification')
            ->where('created_at', '<=', $threshold)
            ->with('items.variant')
            ->get();

        foreach ($orders as $order) {
            $order->order_status = 'expired';
            $order->save();
            $order->releaseStockIfApplicable('pending', 'expired');
        }

        Log::info("orders:expire-pending: expired {$orders->count()} orders.");
    }
}
```

- [ ] **Step 8: Update `ExpirePendingOrdersCommandTest.php`**

Read the file, then:
- Every `Order::create([... 'status' => ...])` fixture → `'order_status' => ...` (should already be done if Task 1's Step 14 fallback was applied — verify).
- Any reference to `config('midtrans.order_expire_hours')` in test setup (e.g. via `Config::set(...)`) → `config('orders.expire_hours')`.
- Add one new test case for the corrected exclusion rule:
```php
public function test_does_not_expire_order_awaiting_payment_verification(): void
{
    $order = Order::factory()->create([
        'order_status' => 'pending',
        'payment_status' => 'awaiting_verification',
        'created_at' => now()->subHours(25),
    ]);

    $this->artisan('orders:expire-pending');

    $this->assertEquals('pending', $order->fresh()->order_status);
}

public function test_expires_a_rejected_and_abandoned_order(): void
{
    $order = Order::factory()->create([
        'order_status' => 'pending',
        'payment_status' => 'rejected',
        'created_at' => now()->subHours(25),
    ]);

    $this->artisan('orders:expire-pending');

    $this->assertEquals('expired', $order->fresh()->order_status);
}
```

- [ ] **Step 9: Fix `OrderApiTest.php`'s `snap_token` assertions**

In `test_member_can_checkout_successfully` and `test_guest_can_checkout_successfully`, change:
```php
$response->assertStatus(201)
    ->assertJsonStructure(['order_number', 'snap_token']);
```
to:
```php
$response->assertStatus(201)
    ->assertJsonStructure(['order_number'])
    ->assertJsonMissing(['snap_token']);
```

- [ ] **Step 10: Run tests**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter="OrderApiTest|ExpirePendingOrdersCommandTest"`
Expected: PASS

- [ ] **Step 11: Update `docs/api/orders.md`**

- Change the `POST /api/orders` **Response 201** example to `{ "order_number": "ORD-20260625-ABCDEF" }` (drop `snap_token`).
- Delete the entire `## POST /api/orders/{order_number}/snap-token` section.
- Delete the entire `## POST /api/webhooks/midtrans` section.
- In the `GET /api/orders` response example, add `"order_status": "pending", "payment_status": "unpaid", "payment_proof": null, "payment_rejected_reason": null,` in place of the old `"status": "pending",` line. Update the "Internal fields ... never exposed" sentence: remove `midtrans_order_id`, `midtrans_transaction_id` (columns no longer exist at all, not just hidden) and add `payment_proof_uploaded_at` (the only new internal-only field — `payment_proof` and `payment_rejected_reason` are intentionally exposed, the customer needs to see their own rejection reason and proof).

- [ ] **Step 12: Update `CLAUDE.md`**

Change:
```
- Payment: Midtrans Snap (popup) only. Shipping: RajaOngkir, single origin warehouse.
```
to:
```
- Payment: manual bank/e-wallet transfer, admin-verified in Filament (see `docs/api/orders.md`, `docs/api/payment-methods.md`). Shipping: RajaOngkir, single origin warehouse.
```

- [ ] **Step 13: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment"
git add -A api/config api/app/Http/Controllers/Api/OrderController.php api/app/Http/Controllers/Api/WebhookController.php \
  api/app/Console/Commands/ExpirePendingOrders.php api/routes/api.php \
  api/tests/Feature/Order/ docs/api/orders.md CLAUDE.md
git commit -m "refactor: remove Midtrans integration entirely"
```

---

## Task 6: Admin payment verification (Filament)

**Files:**
- Modify: `api/app/Filament/Resources/OrderResource.php`
- Modify: `api/app/Filament/Resources/OrderResource/Pages/ViewOrder.php`
- Modify: `api/tests/Feature/Order/OrderUpdateStatusActionTest.php`
- Test: `api/tests/Feature/Order/AdminPaymentVerificationTest.php`

**Interfaces:**
- Consumes: `Order::$order_status`/`$payment_status` (Task 1), `App\Notifications\OrderPaidNotification` (existing, unchanged constructor `__construct(private Order $order)`), `App\Models\Admin` (existing).
- Produces: Filament action `verifyPayment` on `ViewOrder`, callable via `Livewire::test(ViewOrder::class, ...)->callAction('verifyPayment', data: ['decision' => 'accept'|'reject', 'reason' => '...'])`.

- [ ] **Step 1: Write the failing tests**

```php
<?php

namespace Tests\Feature\Order;

use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Notifications\OrderPaidNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPaymentVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function createOrder(string $paymentStatus = 'awaiting_verification'): Order
    {
        $category = Category::create(['name' => 'Umum', 'slug' => 'umum']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Test',
            'slug' => 'produk-test',
            'description' => 'Test',
            'images' => ['produk.png'],
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Default',
            'sku' => 'PROD-1',
            'price' => 100000,
            'stock' => 10,
            'weight' => 200,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-VERIFY-' . uniqid(),
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'subtotal' => 100000,
            'total' => 100000,
            'order_status' => 'pending',
            'payment_status' => $paymentStatus,
            'payment_proof' => 'payment-proofs/bukti.jpg',
        ]);
        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'price' => 100000,
            'qty' => 1,
            'subtotal' => 100000,
        ]);

        return $order;
    }

    public function test_admin_can_accept_payment_and_order_moves_to_processing(): void
    {
        Notification::fake();
        $admin = Admin::factory()->create();
        $order = $this->createOrder();

        Livewire::actingAs($admin, 'admin')
            ->test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('verifyPayment', data: ['decision' => 'accept'])
            ->assertHasNoActionErrors();

        $fresh = $order->fresh();
        $this->assertEquals('paid', $fresh->payment_status);
        $this->assertEquals('processing', $fresh->order_status);
        Notification::assertSentTo($admin, OrderPaidNotification::class);
    }

    public function test_admin_can_reject_payment_with_reason(): void
    {
        $admin = Admin::factory()->create();
        $order = $this->createOrder();

        Livewire::actingAs($admin, 'admin')
            ->test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('verifyPayment', data: ['decision' => 'reject', 'reason' => 'Nominal tidak sesuai'])
            ->assertHasNoActionErrors();

        $fresh = $order->fresh();
        $this->assertEquals('rejected', $fresh->payment_status);
        $this->assertEquals('Nominal tidak sesuai', $fresh->payment_rejected_reason);
        $this->assertEquals('pending', $fresh->order_status);
    }

    public function test_reject_requires_a_reason(): void
    {
        $admin = Admin::factory()->create();
        $order = $this->createOrder();

        Livewire::actingAs($admin, 'admin')
            ->test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('verifyPayment', data: ['decision' => 'reject'])
            ->assertHasActionErrors(['reason' => 'required']);
    }

    public function test_verify_action_hidden_when_not_awaiting_verification(): void
    {
        $admin = Admin::factory()->create();
        $order = $this->createOrder('unpaid');

        Livewire::actingAs($admin, 'admin')
            ->test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionHidden('verifyPayment');
    }

    public function test_cannot_mark_shipped_when_payment_not_paid(): void
    {
        $admin = Admin::factory()->create();
        $order = $this->createOrder('unpaid');

        Livewire::actingAs($admin, 'admin')
            ->test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('updateStatus', data: ['status' => 'shipped', 'tracking_number' => 'JNE123'])
            ->assertHasActionErrors(['status']);

        $this->assertEquals('pending', $order->fresh()->order_status);
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter=AdminPaymentVerificationTest`
Expected: FAIL (action `verifyPayment` doesn't exist)

- [ ] **Step 3: Add `paymentStatusOptions()`/`paymentStatusColor()` and the payment infolist section to `OrderResource.php`**

Add these two static methods near `statusOptions()`:
```php
public static function paymentStatusColor(string $status): string
{
    return match ($status) {
        'unpaid' => 'gray',
        'awaiting_verification' => 'warning',
        'paid' => 'success',
        'rejected' => 'danger',
        'refunded' => 'info',
        default => 'gray',
    };
}

public static function paymentStatusOptions(): array
{
    return [
        'unpaid' => 'Unpaid',
        'awaiting_verification' => 'Menunggu Verifikasi',
        'paid' => 'Paid',
        'rejected' => 'Rejected',
        'refunded' => 'Refunded',
    ];
}
```

In `table()`, add a second badge column right after the `order_status` column:
```php
TextColumn::make('payment_status')
    ->label('Pembayaran')
    ->badge()
    ->color(fn (string $state): string => self::paymentStatusColor($state)),
```

Add a filter alongside the existing `status` `SelectFilter`:
```php
SelectFilter::make('payment_status')
    ->label('Pembayaran')
    ->options(self::paymentStatusOptions()),
```

In `infolist()`, add a new section after "Informasi Pesanan" (needs `use Filament\Infolists\Components\ImageEntry;` added to the imports):
```php
Section::make('Pembayaran')
    ->schema([
        TextEntry::make('payment_status')
            ->label('Status Pembayaran')
            ->badge()
            ->color(fn (string $state): string => self::paymentStatusColor($state)),
        TextEntry::make('payment_rejected_reason')
            ->label('Alasan Ditolak')
            ->placeholder('-'),
        ImageEntry::make('payment_proof')
            ->label('Bukti Transfer')
            ->disk('public_uploads')
            ->visible(fn (Order $record): bool => filled($record->payment_proof)),
    ])
    ->columns(2),
```

- [ ] **Step 4: Add the `verifyPayment` action and guard `updateStatus` in `ViewOrder.php`**

Replace the full file:

```php
<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Admin;
use App\Models\Order;
use App\Notifications\OrderPaidNotification;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Log;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verifyPayment')
                ->label('Verifikasi Pembayaran')
                ->visible(fn (Order $record): bool => $record->payment_status === 'awaiting_verification')
                ->form([
                    Radio::make('decision')
                        ->label('Keputusan')
                        ->options(['accept' => 'Terima', 'reject' => 'Tolak'])
                        ->live()
                        ->required(),
                    Textarea::make('reason')
                        ->label('Alasan Penolakan')
                        ->visible(fn (Get $get): bool => $get('decision') === 'reject')
                        ->required(fn (Get $get): bool => $get('decision') === 'reject'),
                ])
                ->action(function (array $data, Order $record): void {
                    if ($data['decision'] === 'accept') {
                        $record->payment_status = 'paid';
                        if ($record->order_status === 'pending') {
                            $record->order_status = 'processing';
                        }
                        $record->save();

                        $admins = Admin::all();

                        try {
                            if ($admins->count() > 0) {
                                Notification::make()
                                    ->title('Pembayaran Diverifikasi')
                                    ->body("Pesanan {$record->order_number} sebesar Rp " . number_format($record->total) . ' telah diverifikasi dibayar.')
                                    ->success()
                                    ->sendToDatabase($admins);
                            }
                        } catch (\Throwable $e) {
                            Log::error('Gagal mengirim Filament database notification ke admin: ' . $e->getMessage());
                        }

                        try {
                            foreach ($admins as $admin) {
                                $admin->notify(new OrderPaidNotification($record));
                            }
                        } catch (\Throwable $e) {
                            Log::error('Gagal mengirim email notification ke admin: ' . $e->getMessage());
                        }
                    } else {
                        $record->payment_status = 'rejected';
                        $record->payment_rejected_reason = $data['reason'];
                        $record->save();
                    }

                    Notification::make()
                        ->title($data['decision'] === 'accept' ? 'Pembayaran diterima' : 'Pembayaran ditolak')
                        ->success()
                        ->send();
                }),
            Action::make('updateStatus')
                ->label('Update Status')
                ->form([
                    Select::make('status')
                        ->label('Status')
                        ->options(OrderResource::statusOptions())
                        ->default(fn (Order $record): string => $record->order_status)
                        ->live()
                        ->required()
                        ->rule(function (Get $get, Order $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                if ($value === 'shipped' && $record->payment_status !== 'paid') {
                                    $fail('Pesanan belum lunas — verifikasi pembayaran terlebih dahulu sebelum mengubah status ke Shipped.');
                                }
                            };
                        }),
                    TextInput::make('tracking_number')
                        ->label('Nomor Resi')
                        ->visible(fn (Get $get): bool => $get('status') === 'shipped')
                        ->required(fn (Get $get): bool => $get('status') === 'shipped'),
                ])
                ->action(function (array $data, Order $record): void {
                    $oldStatus = $record->order_status;
                    $record->order_status = $data['status'];

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

- [ ] **Step 5: Fix `OrderUpdateStatusActionTest.php`'s now-unrealistic fixtures**

`'paid'` is no longer a real `order_status` value post-Task-1 (it moved to `payment_status`). Give `createOrderWithItem()` a fourth parameter for payment status, defaulting to `'unpaid'`:

```php
private function createOrderWithItem(string $status, int $stock = 8, int $qty = 2, string $paymentStatus = 'unpaid'): array
{
    // ... unchanged category/product/variant setup ...
    $order = Order::create([
        'order_number' => 'ORD-ACTION-TEST',
        'guest_name' => 'Guest',
        'guest_email' => 'guest@example.com',
        'guest_phone' => '0899',
        'shipping_address' => ['recipient_name' => 'Guest'],
        'shipping_cost' => 15000,
        'subtotal' => 200000,
        'total' => 215000,
        'order_status' => $status,
        'payment_status' => $paymentStatus,
    ]);
    // ... unchanged item creation ...
}
```

Then update each call site: `createOrderWithItem('paid')` in `test_admin_can_update_status_to_shipped_with_tracking_number` and `test_requires_tracking_number_when_status_is_shipped` → `createOrderWithItem('processing', paymentStatus: 'paid')` (both transition toward `shipped`, so both need the new guard from Step 4 satisfied). `createOrderWithItem('paid', stock: 8, qty: 2)` in `test_does_not_release_stock_when_cancelling_an_already_paid_order` → `createOrderWithItem('processing', stock: 8, qty: 2)` (this test only exercises `releaseStockIfApplicable`'s `order_status` guard, not the payment guard — leave `paymentStatus` at its `'unpaid'` default, it's irrelevant to this assertion). Leave `test_releases_stock_when_cancelling_a_pending_order`'s `createOrderWithItem('pending', ...)` untouched — no `paid`/`processing` involved.

- [ ] **Step 6: Run tests to verify they pass**

Run: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test --filter="AdminPaymentVerificationTest|OrderUpdateStatusActionTest"`
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment"
git add api/app/Filament/Resources/OrderResource.php api/app/Filament/Resources/OrderResource/Pages/ViewOrder.php \
  api/tests/Feature/Order/OrderUpdateStatusActionTest.php api/tests/Feature/Order/AdminPaymentVerificationTest.php
git commit -m "feat(admin): manual payment verification workflow"
```

---

## Task 7: Storefront data layer (types + orderService.ts)

**Files:**
- Modify: `storefront/app/types/index.ts`
- Modify: `storefront/app/services/orderService.ts`
- Test: `storefront/tests/orderService.test.ts`

**Interfaces:**
- Produces: `Order.orderStatus: OrderStatus`, `Order.paymentStatus: PaymentStatus`, `Order.paymentProof: string | null`, `Order.paymentRejectedReason: string | null` (replaces `Order.status`, drops `Order.snapToken`). `orderService.uploadPaymentProof(orderNumber: string, file: File, guestEmail?: string): Promise<Order>`. `orderService.createOrder(...)` return type becomes `{ orderNumber: string }` (drops `snapToken`). `orderService.requestSnapToken` removed.

- [ ] **Step 1: Write the failing unit test**

```typescript
// storefront/tests/orderService.test.ts
import { describe, expect, it } from "vitest";
import { mapOrderForTest } from "../app/services/orderService";

describe("orderService.mapOrder", () => {
  it("maps order_status/payment_status snake_case fields to camelCase", () => {
    const raw = {
      id: 1,
      order_number: "ORD-TEST-1",
      order_status: "processing",
      payment_status: "paid",
      payment_proof: "http://localhost:8000/uploads/payment-proofs/bukti.jpg",
      payment_rejected_reason: null,
      courier: "jne",
      courier_service: "REG",
      shipping_cost: 15000,
      subtotal: 100000,
      total: 115000,
      tracking_number: null,
      created_at: "2026-08-27T00:00:00.000000Z",
      items: [],
      shipping_address: null,
    };

    const order = mapOrderForTest(raw);

    expect(order.orderStatus).toBe("processing");
    expect(order.paymentStatus).toBe("paid");
    expect(order.paymentProof).toBe(
      "http://localhost:8000/uploads/payment-proofs/bukti.jpg",
    );
    expect(order.paymentRejectedReason).toBeNull();
    expect((order as any).status).toBeUndefined();
    expect((order as any).snapToken).toBeUndefined();
  });
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/storefront" && npx vitest run tests/orderService.test.ts`
Expected: FAIL (`mapOrderForTest` is not exported)

- [ ] **Step 3: Update `Order`/`OrderStatus` types**

In `storefront/app/types/index.ts`, replace:
```typescript
export type OrderStatus =
  | "pending"
  | "paid"
  | "processing"
  | "shipped"
  | "completed"
  | "cancelled"
  | "expired";
```
with:
```typescript
export type OrderStatus =
  | "pending"
  | "processing"
  | "shipped"
  | "completed"
  | "cancelled"
  | "expired";

export type PaymentStatus =
  | "unpaid"
  | "awaiting_verification"
  | "paid"
  | "rejected"
  | "refunded";
```

Then update the `Order` interface:
```typescript
export interface Order {
  id: number;
  orderNumber: string;
  orderStatus: OrderStatus;
  paymentStatus: PaymentStatus;
  paymentProof: string | null;
  paymentRejectedReason: string | null;
  courier: string;
  courierService: string;
  shippingCost: number;
  subtotal: number;
  total: number;
  trackingNumber: string | null;
  createdAt: string;
  items: OrderItem[];
  shippingAddress?: {
    recipientName: string;
    phone: string;
    province: string;
    city: string;
    district: string;
    postalCode: string;
    fullAddress: string;
    rajaongkirDistrictId?: number | null;
  } | null;
}
```
(Dropped `status` and `snapToken`.)

- [ ] **Step 4: Rewrite `orderService.ts`**

Replace the full file:

```typescript
import type { Order, OrderItem } from "~/types";
import { useAuthStore } from "~/store/auth";

function apiBase(): string {
  return useRuntimeConfig().public.apiBase;
}

function getHeaders(): Record<string, string> {
  const authStore = useAuthStore();
  const headers: Record<string, string> = {
    Accept: "application/json",
    "Content-Type": "application/json",
  };
  if (authStore.token) {
    headers.Authorization = `Bearer ${authStore.token}`;
  }
  return headers;
}

function mapOrderItem(raw: any): OrderItem {
  return {
    id: raw.id,
    productVariantId: raw.product_variant_id,
    productName: raw.product_name,
    variantName: raw.variant_name,
    price: raw.price,
    qty: raw.qty,
    subtotal: raw.subtotal,
    rated: !!raw.rating || !!raw.rated,
  };
}

function mapOrder(raw: any): Order {
  const shippingAddress = raw.shipping_address
    ? {
        recipientName: raw.shipping_address.recipient_name,
        phone: raw.shipping_address.phone,
        province: raw.shipping_address.province,
        city: raw.shipping_address.city,
        district: raw.shipping_address.district,
        postalCode: raw.shipping_address.postal_code,
        fullAddress: raw.shipping_address.full_address,
        rajaongkirDistrictId:
          raw.shipping_address.rajaongkir_district_id ?? null,
      }
    : null;

  return {
    id: raw.id,
    orderNumber: raw.order_number,
    orderStatus: raw.order_status,
    paymentStatus: raw.payment_status,
    paymentProof: raw.payment_proof ?? null,
    paymentRejectedReason: raw.payment_rejected_reason ?? null,
    courier: raw.courier,
    courierService: raw.courier_service,
    shippingCost: raw.shipping_cost,
    subtotal: raw.subtotal,
    total: raw.total,
    trackingNumber: raw.tracking_number,
    createdAt: raw.created_at,
    items: (raw.items ?? []).map(mapOrderItem),
    shippingAddress,
  };
}

// Exported only for the unit test in tests/orderService.test.ts — not part
// of the public service surface used by pages.
export function mapOrderForTest(raw: any): Order {
  return mapOrder(raw);
}

export const orderService = {
  async getOrders(): Promise<{ data: Order[]; total: number }> {
    const response = await $fetch<{ data: any[]; meta?: { total: number } }>(
      "/orders",
      {
        baseURL: apiBase(),
        headers: getHeaders(),
      },
    );
    return {
      data: (response.data ?? []).map(mapOrder),
      total: response.meta?.total ?? (response.data ?? []).length,
    };
  },

  async getOrderDetails(orderNumber: string): Promise<Order> {
    const response = await $fetch<{ data: any }>(`/orders/${orderNumber}`, {
      baseURL: apiBase(),
      headers: getHeaders(),
    });
    return mapOrder(response.data);
  },

  async lookupOrder(orderNumber: string, email: string): Promise<Order> {
    const response = await $fetch<{ data: any }>("/orders/lookup", {
      baseURL: apiBase(),
      query: { order_number: orderNumber, email },
    });
    return mapOrder(response.data);
  },

  async createOrder(data: {
    items: { variantId: number; qty: number }[];
    addressId?: number | null;
    guestName?: string | null;
    guestEmail?: string | null;
    guestPhone?: string | null;
    shippingAddress?: any;
    courier: string;
    courierService: string;
    shippingCost: number;
  }): Promise<{ orderNumber: string }> {
    const body: any = {
      items: data.items.map((i) => ({ variant_id: i.variantId, qty: i.qty })),
      courier: data.courier,
      courier_service: data.courierService,
      shipping_cost: data.shippingCost,
    };

    if (data.addressId) {
      body.address_id = data.addressId;
    } else {
      body.guest_name = data.guestName;
      body.guest_email = data.guestEmail;
      body.guest_phone = data.guestPhone;
      body.shipping_address = data.shippingAddress
        ? {
            recipient_name: data.shippingAddress.recipientName,
            phone: data.shippingAddress.phone,
            province: data.shippingAddress.province,
            city: data.shippingAddress.city,
            district: data.shippingAddress.district,
            postal_code: data.shippingAddress.postalCode,
            full_address: data.shippingAddress.fullAddress,
            rajaongkir_district_id: data.shippingAddress.rajaongkirDistrictId,
          }
        : null;
    }

    const response = await $fetch<{ order_number: string }>("/orders", {
      baseURL: apiBase(),
      method: "POST",
      headers: getHeaders(),
      body,
    });

    return { orderNumber: response.order_number };
  },

  async getShippingCost(
    districtId: number,
    totalWeight: number,
  ): Promise<any[]> {
    const response = await $fetch<{ couriers: any[] }>("/shipping/cost", {
      baseURL: apiBase(),
      method: "POST",
      headers: getHeaders(),
      body: {
        district_id: districtId,
        total_weight: totalWeight,
      },
    });
    return response.couriers ?? [];
  },

  async uploadPaymentProof(
    orderNumber: string,
    file: File,
    guestEmail?: string,
  ): Promise<Order> {
    const formData = new FormData();
    formData.append("proof", file);
    if (guestEmail) formData.append("guest_email", guestEmail);

    const authStore = useAuthStore();
    const headers: Record<string, string> = {};
    if (authStore.token) headers.Authorization = `Bearer ${authStore.token}`;

    const response = await $fetch<{ data: any }>(
      `/orders/${orderNumber}/payment-proof`,
      {
        baseURL: apiBase(),
        method: "POST",
        headers,
        body: formData,
      },
    );
    return mapOrder(response.data);
  },

  async rateOrderItem(orderItemId: number, stars: number): Promise<any> {
    return await $fetch(`/order-items/${orderItemId}/rating`, {
      baseURL: apiBase(),
      method: "POST",
      headers: getHeaders(),
      body: { stars },
    });
  },
};
```

(Note: `getHeaders()` sets `Content-Type: application/json`, which must NOT be used for the multipart upload — `uploadPaymentProof` builds its own headers object without that key so the browser sets the correct `multipart/form-data` boundary automatically. `requestSnapToken` is dropped entirely — no replacement, matches Task 5's removal of the backend endpoint.)

- [ ] **Step 5: Run test to verify it passes**

Run: `cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/storefront" && npx vitest run tests/orderService.test.ts`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment"
git add storefront/app/types/index.ts storefront/app/services/orderService.ts storefront/tests/orderService.test.ts
git commit -m "refactor(storefront): order_status/payment_status types, drop Midtrans from orderService"
```

---

## Task 8: Storefront checkout page — remove Midtrans flow

**Files:**
- Modify: `storefront/app/pages/checkout/index.vue`

**Interfaces:**
- Consumes: `orderService.createOrder(...)` returning `{ orderNumber: string }` (Task 7).

- [ ] **Step 1: Remove the Midtrans script loader and Snap-pay branching from `handleCheckout()`**

Replace the whole `handleCheckout` function body from `const { orderNumber, snapToken } = ...` to the end of the `try` block:

```typescript
// Submit Order
const { orderNumber } = await orderService.createOrder(orderPayload);

cart.clear();
router.push(orderUrl(orderNumber));
```

Delete the `loadMidtransSnapScript()` function entirely (no longer called anywhere in this file).

- [ ] **Step 2: Verify no leftover Midtrans references in this file**

Run: `cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/storefront" && grep -in "midtrans\|snap" app/pages/checkout/index.vue`
Expected: no output.

- [ ] **Step 3: Manual verification in the browser**

Run: `cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/storefront" && npm run dev` (and `cd ../api && php artisan serve` in another terminal if not already running). Add a product to cart, go through checkout as a guest, submit, confirm it redirects straight to `/orders/{orderNumber}` with no popup and no console errors. Stop both dev servers when done.

- [ ] **Step 4: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment"
git add storefront/app/pages/checkout/index.vue
git commit -m "refactor(checkout): remove Midtrans Snap popup flow"
```

---

## Task 9: Storefront order detail page — payment instructions, upload, WA/LINE

**Files:**
- Modify: `storefront/app/pages/orders/[orderNumber].vue`

**Interfaces:**
- Consumes: `orderService.uploadPaymentProof(orderNumber, file, guestEmail?)` (Task 7), `GET /api/payment-methods` (Task 2), `GET /api/settings` (`whatsapp_number`, `line_url` — Task 3), `Order.orderStatus`/`Order.paymentStatus`/`Order.paymentProof`/`Order.paymentRejectedReason` (Task 7).

- [ ] **Step 1: Remove the Midtrans retry-payment code**

Delete `loadMidtransSnapScript()`, `handleRetryPayment()`, and the `loadingPayment` ref. Delete the "Bayar Sekarang" `UButton` block in the header card (the `v-if="order.status === 'pending'"` block).

- [ ] **Step 2: Update all `order.status` references to `order.orderStatus`**

In `getStatusBadgeColor`, `getStatusLabel` calls and the template's `:color="getStatusBadgeColor(order.status)"` / `{{ getStatusLabel(order.status) }}` — replace every `order.status` with `order.orderStatus`. Inside `getStatusLabel`, remove the `case "paid": return "Dibayar";` branch (that state no longer exists on `orderStatus`).

- [ ] **Step 3: Add payment methods + settings fetch**

Add near the top of `<script setup>`, alongside the existing imports:
```typescript
import { useApi } from "~/services/api";
```
Add state and fetches inside `<script setup>`:
```typescript
const api = useApi();
const { data: paymentMethodsResponse } = useAsyncData("payment-methods", () =>
  api.getPaymentMethods(),
);
const paymentMethods = computed(() => paymentMethodsResponse.value?.data ?? []);

const { data: settingsResponse } = useAsyncData("store-settings-order", () =>
  api.getSettings(),
);
const settings = computed(() => settingsResponse.value?.data ?? {});

const proofFile = ref<File | null>(null);
const uploadingProof = ref(false);
const proofError = ref("");

function onProofFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0] ?? null;
  proofError.value = "";
  if (file && file.size > 2 * 1024 * 1024) {
    proofError.value = "Ukuran file maksimal 2MB.";
    proofFile.value = null;
    return;
  }
  proofFile.value = file;
}

async function submitProof() {
  if (!proofFile.value || !order.value) return;
  uploadingProof.value = true;
  try {
    order.value = await orderService.uploadPaymentProof(
      order.value.orderNumber,
      proofFile.value,
      guestEmail.value || undefined,
    );
    proofFile.value = null;
    toast.add({
      title: "Bukti Terkirim",
      description: "Bukti pembayaran berhasil diunggah, menunggu verifikasi admin.",
      color: "primary",
    });
  } catch (error: any) {
    toast.add({
      title: "Gagal Mengunggah",
      description: error.data?.message || "Terjadi kesalahan, coba lagi.",
      color: "error",
    });
  } finally {
    uploadingProof.value = false;
  }
}

function whatsappConfirmUrl(): string {
  const phone = settings.value.whatsapp_number;
  if (!phone || !order.value) return "";
  const text = encodeURIComponent(
    `Konfirmasi pembayaran pesanan ${order.value.orderNumber}`,
  );
  return `https://api.whatsapp.com/send/?phone=${phone}&text=${text}&type=phone_number&app_absent=0`;
}
```

(`api.getPaymentMethods()` and `api.getSettings()` — verify `useApi()`'s shape first: run `grep -n "getSettings\|getBanners" app/services/api.ts` to confirm the existing method naming convention, then add `getPaymentMethods: () => $fetch(...)` following that exact same pattern, hitting `GET /payment-methods`.)

- [ ] **Step 4: Add the payment UI to the template**

Insert this new card into the "Right side" column (`<div class="space-y-6">` block), right after the "Receipt Card" `</div>` and before the closing `</div>` of that column:

```html
<!-- Payment Instructions & Proof Upload -->
<div
  v-if="order.paymentStatus === 'unpaid' || order.paymentStatus === 'rejected'"
  class="bg-white border border-gray-100 rounded-3xl p-6 shadow-xs space-y-4"
>
  <h2 class="text-xs uppercase tracking-wider font-extrabold text-primary-850 border-b border-gray-100 pb-2">
    Instruksi Pembayaran
  </h2>

  <p v-if="order.paymentStatus === 'rejected'" class="text-xs text-red-600 font-semibold bg-red-50 rounded-xl p-3">
    Bukti pembayaran sebelumnya ditolak{{ order.paymentRejectedReason ? ': ' + order.paymentRejectedReason : '' }}. Silakan unggah ulang.
  </p>

  <div v-for="method in paymentMethods" :key="method.id" class="text-xs border border-gray-100 rounded-2xl p-3.5 space-y-1">
    <p class="font-extrabold text-gray-900">{{ method.label }}</p>
    <p v-if="method.account_number" class="text-gray-600 font-semibold select-all">{{ method.account_number }}</p>
    <p v-if="method.account_holder" class="text-gray-400">a/n {{ method.account_holder }}</p>
    <img v-if="method.qr_image" :src="method.qr_image" alt="QR" class="w-28 h-28 object-contain mt-2" />
  </div>

  <div class="space-y-2 pt-2">
    <input type="file" accept="image/jpeg,image/png,image/webp" class="text-xs" @change="onProofFileChange" />
    <p v-if="proofError" class="text-[10px] text-red-500 font-semibold">{{ proofError }}</p>
    <UButton
      block
      color="primary"
      size="sm"
      class="font-bold rounded-xl cursor-pointer"
      :disabled="!proofFile"
      :loading="uploadingProof"
      @click="submitProof"
    >
      Kirim Bukti Transfer
    </UButton>
  </div>
</div>

<div
  v-else-if="order.paymentStatus === 'awaiting_verification'"
  class="bg-amber-50 border border-amber-100 rounded-3xl p-6 shadow-xs text-center"
>
  <p class="text-xs font-bold text-amber-700">Menunggu verifikasi admin.</p>
  <p class="text-[10px] text-amber-600 mt-1">Bukti pembayaran sudah diterima, proses verifikasi biasanya memakan waktu beberapa jam.</p>
</div>

<!-- WhatsApp/LINE Confirmation -->
<div v-if="order.paymentStatus !== 'unpaid'" class="grid grid-cols-1 gap-2.5">
  <a
    v-if="settings.whatsapp_number"
    :href="whatsappConfirmUrl()"
    target="_blank"
    rel="noopener"
    class="flex items-center justify-center gap-2 text-xs font-bold rounded-xl px-4 py-3 border border-primary-200 text-primary-700 bg-primary-50/40 hover:bg-primary-50 transition-colors"
  >
    <UIcon name="i-simple-icons-whatsapp" class="w-4 h-4" />
    Konfirmasi via WhatsApp
  </a>
  <a
    v-if="settings.line_url"
    :href="settings.line_url"
    target="_blank"
    rel="noopener"
    class="flex items-center justify-center gap-2 text-xs font-bold rounded-xl px-4 py-3 border border-gray-200 text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors"
  >
    Konfirmasi via LINE
  </a>
</div>
```

- [ ] **Step 5: Verify no leftover Midtrans references**

Run: `cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/storefront" && grep -in "midtrans\|snap\|order.status\b" "app/pages/orders/[orderNumber].vue"`
Expected: no output.

- [ ] **Step 6: Manual verification in the browser**

With both dev servers running, open an order in `unpaid` state: confirm payment methods list renders, file picker + submit works, order flips to `awaiting_verification` view after upload. In the admin panel (`/admin/orders/{id}`), run the `verifyPayment` → accept action, then reload the storefront order page and confirm the WA/LINE buttons and payment card disappear appropriately per the new `paymentStatus`.

- [ ] **Step 7: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment"
git add "storefront/app/pages/orders/[orderNumber].vue"
git commit -m "feat(orders): payment proof upload + WA/LINE confirmation UI"
```

---

## Task 10: Footer payment methods + env cleanup

**Files:**
- Modify: `storefront/app/components/AppFooter.vue`
- Modify: `storefront/app/services/api.ts`
- Modify: `storefront/.env`
- Modify: `storefront/nuxt.config.ts`

**Interfaces:**
- Consumes: `GET /api/payment-methods` (Task 2).

- [ ] **Step 1: Add `getPaymentMethods` to the shared API service (if Task 9 didn't already land it here)**

Run: `cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/storefront" && grep -n "getBanners\|getSettings" app/services/api.ts` to see the exact existing pattern, then add a `getPaymentMethods` method following it exactly (same `$fetch` call shape, hitting `/payment-methods`).

- [ ] **Step 2: Replace the hardcoded payment badge list in `AppFooter.vue`**

Add the fetch near the top of `<script setup>`:
```typescript
const { data: paymentMethodsResponse } = useAsyncData("footer-payment-methods", () =>
  api.getPaymentMethods(),
);
const paymentMethods = computed(() => paymentMethodsResponse.value?.data ?? []);
```

Replace:
```html
<div class="flex flex-wrap gap-1">
  <span
    v-for="method in [
      'Transfer Bank',
      'GoPay',
      'OVO',
      'DANA',
      'Kartu Kredit',
    ]"
    :key="method"
    class="px-2 py-1 bg-gray-700/60 border border-gray-800/80 rounded text-xs text-gray-400 font-semibold hover:border-primary-600/40 hover:text-white transition-colors duration-200"
  >
    {{ method }}
  </span>
</div>
```
with:
```html
<div class="flex flex-wrap gap-1">
  <span
    v-for="method in paymentMethods"
    :key="method.id"
    class="px-2 py-1 bg-gray-700/60 border border-gray-800/80 rounded text-xs text-gray-400 font-semibold hover:border-primary-600/40 hover:text-white transition-colors duration-200"
  >
    {{ method.label }}
  </span>
</div>
```

- [ ] **Step 3: Remove Midtrans env keys**

In `storefront/.env`, delete these two lines:
```
NUXT_PUBLIC_MIDTRANS_CLIENT_KEY=Mid-client-yRLy_6g9XXlT38PI
NUXT_PUBLIC_MIDTRANS_IS_PRODUCTION=true
```

In `storefront/nuxt.config.ts`, remove `midtransClientKey` and `midtransIsProduction` from `runtimeConfig.public`, leaving only `apiBase`.

- [ ] **Step 4: Verify no leftover Midtrans references anywhere in storefront**

Run: `cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/storefront" && grep -irn "midtrans" app .env nuxt.config.ts`
Expected: no output.

- [ ] **Step 5: Manual verification**

Run `npm run dev`, load the homepage, scroll to footer, confirm the "Metode Pembayaran" badges now reflect whatever's seeded in `payment_methods` (empty list is fine if none seeded yet — confirm it doesn't error, just renders an empty row).

- [ ] **Step 6: Commit**

```bash
cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment"
git add storefront/app/components/AppFooter.vue storefront/app/services/api.ts storefront/.env storefront/nuxt.config.ts
git commit -m "refactor(storefront): live payment methods in footer, drop Midtrans env"
```

---

## Task 11: Final verification

**Files:** none (verification only).

- [ ] **Step 1: Run the full backend test suite**

Run: `cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/api" && export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test`
Expected: PASS, 0 failures. If anything fails, it's almost certainly a leftover `'status' =>`/`->status` reference in a test file this plan didn't explicitly enumerate — grep for it: `grep -rn "'status' =>" tests/Feature/ | grep -v payment_status`.

- [ ] **Step 2: Run the full frontend unit test suite**

Run: `cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/storefront" && npx vitest run`
Expected: PASS.

- [ ] **Step 3: Grep the whole repo for any remaining Midtrans reference**

Run: `cd "/Users/adiprimanto/Developer/Rangkai Web/bbystargo/.claude/worktrees/manual-payment" && grep -rin "midtrans" api/app api/config api/routes api/tests storefront/app storefront/.env storefront/nuxt.config.ts docs/api CLAUDE.md 2>/dev/null`
Expected: no output (a hit here means a step above was missed).

- [ ] **Step 4: Manual end-to-end browser check**

With `php artisan serve` and `npm run dev` both running: place a Ready Stock order as a guest → land on order detail page → see payment methods + upload form → upload a proof → see "awaiting verification" state → log into `/admin` → open the order → run `verifyPayment` accept → confirm `order_status` becomes `processing`, `payment_status` becomes `paid`, and an admin notification appears (bell icon in Filament topbar).

- [ ] **Step 5: Update the plan's own checkboxes and report completion**

No commit needed for this task — it's verification-only. Report the final state to the user: all tasks done, full suite green, no Midtrans references remaining.
