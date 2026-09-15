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
                'name' => 'Darren Phangkawira',
                'position' => 'Managing Partner',
                'photo' => '/images/about/team/darren.webp',
                'bio' => 'Senior consultant with extensive experience in corporate tax planning and financial advisory.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Jacob Phangkawira',
                'position' => 'Tax Partner',
                'photo' => '/images/about/team/jacob.webp',
                'bio' => 'Specializing in corporate tax compliance, audit defense, and tax risk mitigation.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Floyd Miles',
                'position' => 'Senior Accounting Manager',
                'photo' => '/images/about/team/floyd.webp',
                'bio' => 'Over 10 years of experience managing financial reporting, bookkeeping, and cash flow analysis.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Cody Fisher',
                'position' => 'Business Advisory Consultant',
                'photo' => '/images/about/team/cody.webp',
                'bio' => 'Focusing on business administration support and strategic corporate budgeting.',
                'sort_order' => 4,
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
