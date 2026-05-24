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
        Schema::create('curriculum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('educational_programs')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('academic_year');
            $table->integer('semester_number');
            $table->integer('week_number');
            $table->integer('lesson_number');
            $table->string('topic_name_uz');
            $table->string('topic_name_ru')->nullable();
            $table->string('topic_name_en')->nullable();
            $table->text('description')->nullable();
            $table->string('lesson_type'); // lecture, practice, seminar, lab, independent
            $table->integer('hours');
            $table->text('learning_outcomes')->nullable();
            $table->text('teaching_methods')->nullable();
            $table->text('assessment_methods')->nullable();
            $table->text('resources')->nullable();
            $table->text('homework')->nullable();
            $table->boolean('is_online')->default(false);
            $table->integer('sequence_number')->default(0);
            $table->timestamps();

            $table->index(['program_id', 'subject_id', 'academic_year', 'semester_number'], 'curriculum_topics_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_topics');
    }
};
