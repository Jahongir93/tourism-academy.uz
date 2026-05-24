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
        // Skip if exists
        if (!Schema::hasTable('grades')) {
            Schema::create('grades', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('journal_id')->nullable();
                $table->string('academic_year', 20); // e.g., "2024-2025"
                $table->integer('semester'); // 1 or 2
                $table->integer('course'); // 1, 2, 3, 4

                // Grade information
                $table->decimal('grade', 5, 2); // Numerical grade (0-100)
                $table->decimal('grade_point', 3, 2); // GPA point (0.00-4.00)
                $table->string('letter_grade', 5)->nullable(); // A, B, C, D, F, etc.
                $table->integer('credits')->default(0); // Credit hours

                // Assessment details
                $table->enum('assessment_type', ['exam', 'test', 'coursework', 'project', 'practice', 'other'])->default('exam');
                $table->date('assessment_date')->nullable();
                $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');

                // Additional info
                $table->text('comments')->nullable();
                $table->boolean('is_retake')->default(false);
                $table->integer('attempt_number')->default(1);
                $table->boolean('is_final')->default(true);

                $table->timestamps();

                // Indexes
                $table->index(['student_id', 'academic_year', 'semester']);
                $table->index(['subject_id', 'academic_year']);
                $table->index('assessment_date');
                $table->unique(['student_id', 'subject_id', 'academic_year', 'semester', 'attempt_number'], 'unique_student_subject_grade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
