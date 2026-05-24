<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cms_media_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('parent_id')->nullable()->constrained('cms_media_folders');
            $table->integer('order_position')->default(0);
            $table->timestamps();
            
            $table->index('parent_id');
            $table->index('slug');
        });
        
        Schema::create('cms_media', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type');
            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('collection')->default('default');
            $table->unsignedBigInteger('size');
            $table->json('metadata')->nullable(); // dimensions, duration, etc.
            $table->json('thumbnails')->nullable(); // different sizes
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->foreignId('folder_id')->nullable()->constrained('cms_media_folders');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->integer('download_count')->default(0);
            $table->timestamps();
            
            $table->index('collection');
            $table->index('mime_type');
            $table->index('folder_id');
            $table->index('uploaded_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_media');
        Schema::dropIfExists('cms_media_folders');
    }
};