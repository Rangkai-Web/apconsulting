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
