<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specialty_id')->nullable();
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->string('name')->unique();
            $table->integer('course');
            $table->integer('max_students')->default(30);
            $table->integer('current_students')->default(0);
            $table->foreignId('curator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('curator_name')->nullable();
            $table->string('monitor_name')->nullable();
            $table->string('monitor_phone')->nullable();
            $table->integer('academic_year');
            $table->enum('semester', ['1', '2'])->default('1');
            $table->enum('language', ['uz', 'ru', 'en', 'kr'])->default('uz');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['specialty_id', 'course']);
            $table->index(['faculty_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_groups');
    }
};