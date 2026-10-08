<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('threads_url')->nullable()->after('tiktok_url');
            $table->string('facebook_url')->nullable()->after('threads_url');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('youtube_url')->nullable()->after('twitter_url');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['threads_url', 'facebook_url', 'twitter_url', 'youtube_url']);
        });
    }
};
