<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $addresses->map(fn ($addr) => $this->mapAddress($addr)),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'full_address' => ['required', 'string'],
            'is_default' => ['required', 'boolean'],
            'rajaongkir_district_id' => ['required', 'integer', 'exists:rajaongkir_districts,id'],
        ]);

        $user = $request->user();

        // If this is the first address, it should automatically be default
        if ($user->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        if ($validated['is_default']) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create($validated);

        return response()->json([
            'data' => $this->mapAddress($address),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'full_address' => ['required', 'string'],
            'is_default' => ['required', 'boolean'],
            'rajaongkir_district_id' => ['required', 'integer', 'exists:rajaongkir_districts,id'],
        ]);

        if ($validated['is_default']) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        } else {
            // Cannot unset default if it is the only address
            if ($request->user()->addresses()->count() === 1) {
                $validated['is_default'] = true;
            }
        }

        $address->update($validated);

        return response()->json([
            'data' => $this->mapAddress($address),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $wasDefault = $address->is_default;
        $address->delete();

        // If we deleted the default address, make another one default if possible
        if ($wasDefault) {
            $nextAddress = $request->user()->addresses()->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        return response()->json([
            'message' => 'Alamat berhasil dihapus.',
        ]);
    }

    private function mapAddress(Address $address): array
    {
        return [
            'id' => $address->id,
            'label' => $address->label,
            'recipient_name' => $address->recipient_name,
            'phone' => $address->phone,
            'province' => $address->province,
            'city' => $address->city,
            'district' => $address->district,
            'postal_code' => $address->postal_code,
            'full_address' => $address->full_address,
            'is_default' => $address->is_default,
            'rajaongkir_district_id' => $address->rajaongkir_district_id,
        ];
    }
}
