<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SettingsResource;
use App\Models\StoreSetting;

class SettingsController extends Controller
{
    public function show()
    {
        $settings = StoreSetting::firstOrCreate([], [
            'store_name' => 'Doctor Plant Indonesia',
        ]);

        $settings->wasRecentlyCreated = false;

        return new SettingsResource($settings);
    }
}
