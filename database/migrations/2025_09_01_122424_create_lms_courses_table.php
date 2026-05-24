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
        Schema::create('lms_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('objectives')->nullable(); // Kurs maqsadlari
            $table->text('requirements')->nullable(); // Talablar
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->foreignId('teacher_id')->constrained('employees')->onDelete('cascade');
            $table->string('course_code')->unique();
            $table->string('language')->default('uz'); // uz, ru, en
            $table->string('level')->default('beginner'); // beginner, intermediate, advanced
            $table->integer('duration_weeks')->nullable(); // Davomiyligi (haftalarda)
            $table->integer('hours_per_week')->nullable(); // Haftasiga soat
            $table->integer('credit_hours')->nullable(); // Kredit soatlari
            $table->string('thumbnail')->nullable(); // Kurs rasmi
            $table->string('intro_video')->nullable(); // Tanishuv videosi
            $table->decimal('price', 10, 2)->default(0); // Narxi (0 = bepul)
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('auto_enrollment')->default(false); // Avtomatik ro'yxatdan o'tish
            $table->integer('max_students')->nullable(); // Maksimal talabalar soni
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->datetime('enrollment_start')->nullable();
            $table->datetime('enrollment_end')->nullable();
            $table->integer('passing_score')->default(60); // O'tish bali
            $table->boolean('certificate_available')->default(true);
            $table->json('tags')->nullable(); // Teglar
            $table->integer('view_count')->default(0);
            $table->integer('enrollment_count')->default(0);
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('rating_count')->default(0);
            $table->timestamps();
            
            $table->index(['subject_id', 'teacher_id']);
            $table->index('is_published');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_courses');
    }
};