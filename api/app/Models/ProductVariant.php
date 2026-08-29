<?php

namespace App\Models;

use Database\Factories\ProductVariantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'name', 'sku', 'price', 'stock', 'weight', 'image', 'fulfillment_type', 'po_days', 'custom_dp'])]
class ProductVariant extends Model
{
    /** @use HasFactory<ProductVariantFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'weight' => 'integer',
            'po_days' => 'integer',
            'custom_dp' => 'integer',
        ];
    }

    public function getDpAmount(): int
    {
        if ($this->fulfillment_type !== 'pre_order') {
            return 0;
        }

        if ($this->custom_dp !== null) {
            return (int) $this->custom_dp;
        }

        $settings = StoreSetting::first();
        $rules = $settings?->dp_rules ?? [
            ['min_price' => 0, 'max_price' => 500000, 'percentage' => 30],
            ['min_price' => 500000, 'max_price' => 1000000, 'percentage' => 40],
            ['min_price' => 1000000, 'max_price' => null, 'percentage' => 50],
        ];

        foreach ($rules as $rule) {
            $min = (int) ($rule['min_price'] ?? 0);
            $max = isset($rule['max_price']) && $rule['max_price'] !== '' ? (int) $rule['max_price'] : null;
            $percentage = (int) ($rule['percentage'] ?? 0);

            if ($this->price >= $min && ($max === null || $this->price < $max)) {
                return (int) round(($this->price * $percentage) / 100);
            }
        }

        return 0;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
