<?php

namespace Tests\Feature\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_an_order_with_a_default_pending_status(): void
    {
        $order = Order::factory()->create();

        $this->assertEquals('pending', $order->order_status);
        $this->assertEquals('unpaid', $order->payment_status);
    }

    public function test_snapshots_product_name_variant_name_and_price_on_the_order_item(): void
    {
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

        $this->assertEquals($order->id, $item->order->id);
        $this->assertEquals('1kg', $item->variant_name);
        $this->assertEquals(25000, $item->price);
    }

    public function test_supports_a_guest_order_with_no_user_id_but_guest_contact_fields(): void
    {
        $order = Order::factory()->create([
            'user_id' => null,
            'guest_name' => 'Tamu Pembeli',
            'guest_email' => 'tamu@example.com',
            'guest_phone' => '081200000000',
        ]);

        $this->assertNull($order->user_id);
        $this->assertEquals('tamu@example.com', $order->guest_email);
    }
}
