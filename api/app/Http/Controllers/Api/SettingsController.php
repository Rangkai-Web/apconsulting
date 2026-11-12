<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        $logoPath = $settings['company_logo'] ?? null;
        $logoUrl = $logoPath ? asset('uploads/' . $logoPath) : asset('ap_consulting_logo.jpeg');

        return response()->json([
            'status' => 'success',
            'data' => [
                'company' => [
                    'logo' => $logoUrl,
                ],
                'contact' => [
                    'email' => $settings['contact_email'] ?? 'info@apconsulting.id',
                    'phone' => $settings['contact_phone'] ?? '+62 21 1234 5678',
                    'whatsapp' => $settings['whatsapp_number'] ?? '6281234567890',
                    'address' => $settings['office_address'] ?? '',
                    'maps_embed_url' => $settings['google_maps_embed_url'] ?? '',
                ],
                'social' => [
                    'linkedin' => $settings['social_linkedin'] ?? '',
                    'instagram' => $settings['social_instagram'] ?? '',
                    'tiktok' => $settings['social_tiktok'] ?? '',
                    'threads' => $settings['social_threads'] ?? '',
                    'facebook' => $settings['social_facebook'] ?? '',
                    'twitter' => $settings['social_twitter'] ?? '',
                    'youtube' => $settings['social_youtube'] ?? '',
                ],
            ],
        ]);
    }
}
