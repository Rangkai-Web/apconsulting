<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_variant_id' => $this->product_variant_id,
            'product_name' => $this->product_name,
            'variant_name' => $this->variant_name,
            'price' => $this->price,
            'qty' => $this->qty,
            'subtotal' => $this->subtotal,
            'rating' => $this->whenLoaded('rating'),
        ];
    }
}
