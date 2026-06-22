<?php

namespace Tests\Feature\Catalog;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_links_an_address_to_a_logged_in_user(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);

        $this->assertEquals($user->id, $address->user->id);
        $this->assertTrue($address->is_default);
    }

    public function test_allows_a_null_user_id_for_addresses_not_tied_to_an_account(): void
    {
        $address = Address::factory()->create(['user_id' => null]);

        $this->assertNull($address->user_id);
    }
}
