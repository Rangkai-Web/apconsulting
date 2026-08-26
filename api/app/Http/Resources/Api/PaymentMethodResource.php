<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'account_number' => $this->account_number,
            'account_holder' => $this->account_holder,
            'qr_image' => $this->qr_image ? Storage::disk('public_uploads')->url($this->qr_image) : null,
            'instructions' => $this->instructions,
            'sort_order' => $this->sort_order,
        ];
    }
}
