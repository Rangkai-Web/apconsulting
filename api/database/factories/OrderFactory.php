<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . fake()->unique()->numerify('########'),
            'user_id' => User::factory(),
            'shipping_address' => [
                'recipient_name' => fake()->name(),
                'phone' => fake()->numerify('08##########'),
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Coblong',
                'postal_code' => fake()->numerify('40###'),
                'full_address' => fake()->streetAddress(),
            ],
            'subtotal' => 50000,
            'total' => 60000,
            'status' => 'pending',
        ];
    }
}
