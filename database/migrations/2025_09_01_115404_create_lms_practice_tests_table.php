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
        Schema::create('lms_practice_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('employees')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('questions'); // JSON array of questions with answers
            $table->integer('time_limit')->nullable(); // in minutes
            $table->integer('passing_score')->default(60);
            $table->string('test_type'); // quiz, midterm, final, practice
            $table->integer('week_number')->nullable();
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('allow_retake')->default(true);
            $table->integer('max_attempts')->nullable();
            $table->boolean('is_active')->default(true);
            $table->datetime('available_from')->nullable();
            $table->datetime('available_until')->nullable();
            $table->timestamps();
            
            $table->index(['subject_id', 'teacher_id']);
            $table->index('test_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_practice_tests');
    }
};