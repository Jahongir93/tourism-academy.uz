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
        Schema::table('curricula', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('curricula', 'program_id')) {
                $table->foreignId('program_id')->nullable()->constrained('educational_programs');
            }
            if (!Schema::hasColumn('curricula', 'academic_year')) {
                $table->string('academic_year', 9)->nullable();
            }
            if (!Schema::hasColumn('curricula', 'semester_number')) {
                $table->integer('semester_number')->nullable();
            }
            if (!Schema::hasColumn('curricula', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->constrained('subjects');
            }
            if (!Schema::hasColumn('curricula', 'lecture_hours')) {
                $table->integer('lecture_hours')->default(0);
            }
            if (!Schema::hasColumn('curricula', 'practice_hours')) {
                $table->integer('practice_hours')->default(0);
            }
            if (!Schema::hasColumn('curricula', 'seminar_hours')) {
                $table->integer('seminar_hours')->default(0);
            }
            if (!Schema::hasColumn('curricula', 'lab_hours')) {
                $table->integer('lab_hours')->default(0);
            }
            if (!Schema::hasColumn('curricula', 'independent_hours')) {
                $table->integer('independent_hours')->default(0);
            }
            if (!Schema::hasColumn('curricula', 'credits')) {
                $table->integer('credits')->default(0);
            }
            if (!Schema::hasColumn('curricula', 'subject_type')) {
                $table->enum('subject_type', ['majburiy', 'tanlov', 'fakultativ'])->default('majburiy');
            }
            if (!Schema::hasColumn('curricula', 'sequence_number')) {
                $table->integer('sequence_number')->nullable();
            }
            if (!Schema::hasColumn('curricula', 'is_approved')) {
                $table->boolean('is_approved')->default(false);
            }

            // Old columns for compatibility
            if (!Schema::hasColumn('curricula', 'specialty_id')) {
                $table->foreignId('specialty_id')->nullable()->constrained('specialties');
            }
            if (!Schema::hasColumn('curricula', 'semester')) {
                $table->integer('semester')->nullable();
            }
            if (!Schema::hasColumn('curricula', 'year')) {
                $table->integer('year')->nullable();
            }
            if (!Schema::hasColumn('curricula', 'hours_per_week')) {
                $table->integer('hours_per_week')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            $table->dropColumn([
                'program_id',
                'academic_year',
                'semester_number',
                'subject_id',
                'lecture_hours',
                'practice_hours',
                'seminar_hours',
                'lab_hours',
                'independent_hours',
                'credits',
                'subject_type',
                'sequence_number',
                'is_approved',
                'specialty_id',
                'semester',
                'year',
                'hours_per_week'
            ]);
        });
    }
};