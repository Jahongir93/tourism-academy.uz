<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Educational programs (Ta'lim yo'nalishlari)
        if (!Schema::hasTable('educational_programs')) {
            Schema::create('educational_programs', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name_uz');
                $table->string('name_ru')->nullable();
                $table->string('name_en')->nullable();
                $table->enum('level', ['bakalavriat', 'magistratura', 'doktorantura', 'ordinatura']);
                $table->enum('education_form', ['kunduzgi', 'kechki', 'sirtqi']);
                $table->integer('duration_years');
                $table->integer('total_credits');
                $table->foreignId('faculty_id')->constrained();
                $table->foreignId('department_id')->nullable()->constrained();
                $table->string('qualification')->nullable();
                $table->text('description')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
                
                $table->index(['faculty_id', 'level', 'education_form']);
                $table->index('code');
            });
        }

        // Subject hour distribution templates
        if (!Schema::hasTable('subject_hour_distributions')) {
            Schema::create('subject_hour_distributions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->foreignId('program_id')->nullable()->constrained('educational_programs')->onDelete('cascade');
                $table->integer('lecture_hours')->default(0);
                $table->integer('practice_hours')->default(0);
                $table->integer('seminar_hours')->default(0);
                $table->integer('lab_hours')->default(0);
                $table->integer('independent_hours')->default(0);
                $table->integer('course_work_hours')->nullable();
                $table->timestamps();
                
                $table->unique(['subject_id', 'program_id']);
            });
        }

        // Academic years
        if (!Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('year')->unique(); // "2024-2025"
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_current')->default(false);
                $table->timestamps();
                
                $table->index('year');
            });
        }

        // Curriculum versions (for tracking changes)
        if (!Schema::hasTable('curriculum_versions')) {
            Schema::create('curriculum_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_id')->constrained('educational_programs');
                $table->string('academic_year');
                $table->integer('version_number')->default(1);
                $table->json('curriculum_data'); // Full curriculum snapshot
                $table->string('status')->default('draft'); // draft, approved, archived
                $table->foreignId('created_by')->constrained('users');
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->timestamp('approved_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['program_id', 'academic_year', 'version_number'], 'cv_prog_year_ver_idx');
            });
        }

        // Subject dependencies (prerequisites)
        if (!Schema::hasTable('subject_prerequisites')) {
            Schema::create('subject_prerequisites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->foreignId('prerequisite_id')->constrained('subjects')->onDelete('cascade');
                $table->enum('type', ['required', 'recommended'])->default('required');
                $table->timestamps();
                
                $table->unique(['subject_id', 'prerequisite_id']);
            });
        }

        // Program learning outcomes
        if (!Schema::hasTable('program_outcomes')) {
            Schema::create('program_outcomes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_id')->constrained('educational_programs')->onDelete('cascade');
                $table->string('code'); // PO1, PO2, etc.
                $table->text('description_uz');
                $table->text('description_ru')->nullable();
                $table->text('description_en')->nullable();
                $table->enum('category', ['knowledge', 'skills', 'competencies'])->default('knowledge');
                $table->integer('sequence_number');
                $table->timestamps();
                
                $table->index('program_id');
            });
        }

        // Subject-outcome mapping
        if (!Schema::hasTable('subject_outcome_mappings')) {
            Schema::create('subject_outcome_mappings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->foreignId('outcome_id')->constrained('program_outcomes')->onDelete('cascade');
                $table->enum('contribution_level', ['low', 'medium', 'high']);
                $table->timestamps();
                
                $table->unique(['subject_id', 'outcome_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('subject_outcome_mappings');
        Schema::dropIfExists('program_outcomes');
        Schema::dropIfExists('subject_prerequisites');
        Schema::dropIfExists('curriculum_versions');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('subject_hour_distributions');
        Schema::dropIfExists('educational_programs');
    }
};