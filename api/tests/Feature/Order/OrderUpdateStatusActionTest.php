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
            'order_status' => $status,
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
        $this->assertEquals('shipped', $fresh->order_status);
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

        $this->assertEquals('cancelled', $order->fresh()->order_status);
        $this->assertEquals(10, $variant->fresh()->stock);
    }

    public function test_does_not_release_stock_when_cancelling_an_already_paid_order(): void
    {
        $admin = Admin::factory()->create();
        [$order, $variant] = $this->createOrderWithItem('paid', stock: 8, qty: 2);

        Livewire::actingAs($admin, 'admin')
            ->test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('updateStatus', data: ['status' => 'cancelled']);

        $this->assertEquals('cancelled', $order->fresh()->order_status);
        $this->assertEquals(8, $variant->fresh()->stock);
    }

    public function test_requires_tracking_number_when_status_is_shipped(): void
    {
        $admin = Admin::factory()->create();
        [$order] = $this->createOrderWithItem('paid');

        Livewire::actingAs($admin, 'admin')
            ->test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('updateStatus', data: ['status' => 'shipped'])
            ->assertHasActionErrors(['tracking_number' => 'required']);

        $this->assertEquals('paid', $order->fresh()->order_status);
    }
}
