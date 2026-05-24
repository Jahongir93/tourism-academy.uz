<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title_uz');
            $table->string('title_ru')->nullable();
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('meta_description_uz')->nullable();
            $table->text('meta_description_ru')->nullable();
            $table->text('meta_description_en')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->json('content_uz')->nullable();
            $table->json('content_ru')->nullable();
            $table->json('content_en')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('template')->default('default');
            $table->foreignId('parent_id')->nullable()->constrained('cms_pages')->onDelete('cascade');
            $table->integer('order_position')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_homepage')->default(false);
            $table->boolean('show_in_menu')->default(true);
            $table->boolean('show_in_footer')->default(false);
            $table->json('custom_css')->nullable();
            $table->json('custom_js')->nullable();
            $table->json('page_builder_data')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamp('published_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('status');
            $table->index('is_homepage');
            $table->index('parent_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_pages');
    }
};