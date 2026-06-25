<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'data' => $orders->items(),
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
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

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
            'data' => $order,
        ]);
    }

    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $order = Order::with('items')
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
            'data' => $order,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $rules = [
            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'courier' => ['required', 'string'],
            'courier_service' => ['required', 'string'],
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

                    $itemPrice = $variant->price;
                    $itemSubtotal = $itemPrice * $itemInput['qty'];
                    $subtotal += $itemSubtotal;

                    $orderItemsData[] = [
                        'product_variant_id' => $variant->id,
                        'product_name' => $variant->product->name,
                        'variant_name' => $variant->name,
                        'price' => $itemPrice,
                        'qty' => $itemInput['qty'],
                        'subtotal' => $itemSubtotal,
                    ];
                }

                $total = $subtotal + $validated['shipping_cost'];
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
                    'shipping_cost' => $validated['shipping_cost'],
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'status' => 'pending',
                ]);

                // Save Order Items
                foreach ($orderItemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                return $order;
            });

            // Call Midtrans Snap API
            $snapToken = $this->getMidtransSnapToken($order);

            return response()->json([
                'order_number' => $order->order_number,
                'snap_token' => $snapToken,
            ], 201);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Gagal membuat pesanan: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses pesanan Anda, coba lagi.'], 500);
        }
    }

    public function requestSnapToken(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Access check
        if ($request->user()) {
            if ($order->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
        } else {
            if ($order->user_id !== null) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Pesanan ini sudah dibayar atau dibatalkan.'], 422);
        }

        // Generate a new Midtrans order ID attempt since Snap requires a unique ID per try
        $attempt = 1;
        if ($order->midtrans_order_id && str_contains($order->midtrans_order_id, '-')) {
            $parts = explode('-', $order->midtrans_order_id);
            $attempt = (int) end($parts) + 1;
        }
        $order->midtrans_order_id = "{$order->order_number}-{$attempt}";
        $order->save();

        $snapToken = $this->getMidtransSnapToken($order);

        return response()->json([
            'snap_token' => $snapToken,
        ]);
    }

    private function getMidtransSnapToken(Order $order): string
    {
        $serverKey = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production');

        // Increment attempt on midtrans_order_id if not set yet
        if (! $order->midtrans_order_id) {
            $order->midtrans_order_id = "{$order->order_number}-1";
            $order->save();
        }

        // Mock fallback for development if keys are missing
        if (empty($serverKey)) {
            Log::info('Midtrans server key is empty. Using mock snap_token for dev.');
            return 'mock-snap-token-' . Str::random(10);
        }

        $url = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $customerEmail = $order->user ? $order->user->email : $order->guest_email;
        $customerName = $order->user ? $order->user->name : $order->guest_name;
        $customerPhone = $order->user ? ($order->user->phone ?? $order->shipping_address['phone']) : $order->guest_phone;

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->post($url, [
                    'transaction_details' => [
                        'order_id' => $order->midtrans_order_id,
                        'gross_amount' => $order->total,
                    ],
                    'credit_card' => [
                        'secure' => true,
                    ],
                    'customer_details' => [
                        'first_name' => $customerName,
                        'email' => $customerEmail,
                        'phone' => $customerPhone,
                        'billing_address' => [
                            'first_name' => $order->shipping_address['recipient_name'],
                            'phone' => $order->shipping_address['phone'],
                            'address' => $order->shipping_address['full_address'],
                            'city' => $order->shipping_address['city'],
                            'postal_code' => $order->shipping_address['postal_code'],
                        ],
                        'shipping_address' => [
                            'first_name' => $order->shipping_address['recipient_name'],
                            'phone' => $order->shipping_address['phone'],
                            'address' => $order->shipping_address['full_address'],
                            'city' => $order->shipping_address['city'],
                            'postal_code' => $order->shipping_address['postal_code'],
                        ]
                    ]
                ]);

            if ($response->failed()) {
                throw new \RuntimeException('Midtrans request failed with status ' . $response->status() . ': ' . $response->body());
            }

            return $response->json('token');
        } catch (\Throwable $e) {
            Log::error('Gagal mendapatkan token Midtrans Snap: ' . $e->getMessage());
            // Fallback for offline mode in local testing to not block developers
            if (config('app.env') === 'local' || config('app.env') === 'testing') {
                return 'mock-snap-token-' . Str::random(10);
            }
            throw $e;
        }
    }
}
