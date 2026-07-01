<?php

namespace Tests\Feature\Order;

use App\Models\Admin;
use App\Models\Order;
use App\Notifications\OrderPaidNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderPaidEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createOrder(string $status, string $orderId = 'ORD-TEST-001'): Order
    {
        return Order::create([
            'order_number' => $orderId,
            'guest_name' => 'Guest Tester',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest Tester'],
            'courier' => 'jne',
            'courier_service' => 'REG',
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => $status,
        ]);
    }

    public function test_sends_email_notification_to_all_admins_when_order_paid(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create();
        $order = $this->createOrder('pending', 'ORD-PAY-001');

        $response = $this->postJson('/api/webhooks/midtrans', [
            'order_id' => $order->order_number,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '215000.00',
            'signature_key' => 'dummy',
        ]);

        $response->assertStatus(200);
        Notification::assertSentTo($admin, OrderPaidNotification::class);
    }

    public function test_does_not_send_email_notification_for_non_paid_status(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create();
        $order = $this->createOrder('pending', 'ORD-PENDING-001');

        $this->postJson('/api/webhooks/midtrans', [
            'order_id' => $order->order_number,
            'transaction_status' => 'pending',
            'status_code' => '200',
            'gross_amount' => '215000.00',
            'signature_key' => 'dummy',
        ]);

        Notification::assertNothingSent();
    }

    public function test_webhook_succeeds_even_when_no_admin_exists(): void
    {
        Notification::fake();

        $order = $this->createOrder('pending', 'ORD-NOADMIN-001');

        $response = $this->postJson('/api/webhooks/midtrans', [
            'order_id' => $order->order_number,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '215000.00',
            'signature_key' => 'dummy',
        ]);

        $response->assertStatus(200);
        Notification::assertNothingSent();
    }
}
