<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSettings = [
            // Contact
            ['key' => 'contact_email', 'value' => 'info@apconsulting.id', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+62 21 1234 5678', 'group' => 'contact'],
            ['key' => 'whatsapp_number', 'value' => '6281234567890', 'group' => 'contact'],
            ['key' => 'office_address', 'value' => 'Jl. Jend. Sudirman No. 123, Jakarta Selatan, DKI Jakarta', 'group' => 'contact'],
            ['key' => 'google_maps_embed_url', 'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2708304910243!2d106.82083!3d-6.22805!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTMnNDEuMCJTIDEwNsKwNDknMTUuMCJF!5e0!3m2!1sen!2sid!4v1625000000000!5m2!1sen!2sid', 'group' => 'contact'],

            // Social Media
            ['key' => 'social_linkedin', 'value' => 'https://linkedin.com/company/ap-consulting', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/apconsulting', 'group' => 'social'],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/apconsulting', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/apconsulting', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@apconsulting', 'group' => 'social'],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
