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
        Schema::create('subject_teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade');
            $table->enum('lesson_type', ['lecture', 'practice', 'lab', 'seminar', 'all'])->default('all');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->integer('semester_id');
            $table->integer('hours_allocated')->nullable()->comment('Ajratilgan soatlar');
            $table->boolean('is_primary')->default(false)->comment('Asosiy o\'qituvchi');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for faster queries
            $table->index(['subject_id', 'academic_year_id', 'semester_id'], 'idx_subject_year_semester');
            $table->index(['teacher_id', 'academic_year_id'], 'idx_teacher_year');
            $table->index(['group_id', 'subject_id'], 'idx_group_subject');

            // Unique constraint: one teacher per subject-group-lesson_type combination
            $table->unique(['subject_id', 'teacher_id', 'group_id', 'lesson_type', 'academic_year_id', 'semester_id'], 'unique_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_teacher_assignments');
    }
};
