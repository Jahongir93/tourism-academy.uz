<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_tour_buildings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_ru')->nullable();
            $table->string('title_en')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_en')->nullable();
            $table->string('short_description')->nullable();
            $table->string('icon')->nullable(); // marker icon
            $table->string('color')->default('#3498db'); // marker color
            $table->decimal('marker_x', 10, 6)->nullable(); // X position for image-based map
            $table->decimal('marker_y', 10, 6)->nullable(); // Y position for image-based map
            $table->decimal('latitude', 10, 8)->nullable(); // For OSM/Leaflet
            $table->decimal('longitude', 11, 8)->nullable(); // For OSM/Leaflet
            $table->unsignedBigInteger('panorama_id')->nullable();
            $table->string('image')->nullable(); // Building photo
            $table->string('working_hours')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('floor_count')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('panorama_id')
                ->references('id')
                ->on('campus_tour_panoramas')
                ->onDelete('set null');

            $table->index(['is_active', 'order']);
        });

        // Add foreign key to panoramas table
        Schema::table('campus_tour_panoramas', function (Blueprint $table) {
            $table->foreign('building_id')
                ->references('id')
                ->on('campus_tour_buildings')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('campus_tour_panoramas', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
        });

        Schema::dropIfExists('campus_tour_buildings');
    }
};
