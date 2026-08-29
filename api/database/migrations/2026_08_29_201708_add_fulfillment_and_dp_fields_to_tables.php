<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('fulfillment_type')->default('ready_stock')->after('weight');
            $table->unsignedInteger('po_days')->nullable()->after('fulfillment_type');
            $table->unsignedBigInteger('custom_dp')->nullable()->after('po_days');
        });

        Schema::table('store_settings', function (Blueprint $table) {
            $table->json('dp_rules')->nullable()->after('socials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['fulfillment_type', 'po_days', 'custom_dp']);
        });

        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn('dp_rules');
        });
    }
};
