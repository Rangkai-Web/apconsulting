<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(private RajaOngkirService $rajaOngkir)
    {
    }

    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items.rating')->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'data' => OrderResource::collection($orders->items()),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    public function show(Request $request, $orderNumber)
    {
        $order = Order::with('items.rating')->where('order_number', $orderNumber)->firstOrFail();

        // If authenticated, make sure the order belongs to them
        if ($request->user()) {
            if ($order->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
        } else {
            // For guests, we require a secure query lookup in another method, or check if email matches
            if ($order->user_id !== null) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
        }

        return response()->json([
            'data' => new OrderResource($order),
        ]);
    }

    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $order = Order::with('items.rating')
            ->where('order_number', $validated['order_number'])
            ->where(function ($query) use ($validated) {
                $query->where('guest_email', $validated['email'])
                    ->orWhereHas('user', function ($uQuery) use ($validated) {
                        $uQuery->where('email', $validated['email']);
                    });
            })
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan atau email tidak cocok.'], 404);
        }

        return response()->json([
            'data' => new OrderResource($order),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user('sanctum');

        $rules = [
            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'courier' => ['required', 'string', 'max:255'],
            'courier_service' => ['required', 'string', 'max:255'],
            'shipping_cost' => ['required', 'integer', 'min:0'],
        ];

        if ($user) {
            $rules['address_id'] = ['required_without:shipping_address', 'nullable', 'integer', 'exists:addresses,id'];
            $rules['shipping_address'] = ['nullable', 'array'];
        } else {
            $rules['guest_name'] = ['required', 'string', 'max:255'];
            $rules['guest_email'] = ['required', 'email', 'max:255'];
            $rules['guest_phone'] = ['required', 'string', 'max:20'];
            $rules['shipping_address'] = ['required', 'array'];
        }

        // Shared address sub-validation rules if shipping_address is provided
        $addressRules = [
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'full_address' => ['required', 'string'],
            'rajaongkir_district_id' => ['nullable', 'integer', 'exists:rajaongkir_districts,id'],
        ];

        $validated = $request->validate($rules);

        if ($request->has('shipping_address') && is_array($request->input('shipping_address'))) {
            $request->validate(array_combine(
                array_map(fn ($k) => "shipping_address.{$k}", array_keys($addressRules)),
                $addressRules
            ));
        }

        try {
            $order = DB::transaction(function () use ($user, $validated, $request) {
                // Determine shipping address snapshot
                $shippingAddressSnapshot = null;
                if ($user && isset($validated['address_id'])) {
                    $address = Address::where('user_id', $user->id)->findOrFail($validated['address_id']);
                    $shippingAddressSnapshot = [
                        'recipient_name' => $address->recipient_name,
                        'phone' => $address->phone,
                        'province' => $address->province,
                        'city' => $address->city,
                        'district' => $address->district,
                        'postal_code' => $address->postal_code,
                        'full_address' => $address->full_address,
                        'rajaongkir_district_id' => $address->rajaongkir_district_id,
                    ];
                } else {
                    $shippingAddressSnapshot = $validated['shipping_address'];
                }

                // Process items, verify stock and calculate subtotal
                $orderItemsData = [];
                $subtotal = 0;
                $totalWeight = 0;

                foreach ($validated['items'] as $itemInput) {
                    // Lock for update to prevent race condition
                    $variant = ProductVariant::with('product')->lockForUpdate()->findOrFail($itemInput['variant_id']);

                    if ($variant->stock < $itemInput['qty']) {
                        throw ValidationException::withMessages([
                            'items' => ["Stok untuk produk '{$variant->product->name} ({$variant->name})' tidak mencukupi. Tersedia: {$variant->stock}."],
                        ]);
                    }

                    // Decrement stock
                    $variant->decrement('stock', $itemInput['qty']);

                    $itemPrice = ($variant->fulfillment_type === 'pre_order' && $variant->getDpAmount() > 0)
                        ? $variant->getDpAmount()
                        : $variant->price;
                    $itemSubtotal = $itemPrice * $itemInput['qty'];
                    $subtotal += $itemSubtotal;
                    $totalWeight += $variant->weight * $itemInput['qty'];

                    $orderItemsData[] = [
                        'product_variant_id' => $variant->id,
                        'product_name' => $variant->product->name,
                        'variant_name' => $variant->name,
                        'price' => $itemPrice,
                        'qty' => $itemInput['qty'],
                        'subtotal' => $itemSubtotal,
                    ];
                }

                // Shipping cost is never trusted from the client — recompute it the same way
                // /api/shipping/cost does (same cache key), so a legitimate "Cek Ongkir" call
                // made earlier in checkout is reused instead of hitting RajaOngkir again.
                $shippingCost = $this->verifiedShippingCost(
                    isset($shippingAddressSnapshot['rajaongkir_district_id'])
                        ? (int) $shippingAddressSnapshot['rajaongkir_district_id']
                        : null,
                    $totalWeight,
                    $validated['courier'],
                    $validated['courier_service'],
                );

                $total = $subtotal + $shippingCost;
                $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

                // Save Order
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => $user ? $user->id : null,
                    'guest_name' => $user ? null : $validated['guest_name'],
                    'guest_email' => $user ? null : $validated['guest_email'],
                    'guest_phone' => $user ? null : $validated['guest_phone'],
                    'shipping_address' => $shippingAddressSnapshot,
                    'courier' => $validated['courier'],
                    'courier_service' => $validated['courier_service'],
                    'shipping_cost' => $shippingCost,
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'order_status' => 'pending',
                ]);

                // Save Order Items
                foreach ($orderItemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                return $order;
            });

            return response()->json([
                'order_number' => $order->order_number,
            ], 201);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Gagal membuat pesanan: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses pesanan Anda, coba lagi.'], 500);
        }
    }

    public function uploadPaymentProof(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        if ($order->user_id !== null) {
            $user = $request->user('sanctum');
            abort_if(! $user || $order->user_id !== $user->id, 403, 'Unauthorized.');
        } else {
            $validatedEmail = $request->validate(['guest_email' => ['required', 'email']]);
            abort_if($order->guest_email !== $validatedEmail['guest_email'], 403, 'Unauthorized.');
        }

        abort_if(
            ! in_array($order->payment_status, ['unpaid', 'rejected']),
            422,
            'Bukti pembayaran sudah diunggah untuk pesanan ini.',
        );

        abort_if(
            $order->order_status === 'expired',
            422,
            'Pesanan sudah kedaluwarsa dan tidak dapat diisi bukti pembayaran.',
        );

        $request->validate([
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $path = $request->file('proof')->store('payment-proofs', 'public_uploads');

        $order->update([
            'payment_proof' => $path,
            'payment_proof_uploaded_at' => now(),
            'payment_status' => 'awaiting_verification',
            'payment_rejected_reason' => null,
        ]);

        return response()->json(['data' => new OrderResource($order)]);
    }

    private function verifiedShippingCost(?int $destinationDistrictId, int $totalWeight, string $courier, string $courierService): int
    {
        if (! $destinationDistrictId) {
            throw ValidationException::withMessages([
                'shipping_address' => ['Kecamatan tujuan pengiriman tidak valid untuk cek ongkir.'],
            ]);
        }

        $origin = StoreSetting::firstOrCreate([], ['store_name' => 'BbystarGO'])->rajaongkir_origin_district_id;

        if (! $origin) {
            throw ValidationException::withMessages([
                'shipping_cost' => ['Toko belum mengatur lokasi asal pengiriman.'],
            ]);
        }

        $cacheKey = "shipping-cost:{$origin}:{$destinationDistrictId}:{$totalWeight}";
        $ttl = now()->addHours((int) config('rajaongkir.cache_ttl_hours'));

        try {
            $couriers = Cache::remember($cacheKey, $ttl, fn () => $this->rajaOngkir->calculateDomesticCost(
                (int) $origin,
                $destinationDistrictId,
                $totalWeight,
            ));
        } catch (\Throwable $e) {
            Log::error('RajaOngkir cost verification failed: '.$e->getMessage());

            throw ValidationException::withMessages([
                'shipping_cost' => ['Gagal memverifikasi ongkir, coba lagi.'],
            ]);
        }

        $match = collect($couriers)->first(
            fn ($c) => $c['courier'] === $courier && $c['service'] === $courierService,
        );

        if (! $match) {
            throw ValidationException::withMessages([
                'courier_service' => ['Kurir/layanan yang dipilih tidak lagi tersedia, cek ongkir ulang.'],
            ]);
        }

        return (int) $match['cost'];
    }
}
