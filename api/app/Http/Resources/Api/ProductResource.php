<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'images' => collect($this->images ?? [])->map(fn ($path) => asset('uploads/'.$path))->all(),
            'is_active' => $this->is_active,
            'rating_avg' => (float) $this->rating_avg,
            'rating_count' => $this->rating_count,
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
