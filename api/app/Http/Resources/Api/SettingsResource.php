<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SettingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'store_name' => $this->store_name,
            'logo' => $this->logo ? Storage::disk('public_uploads')->url($this->logo) : null,
            'address' => $this->address,
            'phone' => $this->phone,
            'whatsapp_number' => $this->whatsapp_number,
            'line_url' => $this->line_url,
            'operating_hours' => $this->operating_hours,
            'socials' => $this->socials ?? [],
            'dp_rules' => $this->dp_rules ?? [],
        ];
    }
}
