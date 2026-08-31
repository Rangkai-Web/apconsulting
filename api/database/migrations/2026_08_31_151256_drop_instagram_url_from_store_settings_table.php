<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fold any existing instagram_url value into the socials list before dropping
        // the column, so admins who already filled it in don't lose the link.
        DB::table('store_settings')->whereNotNull('instagram_url')->where('instagram_url', '!=', '')->get()
            ->each(function ($setting) {
                $socials = json_decode($setting->socials ?? '[]', true) ?: [];

                $hasInstagram = collect($socials)->contains(fn ($social) => ($social['platform'] ?? null) === 'instagram');

                if (! $hasInstagram) {
                    $socials[] = ['platform' => 'instagram', 'url' => $setting->instagram_url];
                }

                DB::table('store_settings')->where('id', $setting->id)->update(['socials' => json_encode($socials)]);
            });

        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn('instagram_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('instagram_url')->nullable()->after('whatsapp_number');
        });
    }
};
