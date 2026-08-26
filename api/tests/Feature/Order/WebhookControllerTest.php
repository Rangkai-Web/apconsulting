<?php

namespace Tests\Feature\Order;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createOrderAndItems(int $qty = 2): array
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
            'stock' => 10,
            'weight' => 200,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-12345',
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'courier' => 'jne',
            'courier_service' => 'REG',
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'order_status' => 'pending',
        ]);

        $item = $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'price' => 100000,
            'qty' => $qty,
            'subtotal' => 200000,
        ]);

        return [$order, $variant];
    }

    public function test_midtrans_webhook_marks_order_as_paid(): void
    {
        [$order, $variant] = $this->createOrderAndItems();

        $response = $this->postJson('/api/webhooks/midtrans', [
            'order_id' => $order->order_number,
            'transaction_status' => 'settlement',
            'gross_amount' => '215000.00',
            'status_code' => '200',
            'signature_key' => 'dummy',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('paid', $order->fresh()->order_status);
    }

    public function test_midtrans_webhook_releases_stock_when_cancelled_or_expired(): void
    {
        [$order, $variant] = $this->createOrderAndItems(2);

        // Current stock after checkout would have been decremented.
        // We set stock to 8, which simulates what happens after ORD-12345 (purchased 2) was checked out.
        $variant->update(['stock' => 8]);

        $response = $this->postJson('/api/webhooks/midtrans', [
            'order_id' => $order->order_number,
            'transaction_status' => 'expire',
            'gross_amount' => '215000.00',
            'status_code' => '200',
            'signature_key' => 'dummy',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('expired', $order->fresh()->order_status);
        $this->assertEquals(10, $variant->fresh()->stock); // 8 + 2
    }
}
