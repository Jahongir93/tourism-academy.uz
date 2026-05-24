<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pages table
        Schema::create('pb_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('status')->default('draft'); // draft, published
            $table->json('settings')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->index('slug');
            $table->index('status');
        });

        // Templates table
        Schema::create('pb_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('content');
            $table->boolean('is_premium')->default(false);
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            $table->index('category');
        });

        // Page sections/rows
        Schema::create('pb_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pb_pages')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->string('type')->default('row'); // row, section, container
            $table->json('settings')->nullable(); // background, padding, margin, etc.
            $table->json('responsive_settings')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            $table->index(['page_id', 'order']);
        });

        // Columns within sections
        Schema::create('pb_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('pb_sections')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->integer('width')->default(12); // 1-12 grid system
            $table->json('responsive_width')->nullable(); // mobile, tablet widths
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->index(['section_id', 'order']);
        });

        // Elements/Widgets
        Schema::create('pb_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('column_id')->constrained('pb_columns')->onDelete('cascade');
            $table->string('type'); // text, image, button, video, etc.
            $table->integer('order')->default(0);
            $table->json('content');
            $table->json('settings')->nullable();
            $table->json('animations')->nullable();
            $table->json('responsive_settings')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            $table->index(['column_id', 'order']);
            $table->index('type');
        });

        // Element types/widgets library
        Schema::create('pb_element_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('category'); // basic, media, advanced, etc.
            $table->json('default_settings');
            $table->json('fields_schema'); // Field definitions for settings panel
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('category');
        });

        // Saved blocks (reusable components)
        Schema::create('pb_saved_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->json('content');
            $table->string('thumbnail')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_global')->default(false);
            $table->timestamps();
        });

        // Revision history
        Schema::create('pb_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pb_pages')->onDelete('cascade');
            $table->json('content');
            $table->string('revision_type')->default('auto'); // auto, manual
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->index(['page_id', 'created_at']);
        });

        // Custom CSS/JS per page
        Schema::create('pb_page_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pb_pages')->onDelete('cascade');
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();
            $table->json('external_assets')->nullable(); // External CSS/JS links
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pb_page_assets');
        Schema::dropIfExists('pb_revisions');
        Schema::dropIfExists('pb_saved_blocks');
        Schema::dropIfExists('pb_element_types');
        Schema::dropIfExists('pb_elements');
        Schema::dropIfExists('pb_columns');
        Schema::dropIfExists('pb_sections');
        Schema::dropIfExists('pb_templates');
        Schema::dropIfExists('pb_pages');
    }
};