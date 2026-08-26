<?php

namespace Tests\Feature\Order;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\RajaongkirCity;
use App\Models\RajaongkirDistrict;
use App\Models\RajaongkirProvince;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    private function createVariant(int $stock = 10, int $price = 100000): ProductVariant
    {
        $category = Category::create(['name' => 'Skincare', 'slug' => 'skincare']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sunscreen',
            'slug' => 'sunscreen',
            'description' => 'Test sunscreen',
            'images' => ['sunscreen.png'],
            'is_active' => true,
        ]);

        return ProductVariant::create([
            'product_id' => $product->id,
            'name' => '50ml',
            'sku' => 'SUN-50',
            'price' => $price,
            'stock' => $stock,
            'weight' => 200,
        ]);
    }

    private function seedShippingFixtures(int $destinationDistrictId, string $courier, string $service, int $cost): void
    {
        StoreSetting::create(['store_name' => 'Doctor Plant Indonesia', 'rajaongkir_origin_district_id' => 501]);
        RajaongkirProvince::create(['id' => 1, 'name' => 'Jawa Barat']);
        RajaongkirCity::create(['id' => 1, 'province_id' => 1, 'name' => 'Bandung']);
        RajaongkirDistrict::create(['id' => $destinationDistrictId, 'city_id' => 1, 'name' => 'Coblong']);

        Http::fake([
            '*/calculate/district/domestic-cost' => Http::response([
                'meta' => ['code' => 200, 'message' => 'success'],
                'data' => [
                    ['code' => $courier, 'service' => $service, 'description' => 'Layanan', 'cost' => $cost, 'etd' => '2-3'],
                ],
            ], 200),
        ]);
    }

    public function test_member_can_checkout_successfully(): void
    {
        $user = User::factory()->create();
        $variant = $this->createVariant(5);
        $this->seedShippingFixtures(501001, 'jne', 'REG', 15000);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 2],
            ],
            'shipping_address' => [
                'recipient_name' => 'Budi',
                'phone' => '081234',
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Coblong',
                'postal_code' => '40135',
                'full_address' => 'Jl. Dago 10',
                'rajaongkir_district_id' => 501001,
            ],
            'courier' => 'jne',
            'courier_service' => 'REG',
            'shipping_cost' => 15000,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['order_number', 'snap_token']);

        $this->assertEquals(3, $variant->fresh()->stock);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total' => 215000, // 200000 + 15000
            'status' => 'pending',
        ]);
    }

    public function test_guest_can_checkout_successfully(): void
    {
        $variant = $this->createVariant(5);
        $this->seedShippingFixtures(501002, 'jnt', 'EZ', 12000);

        $response = $this->postJson('/api/orders', [
            'guest_name' => 'Guest User',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899999',
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 1],
            ],
            'shipping_address' => [
                'recipient_name' => 'Guest Recipient',
                'phone' => '0899999',
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Coblong',
                'postal_code' => '40135',
                'full_address' => 'Jl. Dago 20',
                'rajaongkir_district_id' => 501002,
            ],
            'courier' => 'jnt',
            'courier_service' => 'EZ',
            'shipping_cost' => 12000,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['order_number', 'snap_token']);

        $this->assertEquals(4, $variant->fresh()->stock);

        $this->assertDatabaseHas('orders', [
            'user_id' => null,
            'guest_name' => 'Guest User',
            'guest_email' => 'guest@example.com',
            'total' => 112000, // 100000 + 12000
        ]);
    }

    public function test_checkout_fails_if_insufficient_stock(): void
    {
        $user = User::factory()->create();
        $variant = $this->createVariant(1);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 2],
            ],
            'shipping_address' => [
                'recipient_name' => 'Budi',
                'phone' => '081234',
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Coblong',
                'postal_code' => '40135',
                'full_address' => 'Jl. Dago 10',
            ],
            'courier' => 'jne',
            'courier_service' => 'REG',
            'shipping_cost' => 15000,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);

        $this->assertEquals(1, $variant->fresh()->stock);
    }

    public function test_user_can_lookup_guest_order(): void
    {
        $variant = $this->createVariant();

        $order = Order::create([
            'order_number' => 'ORD-TEST-123',
            'guest_name' => 'Guest User',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899999',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'courier' => 'jne',
            'courier_service' => 'REG',
            'shipping_cost' => 15000,
            'subtotal' => 100000,
            'total' => 115000,
            'status' => 'pending',
        ]);

        $response = $this->getJson('/api/orders/lookup?order_number=ORD-TEST-123&email=guest@example.com');

        $response->assertStatus(200)
            ->assertJsonPath('data.order_number', 'ORD-TEST-123')
            ->assertJsonMissingPath('data.guest_email')
            ->assertJsonMissingPath('data.midtrans_order_id');
    }
}
