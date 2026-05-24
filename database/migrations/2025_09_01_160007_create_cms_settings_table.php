<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, number, boolean, json, file
            $table->string('group')->default('general');
            $table->string('label');
            $table->text('description')->nullable();
            $table->json('options')->nullable(); // for select fields
            $table->integer('order_position')->default(0);
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->index('key');
            $table->index('group');
            $table->index('is_public');
        });
        
        Schema::create('cms_seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_type'); // home, news, events, etc.
            $table->string('title_template');
            $table->text('description_template');
            $table->string('keywords_template')->nullable();
            $table->json('og_settings')->nullable();
            $table->json('twitter_settings')->nullable();
            $table->json('schema_markup')->nullable();
            $table->timestamps();
            
            $table->index('page_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_seo_settings');
        Schema::dropIfExists('cms_settings');
    }
};