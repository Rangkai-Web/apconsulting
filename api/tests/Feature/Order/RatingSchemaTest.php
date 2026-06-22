<?php

namespace Tests\Feature\Order;

use App\Models\OrderItem;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_a_star_rating_tied_to_a_purchased_order_item(): void
    {
        $orderItem = OrderItem::factory()->create();

        $rating = Rating::factory()->create([
            'order_item_id' => $orderItem->id,
            'product_id' => $orderItem->product_variant_id ? $orderItem->variant->product_id : null,
            'stars' => 5,
        ]);

        $this->assertEquals($orderItem->id, $rating->orderItem->id);
        $this->assertEquals(5, $rating->stars);
    }

    public function test_rejects_a_second_rating_for_the_same_order_item(): void
    {
        $orderItem = OrderItem::factory()->create();
        Rating::factory()->create(['order_item_id' => $orderItem->id]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Rating::factory()->create(['order_item_id' => $orderItem->id]);
    }
}
