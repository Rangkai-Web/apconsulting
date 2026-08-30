<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $remainingBalance = 0;
        if ($this->relationLoaded('items')) {
            foreach ($this->items as $item) {
                if ($item->full_price && $item->full_price > $item->price) {
                    $remainingBalance += ($item->full_price - $item->price) * $item->qty;
                }
            }
        }

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
            'payment_proof' => $this->payment_proof ? Storage::disk('public_uploads')->url($this->payment_proof) : null,
            'dp_payment_proof' => $this->dp_payment_proof ? Storage::disk('public_uploads')->url($this->dp_payment_proof) : null,
            'payment_rejected_reason' => $this->payment_rejected_reason,
            'courier' => $this->courier,
            'courier_service' => $this->courier_service,
            'shipping_cost' => $this->shipping_cost,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'remaining_balance' => $remainingBalance,
            'tracking_number' => $this->tracking_number,
            'shipping_address' => $this->shipping_address,
            'created_at' => $this->created_at,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
