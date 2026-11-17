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
        $logoUrl = $logoPath ? asset('uploads/' . $logoPath) : null;

        return response()->json([
            'status' => 'success',
            'data' => [
                'company' => [
                    'logo' => $logoUrl,
                ],
                'contact' => [
                    'email' => $settings['contact_email'] ?? 'contact@apconsulting.id',
                    'phone' => $settings['contact_phone'] ?? '+62 857 5417 2217',
                    'whatsapp' => $settings['whatsapp_number'] ?? '6285754172217',
                    'address' => $settings['office_address'] ?? 'SUN Plaza, Jl. Meruya Ilir Raya No. 20, Jakarta Barat',
                    'maps_embed_url' => $settings['google_maps_embed_url'] ?? 'https://maps.google.com/maps?q=SUN+Plaza+Jl+Meruya+Ilir+Raya+No+20+Jakarta+Barat&output=embed',
                    'branch_address' => $settings['branch_office_address'] ?? ($settings['branch_address'] ?? 'Ruko Alexandrite 3 No. 23, Jl. Boulevard Gading Serpong, Tangerang'),
                    'branch_maps_embed_url' => $settings['branch_google_maps_embed_url'] ?? ($settings['branch_maps_embed_url'] ?? 'https://maps.google.com/maps?q=Ruko+Alexandrite+3+No+23+Jl+Boulevard+Gading+Serpong+Tangerang&output=embed'),
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
