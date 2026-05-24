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
        // Journal entries table
        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->integer('semester_id');
            $table->timestamps();
            $table->index(['subject_id', 'group_id', 'teacher_id']);
            });
        }

        // Attendance records
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('lesson_date');
            $table->enum('lesson_type', ['lecture', 'practice', 'lab', 'seminar']);
            $table->string('time_slot');
            $table->enum('status', ['present', 'absent', 'excused', 'late']);
            $table->integer('late_minutes')->nullable();
            $table->string('excuse_document_url')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('marked_by')->constrained('users');
            $table->timestamp('marked_at');
            $table->timestamps();
            $table->index(['student_id', 'lesson_date']);
            $table->index(['journal_entry_id', 'lesson_date']);
        });

        // Journal grades table (renamed to avoid conflict)
        Schema::create('journal_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('grade_type', ['joriy', 'oraliq', 'yakuniy']);
            $table->decimal('score', 5, 2);
            $table->decimal('max_score', 5, 2)->default(100);
            $table->decimal('weight_percentage', 5, 2);
            $table->date('graded_date');
            $table->foreignId('graded_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'journal_entry_id', 'grade_type']);
        });

        // Grade calculations
        Schema::create('grade_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('semester_id');
            $table->decimal('joriy_score', 5, 2)->nullable();
            $table->decimal('oraliq_score', 5, 2)->nullable();
            $table->decimal('yakuniy_score', 5, 2)->nullable();
            $table->decimal('additional_points', 5, 2)->default(0);
            $table->decimal('total_weighted_score', 5, 2);
            $table->enum('final_grade', ['5', '4', '3', '2']);
            $table->decimal('gpa_points', 3, 2);
            $table->integer('credits');
            $table->timestamp('calculated_at');
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'semester_id']);
        });

        // Assignments table
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['lab', 'course_work', 'homework', 'independent']);
            $table->datetime('deadline');
            $table->decimal('max_score', 5, 2)->default(100);
            $table->decimal('late_penalty_percent', 5, 2)->default(5);
            $table->json('attachments')->nullable();
            $table->json('group_ids');
            $table->timestamps();
            $table->index(['subject_id', 'teacher_id']);
        });

        // Assignment submissions
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->datetime('submitted_at');
            $table->json('files')->nullable();
            $table->text('text_content')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users');
            $table->datetime('graded_at')->nullable();
            $table->enum('status', ['submitted', 'graded', 'returned', 'late']);
            $table->timestamps();
            $table->unique(['assignment_id', 'student_id']);
        });

        // Schedule tables
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->integer('semester_id');
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->index(['academic_year_id', 'semester_id', 'group_id']);
        });

        // Schedule slots
        Schema::create('schedule_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->string('time_slot');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('classrooms')->onDelete('cascade');
            $table->enum('lesson_type', ['lecture', 'practice', 'lab', 'seminar']);
            $table->enum('week_type', ['numerator', 'denominator', 'both'])->default('both');
            $table->timestamps();
            $table->index(['schedule_id', 'day_of_week', 'time_slot']);
            $table->index(['teacher_id', 'day_of_week', 'time_slot']);
            $table->index(['room_id', 'day_of_week', 'time_slot']);
        });

        // Classrooms - removed as it's created in a separate migration

        // Time slots configuration
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->integer('slot_number');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_break')->default(false);
            $table->enum('slot_type', ['regular', 'evening'])->default('regular');
            $table->timestamps();
            $table->unique('slot_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
        // Schema::dropIfExists('classrooms'); // Handled in separate migration
        Schema::dropIfExists('schedule_slots');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('grade_calculations');
        Schema::dropIfExists('journal_grades');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('journal_entries');
    }
};
