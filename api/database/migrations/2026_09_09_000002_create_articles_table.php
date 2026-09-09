<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            // Bahasa Indonesia
            $table->string('title_id');
            $table->string('slug_id')->unique();
            $table->text('excerpt_id')->nullable();
            $table->longText('content_id');
            $table->string('category_id')->nullable();

            // English
            $table->string('title_en')->nullable();
            $table->string('slug_en')->nullable()->unique();
            $table->text('excerpt_en')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('category_en')->nullable();

            $table->string('cover_image')->nullable();
            $table->string('author_name')->default('Admin PCG');
            $table->unsignedInteger('read_time')->default(5);
            $table->date('published_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
