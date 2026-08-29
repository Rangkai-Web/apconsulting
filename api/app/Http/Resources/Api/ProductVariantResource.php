<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'weight' => $this->weight,
            'image' => $this->image ? Storage::disk('public_uploads')->url($this->image) : null,
            'fulfillment_type' => $this->fulfillment_type ?? 'ready_stock',
            'po_days' => $this->po_days,
            'custom_dp' => $this->custom_dp,
            'dp_amount' => $this->getDpAmount(),
        ];
    }
}
