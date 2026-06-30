<?php

namespace Tests\Feature\Order;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderResourcePanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_page(): void
    {
        $response = $this->get('/admin/orders');

        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_admin_can_view_order_list(): void
    {
        $admin = Admin::factory()->create();
        Order::create([
            'order_number' => 'ORD-LIST-TEST',
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/orders');

        $response->assertStatus(200)->assertSee('ORD-LIST-TEST');
    }

    public function test_authenticated_admin_can_view_order_detail_with_items_and_address(): void
    {
        $admin = Admin::factory()->create();

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
            'order_number' => 'ORD-VIEW-TEST',
            'guest_name' => 'Guest Tester',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '0899',
            'shipping_address' => [
                'recipient_name' => 'Guest Tester',
                'phone' => '0899',
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Coblong',
                'postal_code' => '40123',
                'full_address' => 'Jl. Contoh No. 1',
            ],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => 'pending',
        ]);
        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'price' => 100000,
            'qty' => 2,
            'subtotal' => 200000,
        ]);

        $response = $this->actingAs($admin, 'admin')->get("/admin/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertSee('ORD-VIEW-TEST')
            ->assertSee('Guest Tester')
            ->assertSee('Sunscreen');
    }

    public function test_member_order_shows_member_badge_and_account_name(): void
    {
        $admin = Admin::factory()->create();
        $member = User::factory()->create(['name' => 'Member Tester']);
        Order::create([
            'order_number' => 'ORD-MEMBER-TEST',
            'user_id' => $member->id,
            'shipping_address' => ['recipient_name' => 'Member Tester'],
            'shipping_cost' => 15000,
            'subtotal' => 200000,
            'total' => 215000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/orders');

        $response->assertStatus(200)
            ->assertSee('ORD-MEMBER-TEST')
            ->assertSee('Member Tester')
            ->assertSee('Member');
    }
}
