<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('address');
            $table->string('whatsapp_number')->nullable()->after('phone');
            $table->string('instagram_url')->nullable()->after('whatsapp_number');
            $table->string('operating_hours')->nullable()->after('instagram_url');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['phone', 'whatsapp_number', 'instagram_url', 'operating_hours']);
        });
    }
};
