<?php

namespace Tests\Feature\Settings;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_active_payment_methods_sorted_by_sort_order(): void
    {
        PaymentMethod::create([
            'label' => 'ShopeePay',
            'account_number' => '081229475305',
            'account_holder' => 'yuliesarida',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        PaymentMethod::create([
            'label' => 'BCA',
            'account_number' => '8950505266',
            'account_holder' => 'Yulie Sarida H T',
            'qr_image' => 'payment-methods/bca-qr.png',
            'instructions' => 'Transfer sesuai nominal tagihan.',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        PaymentMethod::create([
            'label' => 'Rekening Lama',
            'account_number' => '0000000000',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/payment-methods');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.label', 'BCA')
            ->assertJsonPath('data.0.qr_image', url('uploads/payment-methods/bca-qr.png'))
            ->assertJsonPath('data.1.label', 'ShopeePay')
            ->assertJsonPath('data.1.qr_image', null);
    }
}
