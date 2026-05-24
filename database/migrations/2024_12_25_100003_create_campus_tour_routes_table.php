<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_tour_routes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_ru')->nullable();
            $table->string('title_en')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_en')->nullable();
            $table->enum('type', ['bus', 'metro', 'walk', 'taxi', 'train', 'other'])->default('bus');
            $table->string('icon')->nullable();
            $table->string('color')->default('#3498db');
            $table->string('start_point');
            $table->string('start_point_ru')->nullable();
            $table->string('start_point_en')->nullable();
            $table->string('end_point');
            $table->string('end_point_ru')->nullable();
            $table->string('end_point_en')->nullable();
            $table->string('duration')->nullable(); // e.g., "15 daqiqa"
            $table->decimal('distance', 8, 2)->nullable(); // km
            $table->decimal('price', 10, 2)->nullable(); // UZS
            $table->text('map_embed_url')->nullable(); // Google Maps or OSM embed
            $table->text('directions')->nullable(); // Step by step directions
            $table->text('directions_ru')->nullable();
            $table->text('directions_en')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'type', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_tour_routes');
    }
};
