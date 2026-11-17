<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SettingsSeeder::class,
            FaqSeeder::class,
            ServiceCategorySeeder::class,
            TeamSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
