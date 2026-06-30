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
