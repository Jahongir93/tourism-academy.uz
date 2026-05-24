<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cms_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // slider, gallery, counter, testimonial, etc.
            $table->json('content'); // widget-specific data
            $table->json('settings'); // display settings
            $table->string('position')->nullable(); // sidebar, footer, etc.
            $table->integer('order_position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('pages')->nullable(); // specific pages to show on
            $table->json('exclude_pages')->nullable(); // pages to exclude from
            $table->timestamps();
            
            $table->index('type');
            $table->index('position');
            $table->index('is_active');
        });
        
        Schema::create('cms_sliders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('slides'); // array of slide data
            $table->json('settings'); // autoplay, duration, effects, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
        });
        
        Schema::create('cms_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('grid'); // grid, masonry, carousel
            $table->json('images'); // array of image data
            $table->json('settings'); // columns, spacing, lightbox, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_galleries');
        Schema::dropIfExists('cms_sliders');
        Schema::dropIfExists('cms_widgets');
    }
};