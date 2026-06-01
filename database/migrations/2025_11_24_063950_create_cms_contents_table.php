<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cms_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section', 100);
            $table->string('key', 100);
            $table->text('value_uz')->nullable();
            $table->text('value_en')->nullable();
            $table->text('value_ru')->nullable();
            $table->enum('type', ['text', 'textarea', 'image', 'url', 'number'])->default('text');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['section', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_contents');
    }
};
