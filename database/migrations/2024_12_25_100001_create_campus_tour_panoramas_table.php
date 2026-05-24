<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_tour_panoramas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_ru')->nullable();
            $table->string('title_en')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_en')->nullable();
            $table->string('image_path');
            $table->string('thumbnail_path')->nullable();
            $table->unsignedBigInteger('building_id')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('hotspots')->nullable(); // For linking panoramas
            $table->json('metadata')->nullable(); // Additional settings
            $table->timestamps();

            $table->index(['is_active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_tour_panoramas');
    }
};
