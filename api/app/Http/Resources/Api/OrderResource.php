<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
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