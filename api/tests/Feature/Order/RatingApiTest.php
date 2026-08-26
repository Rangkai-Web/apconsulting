<?php

namespace Tests\Feature\Order;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingApiTest extends TestCase
{
    use RefreshDatabase;

    private function createOrderAndItem(User $user, string $status = 'completed'): array
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
            'user_id' => $user->id,
            'shipping_address' => ['recipient_name' => 'Budi'],
            'courier' => 'jne',
            'courier_service' => 'REG',
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'order_status' => $status,
        ]);

        $item = $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'price' => 100000,
            'qty' => 2,
            'subtotal' => 200000,
        ]);

        return [$item, $product];
    }

    public function test_user_can_rate_completed_order_item(): void
    {
        $user = User::factory()->create();
        [$item, $product] = $this->createOrderAndItem($user, 'completed');

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/order-items/{$item->id}/rating", [
            'stars' => 5,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('rating.stars', 5)
            ->assertJsonPath('rating.rating_avg', 5);

        $this->assertDatabaseHas('ratings', [
            'order_item_id' => $item->id,
            'stars' => 5,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(5, $product->fresh()->rating_avg);
        $this->assertEquals(1, $product->fresh()->rating_count);
    }

    public function test_user_cannot_rate_pending_order_item(): void
    {
        $user = User::factory()->create();
        [$item, $product] = $this->createOrderAndItem($user, 'pending');

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/order-items/{$item->id}/rating", [
            'stars' => 5,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Anda hanya bisa memberi rating pada pesanan yang sudah selesai.');
    }

    public function test_user_cannot_rate_other_users_order_item(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        [$item, $product] = $this->createOrderAndItem($user1, 'completed');

        $response = $this->actingAs($user2, 'sanctum')->postJson("/api/order-items/{$item->id}/rating", [
            'stars' => 5,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_rate_item_twice(): void
    {
        $user = User::factory()->create();
        [$item, $product] = $this->createOrderAndItem($user, 'completed');

        // First rating
        $this->actingAs($user, 'sanctum')->postJson("/api/order-items/{$item->id}/rating", [
            'stars' => 5,
        ])->assertStatus(201);

        // Second rating
        $response = $this->actingAs($user, 'sanctum')->postJson("/api/order-items/{$item->id}/rating", [
            'stars' => 4,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Produk ini sudah diberi rating sebelumnya.');
    }
}
