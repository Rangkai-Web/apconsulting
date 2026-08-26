<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
            'payment_proof' => $this->payment_proof ? Storage::disk('public_uploads')->url($this->payment_proof) : null,
            'payment_rejected_reason' => $this->payment_rejected_reason,
            'courier' => $this->courier,
            'courier_service' => $this->courier_service,
            'shipping_cost' => $this->shipping_cost,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'tracking_number' => $this->tracking_number,
            'shipping_address' => $this->shipping_address,
            'created_at' => $this->created_at,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
