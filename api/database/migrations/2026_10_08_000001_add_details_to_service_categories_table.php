<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->text('description_id')->nullable()->after('name_en');
            $table->text('description_en')->nullable()->after('description_id');
            $table->string('cta_text_id')->nullable()->after('description_en');
            $table->string('cta_text_en')->nullable()->after('cta_text_id');
            $table->json('features_id')->nullable()->after('cta_text_en');
            $table->json('features_en')->nullable()->after('features_id');
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropColumn([
                'description_id',
                'description_en',
                'cta_text_id',
                'cta_text_en',
                'features_id',
                'features_en',
            ]);
        });
    }
};
