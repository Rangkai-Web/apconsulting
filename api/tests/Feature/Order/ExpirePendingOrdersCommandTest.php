<?php

namespace Tests\Feature\Order;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ExpirePendingOrdersCommandTest extends TestCase
{
    use RefreshDatabase;

    private function createOrderWithItem(string $status): array
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
            'order_number' => 'ORD-EXPIRE-' . uniqid(),
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'order_status' =>$status,
        ]);
        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'price' => 100000,
            'qty' => 2,
            'subtotal' => 200000,
        ]);

        return [$order, $variant];
    }

    public function test_expires_pending_orders_past_threshold_and_releases_stock(): void
    {
        [$order, $variant] = $this->createOrderWithItem('pending');
        Order::where('id', $order->id)->update(['created_at' => Carbon::now()->subHours(25)]);

        $this->artisan('orders:expire-pending');

        $this->assertEquals('expired', $order->fresh()->order_status);
        $this->assertEquals(10, $variant->fresh()->stock);
    }

    public function test_does_not_expire_pending_orders_within_threshold(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-YOUNG',
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'order_status' =>'pending',
        ]);

        $this->artisan('orders:expire-pending');

        $this->assertEquals('pending', $order->fresh()->order_status);
    }

    public function test_does_not_touch_non_pending_orders_even_if_old(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-PAID-OLD',
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'order_status' =>'paid',
        ]);
        Order::where('id', $order->id)->update(['created_at' => Carbon::now()->subHours(25)]);

        $this->artisan('orders:expire-pending');

        $this->assertEquals('paid', $order->fresh()->order_status);
    }

    public function test_idempotent_does_not_double_release_stock_on_second_run(): void
    {
        [$order, $variant] = $this->createOrderWithItem('pending');
        Order::where('id', $order->id)->update(['created_at' => Carbon::now()->subHours(25)]);

        $this->artisan('orders:expire-pending');
        $this->artisan('orders:expire-pending');

        $this->assertEquals('expired', $order->fresh()->order_status);
        $this->assertEquals(10, $variant->fresh()->stock); // 8 + 2 = 10, not 12 — no double release
    }
}
