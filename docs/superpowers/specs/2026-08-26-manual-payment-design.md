# Manual Payment (Ready Stock) — Design

Status: draft, pending user review.
Related: `plan-online-shop.docx` §6 (Pembayaran Manual), §8 (keputusan terkunci: "Pembayaran manual", "Konfirmasi pembayaran melalui WhatsApp", "Tidak ada payment gateway otomatis pada MVP").
Supersedes: Midtrans Snap integration described in `docs/superpowers/specs/2026-06-19-toko-online-design.md` §6.

## 1. Tujuan

Ganti Midtrans Snap (never actually configured for BbystarGO — `MIDTRANS_SERVER_KEY` kosong di `.env`, selalu jalan di mock mode) dengan alur transfer manual: customer transfer ke rekening/e-wallet yang ditampilkan, upload bukti transfer, admin verifikasi manual di panel. Konfirmasi WhatsApp/LINE cuma tombol deep-link — status pembayaran tetap dicatat di sistem, bukan di-drive oleh chat.

## 2. Scope

**In:** Split `order_status`/`payment_status`, tabel `payment_methods` (admin CRUD), upload bukti transfer (storefront), verifikasi admin (Filament), tombol konfirmasi WA/LINE, penyesuaian auto-expire, penghapusan total kode Midtrans.

**Out (sub-project lain):** Alur Pre-Order (DP, status impor `ordered`/`arrived_warehouse_proxy`/dst — lihat catatan §3), guest checkout removal (sub-project terpisah), dashboard admin stats baru.

## 3. Data Model

### 3.1 `orders` table — migration `split_status_and_add_payment_fields_on_orders_table`

```php
Schema::table('orders', function (Blueprint $table) {
    $table->renameColumn('status', 'order_status');
    $table->string('payment_status')->default('unpaid')->after('order_status');
    $table->string('payment_proof')->nullable()->after('shipping_cost');
    $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof');
    $table->string('payment_rejected_reason')->nullable()->after('payment_proof_uploaded_at');
    $table->dropColumn(['midtrans_order_id', 'midtrans_transaction_id']);
});
```

- `order_status` enum values (this sub-project): `pending, processing, shipped, completed, cancelled, expired`. (Dropped `paid` — that's now `payment_status`.)
- `payment_status` enum values: `unpaid, awaiting_verification, paid, rejected, refunded`. Default `unpaid`.
- Import/PO-specific `order_status` values (`ordered`, `arrived_warehouse_proxy`, `shipped_to_indonesia`, `arrived_warehouse_ina`, `ready_to_ship`) are **not** added in this migration — deferred to the PO sub-project so the enum doesn't carry unused values this sub-project doesn't wire up.
- Using `string` (not `enum` column type) for both — MySQL `enum` migrations are painful to extend later (PO sub-project will add values); app-level validation enforces the allowed set via `OrderResource::statusOptions()`/`paymentStatusOptions()`.

### 3.2 New table `payment_methods` — mirrors `banners` pattern exactly

```php
Schema::create('payment_methods', function (Blueprint $table) {
    $table->id();
    $table->string('label');            // "BCA", "ShopeePay", "SeaBank", "QRIS"
    $table->string('account_number')->nullable();
    $table->string('account_holder')->nullable();
    $table->string('qr_image')->nullable();
    $table->text('instructions')->nullable();
    $table->integer('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

`App\Models\PaymentMethod` — `#[Fillable]` on all columns above except id/timestamps, casts `is_active` bool, `sort_order` int (identical shape to `App\Models\Banner`).

### 3.3 `store_settings` — add `line_url`

```php
Schema::table('store_settings', function (Blueprint $table) {
    $table->string('line_url')->nullable()->after('whatsapp_number');
});
```

Full LINE OA link (e.g. `https://line.me/ti/p/@xxxxx`) — LINE has no universal phone-number deep link like WhatsApp's `wa.me`, so this is admin-provided verbatim, same treatment as `instagram_url`.

## 4. Payment Methods (admin CRUD)

New `App\Filament\Resources\PaymentMethodResource` — copy `BannerResource` structure 1:1 (form, table, pages: index/create/edit, `EditAction`+`DeleteAction`, `TernaryFilter` on `is_active`). Form fields: `label` (TextInput, required), `account_number` (TextInput), `account_holder` (TextInput), `qr_image` (FileUpload, disk `public_uploads`, dir `payment-methods`, image, maxSize 2048, jpg/png/webp), `instructions` (Textarea), `sort_order` (TextInput numeric), `is_active` (Toggle).

New `App\Http\Controllers\Api\PaymentMethodController::index()` + `App\Http\Resources\Api\PaymentMethodResource` — mirrors `BannerController`/`BannerResource` exactly: `where('is_active', true)->orderBy('sort_order')->get()`.

New route: `Route::get('/payment-methods', [PaymentMethodController::class, 'index']);` (public, alongside `/banners`).

New doc: `docs/api/payment-methods.md` (new file — first resource without an existing doc; follow `banners.md`'s shape).

## 5. Checkout Flow Changes (storefront)

`checkout/index.vue` — `handleCheckout()`: remove `loadMidtransSnapScript()` call and `window.snap.pay(...)` block entirely. After `orderService.createOrder(orderPayload)` succeeds: `cart.clear()`, redirect straight to `orderUrl(orderNumber)` (drop the `mock`/`status` query params — no longer meaningful).

`OrderController::store()` — drop the `getMidtransSnapToken()` call and `snap_token` from the 201 response; response becomes `{ "order_number": "..." }` only.

Remove entirely: `requestSnapToken()` action, `getMidtransSnapToken()` private method, route `POST /orders/{order_number}/snap-token`, storefront `orderService.requestSnapToken()`, `handleRetryPayment()` + "Bayar Sekarang" retry button in `orders/[orderNumber].vue` (replaced by the upload-proof UI in §6), `loadMidtransSnapScript()` in both storefront files, `NUXT_PUBLIC_MIDTRANS_CLIENT_KEY`/`NUXT_PUBLIC_MIDTRANS_IS_PRODUCTION` from `storefront/.env` and `nuxt.config.ts` runtimeConfig.

## 6. Payment Proof Upload (storefront + API)

`orders/[orderNumber].vue` — when `order.paymentStatus === 'unpaid'` or `'rejected'`: show payment instructions card (list from `GET /api/payment-methods`, each with copy-to-clipboard account number + QR image if present) + file upload form (`UFileUpload` or native input, accept `image/jpeg,image/png,image/webp`, client-side 2MB check before submit). When `'awaiting_verification'`: show "Menunggu verifikasi admin" state, no re-upload. When `'rejected'`: show `payment_rejected_reason` + allow re-upload.

New endpoint `POST /api/orders/{order_number}/payment-proof`:

```php
public function uploadPaymentProof(Request $request, $orderNumber)
{
    $order = Order::where('order_number', $orderNumber)->firstOrFail();

    // Same access pattern as show()/lookup(): member must own it, guest must
    // supply matching guest_email (prevents a stranger who guessed the order
    // number from spoofing a proof upload on someone else's order).
    if ($request->user()) {
        abort_if($order->user_id !== $request->user()->id, 403);
    } else {
        $validated = $request->validate(['guest_email' => ['required', 'email']]);
        abort_if($order->guest_email !== $validated['guest_email'], 403);
    }

    abort_if(! in_array($order->payment_status, ['unpaid', 'rejected']), 422, 'Bukti pembayaran sudah diunggah untuk pesanan ini.');

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

Route (public, guest-accessible like `/orders` POST and `/orders/lookup`):
```php
Route::post('/orders/{order_number}/payment-proof', [OrderController::class, 'uploadPaymentProof']);
```

`OrderResource` (API) — add `payment_status`, `payment_proof` (as a full URL via `Storage::disk('public_uploads')->url(...)`, same pattern as `SettingsResource::logo`), `payment_rejected_reason`. Rename `status` key to `order_status` in the response to match the DB rename (breaking change for the frontend `Order` type/`mapOrder()` — update both together).

`orderService.ts` — add `uploadPaymentProof(orderNumber, file, guestEmail?)` using `FormData`. Update `mapOrder()`/`Order` type: `status` → `orderStatus`, add `paymentStatus`, `paymentProof`, `paymentRejectedReason`.

## 7. Admin Verification (Filament)

`OrderResource.php` — rename `statusOptions()`/`statusColor()` to operate on `order_status` values (drop `'paid'` case). Add `paymentStatusOptions()`/`paymentStatusColor()` (`unpaid`=gray, `awaiting_verification`=warning, `paid`=success, `rejected`=danger, `refunded`=info). Table gets a second badge column for `payment_status`. Infolist adds a "Pembayaran" section: `payment_status` badge, `payment_proof` as `ImageEntry` (disk `public_uploads`, only shown `when(fn ($record) => $record->payment_proof)`), `payment_rejected_reason`.

`ViewOrder.php` — two header actions:

1. **`verifyPayment`** — visible only `fn (Order $record) => $record->payment_status === 'awaiting_verification'`. Form: `Radio::make('decision')` (`accept`/`reject`), `Textarea::make('reason')` visible+required only when `decision === 'reject'`. Action: `accept` → `payment_status='paid'`, and if `order_status==='pending'` bump it to `'processing'`; then fire the same admin notifications the Midtrans webhook used to fire on `paid` (Filament `Notification::make()->sendToDatabase($admins)` + `OrderPaidNotification` mail to each admin — moved here verbatim from `WebhookController::midtrans()`, which is deleted per §10). `reject` → `payment_status='rejected'`, `payment_rejected_reason=$data['reason']`, no notification.
2. **`updateStatus`** (existing, kept) — now operates on `order_status` only (rename field references). Add a guard: disable/hide the `shipped` option in the `Select` unless `$record->payment_status === 'paid'` (`Select::options()` filtered dynamically, or a form-level validation message) — prevents shipping an unpaid order by mistake.

## 8. WhatsApp/LINE Confirmation

`orders/[orderNumber].vue` — two buttons next to the upload-proof card, visible once a proof is uploaded (`payment_status !== 'unpaid'`): "Konfirmasi via WhatsApp" (`https://api.whatsapp.com/send/?phone={settings.whatsapp_number}&text=Konfirmasi pembayaran pesanan {order.orderNumber}`, same pattern already used in `AppFooter.vue`) and "Konfirmasi via LINE" (`href={settings.line_url}`, hidden if `line_url` is empty). No backend involved — pure link-outs.

## 9. Order Expiry

`ExpirePendingOrders` — query becomes `Order::where('order_status', 'pending')->where('payment_status', '!=', 'awaiting_verification')->where('created_at', '<=', $threshold)`. This covers both `unpaid` (never uploaded proof) and `rejected` (uploaded, admin rejected, never re-uploaded) — both are legitimately abandoned. Only `awaiting_verification` is excluded — customer uploaded proof and it's mid-review, so it must **not** auto-expire even past the window, that would silently discard a legitimate pending verification. (`paid` orders are never matched anyway since their `order_status` has already moved past `pending`.)

Config relocation: `config/midtrans.php` deleted; `order_expire_hours` moves to new `config/orders.php`:
```php
return ['expire_hours' => (int) env('ORDER_EXPIRE_HOURS', 24)];
```
`ORDER_EXPIRE_HOURS` env key unchanged.

## 10. Midtrans Removal (full deletion list)

- `api/app/Http/Controllers/Api/WebhookController.php` (delete file)
- `api/config/midtrans.php` (delete file)
- Route `POST /api/webhooks/midtrans` (remove from `routes/api.php`)
- `OrderController::getMidtransSnapToken()`, `requestSnapToken()` (remove methods), route `POST /orders/{order_number}/snap-token`
- `Order` model: `midtrans_order_id`/`midtrans_transaction_id` out of `#[Fillable]`
- `storefront/.env`: `NUXT_PUBLIC_MIDTRANS_CLIENT_KEY`, `NUXT_PUBLIC_MIDTRANS_IS_PRODUCTION`
- `storefront/nuxt.config.ts`: `runtimeConfig.public.midtransClientKey`/`midtransIsProduction` (lines ~21-22)
- `storefront/app/pages/checkout/index.vue` + `orders/[orderNumber].vue`: `loadMidtransSnapScript()`, all `window.snap.*` calls
- `AppFooter.vue`: "Metode Pembayaran" badge list (`GoPay, OVO, DANA, Kartu Kredit`) → replace with live `GET /api/payment-methods` labels
- `docs/api/orders.md`: rewrite `/webhooks/midtrans` section out, rewrite checkout response shape, add payment-proof endpoint
- `CLAUDE.md`: "Payment: Midtrans Snap (popup) only" → "Payment: manual transfer, admin-verified (see `docs/api/orders.md`, `docs/api/payment-methods.md`)"
- Test files referencing Midtrans/`snap_token`/`/webhooks/midtrans` (`OrderApiTest.php` and any webhook test) — rewritten per §11

## 11. Testing Plan

`tests/Feature/Order/OrderApiTest.php` — update existing checkout-creation assertions (no `snap_token` in response; `order_status`/`payment_status` default `pending`/`unpaid`).

New `tests/Feature/Order/PaymentProofUploadTest.php`:
1. Member uploads valid proof on own order → 200, `payment_status=awaiting_verification`, file stored.
2. Guest uploads with matching `guest_email` → same success path.
3. Guest uploads with wrong `guest_email` → 403.
4. Member uploads on someone else's order → 403.
5. Upload when `payment_status` already `awaiting_verification` or `paid` → 422 (no double-upload).
6. Re-upload after `rejected` → succeeds, clears `payment_rejected_reason`, back to `awaiting_verification`.
7. Non-image / oversized file → 422 validation error.

New `tests/Feature/Order/AdminPaymentVerificationTest.php` (Filament action, via `Livewire::test`):
1. Accept → `payment_status=paid`, `order_status` bumps `pending→processing`.
2. Reject with reason → `payment_status=rejected`, reason saved.
3. Action not available when `payment_status !== 'awaiting_verification'`.

`tests/Feature/Order/ExpirePendingOrdersCommandTest.php` — update: add case where `order_status=pending` but `payment_status=awaiting_verification` past threshold → NOT expired (new regression case for §9).

`tests/Feature/Settings/SettingsApiTest.php`, `tests/Feature/Shipping/*Test.php` — no logic change, but touch `orders` table shape indirectly (`Order::create` fixtures) only where they reference `status` — audit during implementation for any `'status' =>` fixture key that must become `'order_status' =>`.

New `tests/Feature/PaymentMethod/PaymentMethodApiTest.php` — mirrors `tests/Feature/Settings/BannerApiTest.php` (active-only listing, `sort_order` ordering).

## 12. Rollout Notes

DB is fresh/empty in this environment (confirmed during sub-project 1 — `toko_online_bbystargo` had zero prior orders), so the `status`→`order_status` rename is a pure schema change with no data backfill needed. If this were a populated production table, the rename migration would need a data-preserving `up()` (rename first, no value transform needed since `paid` is the only value moving to the new column and no live orders exist yet to be `paid`).
