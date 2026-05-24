<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cms_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location'); // header, footer, sidebar
            $table->json('items'); // JSON structure for menu items
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('location');
            $table->index('is_active');
        });
        
        Schema::create('cms_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('cms_menus')->onDelete('cascade');
            $table->string('title_uz');
            $table->string('title_ru')->nullable();
            $table->string('title_en')->nullable();
            $table->string('type'); // page, custom, category, external
            $table->string('url')->nullable();
            $table->foreignId('page_id')->nullable()->constrained('cms_pages');
            $table->foreignId('parent_id')->nullable()->constrained('cms_menu_items');
            $table->integer('order_position')->default(0);
            $table->string('icon')->nullable();
            $table->string('css_class')->nullable();
            $table->string('target')->default('_self');
            $table->boolean('is_active')->default(true);
            $table->json('attributes')->nullable();
            $table->timestamps();
            
            $table->index('menu_id');
            $table->index('parent_id');
            $table->index('order_position');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_menu_items');
        Schema::dropIfExists('cms_menus');
    }
};