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
        Schema::create('vedomost_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->integer('semester')->comment('1 yoki 2');
            $table->integer('credits')->default(3);
            $table->string('assessment_type')->default('exam')->comment('exam, test, coursework, etc');
            $table->date('assessment_date')->nullable();
            $table->enum('status', ['draft', 'in_progress', 'submitted', 'approved'])->default('draft');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: one vedomost per group-subject-year-semester
            $table->unique(['group_id', 'subject_id', 'academic_year_id', 'semester'], 'unique_vedomost_sheet');

            // Indexes
            $table->index(['teacher_id', 'academic_year_id', 'semester'], 'idx_teacher_year_sem');
            $table->index(['status', 'is_active'], 'idx_status_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vedomost_sheets');
    }
};
