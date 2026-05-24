<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // page, header, footer, section
            $table->longText('html_structure');
            $table->longText('css')->nullable();
            $table->longText('js')->nullable();
            $table->json('variables')->nullable(); // template variables
            $table->json('sections')->nullable(); // editable sections
            $table->string('preview_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('type');
            $table->index('is_active');
            $table->index('is_default');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_templates');
    }
};