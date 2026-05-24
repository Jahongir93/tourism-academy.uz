<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lms_library_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_uz');
            $table->string('name_ru')->nullable();
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('order_number')->default(0);
            $table->integer('books_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('lms_library_categories')->onDelete('cascade');
            $table->index(['slug', 'is_active']);
            $table->index('parent_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lms_library_categories');
    }
};