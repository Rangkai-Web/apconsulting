<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RatingController extends Controller
{
    public function rate(Request $request, $orderItemId)
    {
        $validated = $request->validate([
            'stars' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $orderItem = OrderItem::with(['order', 'variant.product'])->findOrFail($orderItemId);

        // Verify ownership
        if ($orderItem->order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Verify order is completed
        if ($orderItem->order->order_status !== 'completed') {
            return response()->json(['message' => 'Anda hanya bisa memberi rating pada pesanan yang sudah selesai.'], 422);
        }

        // Verify not already rated
        if ($orderItem->rating()->exists()) {
            return response()->json(['message' => 'Produk ini sudah diberi rating sebelumnya.'], 422);
        }

        if (! $orderItem->variant) {
            return response()->json(['message' => 'Varian produk tidak ditemukan.'], 404);
        }

        $product = $orderItem->variant->product;

        // Create rating
        Rating::create([
            'order_item_id' => $orderItem->id,
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'stars' => $validated['stars'],
        ]);

        // Recalculate average and count on product
        $ratings = Rating::where('product_id', $product->id)->get();
        $product->rating_avg = $ratings->avg('stars');
        $product->rating_count = $ratings->count();
        $product->save();

        return response()->json([
            'message' => 'Terima kasih atas rating Anda!',
            'rating' => [
                'stars' => $validated['stars'],
                'rating_avg' => (float) $product->rating_avg,
                'rating_count' => $product->rating_count,
            ]
        ], 201);
    }
}
