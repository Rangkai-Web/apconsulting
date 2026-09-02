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
            'store_name' => 'BbystarGO',
        ]);

        $settings->wasRecentlyCreated = false;

        return (new SettingsResource($settings))
            ->response()
            ->header('Cache-Control', 'public, max-age=300');
    }
}
