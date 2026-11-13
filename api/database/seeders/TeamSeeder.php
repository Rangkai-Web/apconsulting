<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name' => 'Andry Phangkawira, S.E., Ak., BKP.',
                'position' => 'Partner',
                'photo' => 'https://www.apconsulting.id/images/team/andry.jpeg',
                'bio' => 'Izin Praktik No. KIP-6592/IP.B/PJ/2020 · Anggota IKPI',
                'linkedin_url' => 'https://www.linkedin.com/in/andry-phangkawira/',
                'instagram_url' => null,
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($members as $member) {
            Team::updateOrCreate(
                ['name' => $member['name']],
                $member
            );
        }
    }
}
