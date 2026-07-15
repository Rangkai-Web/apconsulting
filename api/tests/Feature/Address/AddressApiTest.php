<?php

namespace Tests\Feature\Address;

use App\Models\Address;
use App\Models\RajaongkirCity;
use App\Models\RajaongkirDistrict;
use App\Models\RajaongkirProvince;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressApiTest extends TestCase
{
    use RefreshDatabase;

    private function createDistrict(int $id = 501): RajaongkirDistrict
    {
        $province = RajaongkirProvince::create(['id' => 9, 'name' => 'Jawa Barat']);
        $city = RajaongkirCity::create(['id' => 23, 'province_id' => $province->id, 'name' => 'Bandung']);
        return RajaongkirDistrict::create(['id' => $id, 'city_id' => $city->id, 'name' => 'Coblong']);
    }

    public function test_guest_cannot_access_address_endpoints(): void
    {
        $this->getJson('/api/addresses')->assertStatus(401);
        $this->postJson('/api/addresses', [])->assertStatus(401);
        $this->putJson('/api/addresses/1', [])->assertStatus(401);
        $this->deleteJson('/api/addresses/1')->assertStatus(401);
    }

    public function test_user_can_list_their_addresses(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $district = $this->createDistrict();

        $address1 = Address::create([
            'user_id' => $user->id,
            'label' => 'Rumah',
            'recipient_name' => 'Budi',
            'phone' => '081234',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40135',
            'full_address' => 'Jl. Coblong No. 1',
            'is_default' => true,
            'rajaongkir_district_id' => $district->id,
        ]);

        $address2 = Address::create([
            'user_id' => $otherUser->id,
            'label' => 'Kantor',
            'recipient_name' => 'Ali',
            'phone' => '089999',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40135',
            'full_address' => 'Jl. Coblong No. 2',
            'is_default' => true,
            'rajaongkir_district_id' => $district->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/addresses');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $address1->id)
            ->assertJsonPath('data.0.label', 'Rumah')
            ->assertJsonPath('data.0.recipient_name', 'Budi')
            ->assertJsonPath('data.0.rajaongkir_district_id', $district->id);
    }

    public function test_user_can_create_address_and_automatically_sets_default(): void
    {
        $user = User::factory()->create();
        $district = $this->createDistrict();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/addresses', [
            'label' => 'Apartment',
            'recipient_name' => 'Budi Santoso',
            'phone' => '0812345678',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40135',
            'full_address' => 'Apartment Dago Suite No. 10B',
            'is_default' => false, // will be auto-set to true because it is the first address
            'rajaongkir_district_id' => $district->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.is_default', true)
            ->assertJsonPath('data.label', 'Apartment')
            ->assertJsonPath('data.recipient_name', 'Budi Santoso');

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'label' => 'Apartment',
            'is_default' => true,
            'rajaongkir_district_id' => $district->id,
        ]);
    }

    public function test_user_can_create_new_default_address_and_unsets_previous_default(): void
    {
        $user = User::factory()->create();
        $district = $this->createDistrict();

        $oldAddress = Address::create([
            'user_id' => $user->id,
            'label' => 'Rumah',
            'recipient_name' => 'Budi',
            'phone' => '081234',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40135',
            'full_address' => 'Jl. Coblong No. 1',
            'is_default' => true,
            'rajaongkir_district_id' => $district->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/addresses', [
            'label' => 'Kantor',
            'recipient_name' => 'Budi Santoso',
            'phone' => '0812345678',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40135',
            'full_address' => 'Jl. Ganesha No. 10',
            'is_default' => true,
            'rajaongkir_district_id' => $district->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.is_default', true);

        $this->assertFalse($oldAddress->fresh()->is_default);
    }

    public function test_user_can_update_address(): void
    {
        $user = User::factory()->create();
        $district = $this->createDistrict();

        $address = Address::create([
            'user_id' => $user->id,
            'label' => 'Rumah',
            'recipient_name' => 'Budi',
            'phone' => '081234',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40135',
            'full_address' => 'Jl. Coblong No. 1',
            'is_default' => true,
            'rajaongkir_district_id' => $district->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/addresses/{$address->id}", [
            'label' => 'Rumah Baru',
            'recipient_name' => 'Budi Santoso',
            'phone' => '0812345678',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40135',
            'full_address' => 'Jl. Coblong No. 100',
            'is_default' => true,
            'rajaongkir_district_id' => $district->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.label', 'Rumah Baru')
            ->assertJsonPath('data.full_address', 'Jl. Coblong No. 100');

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'label' => 'Rumah Baru',
            'full_address' => 'Jl. Coblong No. 100',
        ]);
    }

    public function test_user_can_delete_address_and_auto_transfers_default(): void
    {
        $user = User::factory()->create();
        $district = $this->createDistrict();

        $address1 = Address::create([
            'user_id' => $user->id,
            'label' => 'Rumah',
            'recipient_name' => 'Budi',
            'phone' => '081234',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40135',
            'full_address' => 'Jl. Coblong No. 1',
            'is_default' => true,
            'rajaongkir_district_id' => $district->id,
        ]);

        $address2 = Address::create([
            'user_id' => $user->id,
            'label' => 'Kantor',
            'recipient_name' => 'Budi',
            'phone' => '081234',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40135',
            'full_address' => 'Jl. Ganesha No. 10',
            'is_default' => false,
            'rajaongkir_district_id' => $district->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/addresses/{$address1->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('addresses', ['id' => $address1->id]);
        $this->assertTrue($address2->fresh()->is_default);
    }
}
