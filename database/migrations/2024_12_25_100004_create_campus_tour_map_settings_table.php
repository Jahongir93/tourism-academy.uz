<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_tour_map_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('map_type', ['image', 'osm', 'google'])->default('image');
            $table->string('base_image')->nullable(); // Campus map image
            $table->integer('image_width')->nullable();
            $table->integer('image_height')->nullable();
            $table->decimal('center_lat', 10, 8)->nullable();
            $table->decimal('center_lng', 11, 8)->nullable();
            $table->integer('default_zoom')->default(16);
            $table->integer('min_zoom')->default(14);
            $table->integer('max_zoom')->default(19);
            $table->string('tile_url')->nullable(); // Custom OSM tiles
            $table->json('bounds')->nullable(); // Map bounds
            $table->json('settings')->nullable(); // Additional settings
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_tour_map_settings');
    }
};
