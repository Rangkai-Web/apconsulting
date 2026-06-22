<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rajaongkir_provinces', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('rajaongkir_cities', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('province_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('rajaongkir_provinces')->cascadeOnDelete();
        });

        Schema::create('rajaongkir_districts', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('city_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('rajaongkir_cities')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rajaongkir_districts');
        Schema::dropIfExists('rajaongkir_cities');
        Schema::dropIfExists('rajaongkir_provinces');
    }
};
