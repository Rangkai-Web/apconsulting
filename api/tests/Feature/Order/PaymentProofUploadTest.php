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

    public function test_member_cannot_upload_proof_on_expired_order(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user->id, null);
        $order->update(['order_status' => 'expired']);

        $response = $this->actingAs($user, 'sanctum')->postJson(
            "/api/orders/{$order->order_number}/payment-proof",
            ['proof' => UploadedFile::fake()->image('bukti.jpg')->size(500)],
        );

        $response->assertStatus(422);
        $fresh = $order->fresh();
        $this->assertEquals('unpaid', $fresh->payment_status);
        $this->assertNull($fresh->payment_proof);
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
