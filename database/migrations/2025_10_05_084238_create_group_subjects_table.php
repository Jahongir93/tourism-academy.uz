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
        // Only create if doesn't exist - prevents duplicate table error
        if (!Schema::hasTable('group_subjects')) {
            Schema::create('group_subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_group_id')->constrained('student_groups')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
                $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
                $table->integer('semester'); // 1 yoki 2
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                // Bir guruhda bir fan bir o'quv yilida bir semestrda faqat bir marta
                $table->unique(['student_group_id', 'subject_id', 'academic_year_id', 'semester'], 'group_subject_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_subjects');
    }
};
