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
        // Buildings table
        if (!Schema::hasTable('buildings')) {
            Schema::create('buildings', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('address')->nullable();
                $table->integer('floors')->default(1);
                $table->timestamps();
            });
        }

        // Academic years table
        if (!Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_current')->default(false);
                $table->timestamps();
            });
        }

        // Faculties table
        if (!Schema::hasTable('faculties')) {
            Schema::create('faculties', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('dean_name')->nullable();
                $table->timestamps();
            });
        }

        // Departments table
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');
                $table->string('head_name')->nullable();
                $table->timestamps();
            });
        }

        // Groups table
        if (!Schema::hasTable('groups')) {
            Schema::create('groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
                $table->integer('course');
                $table->integer('students_count')->default(0);
                $table->enum('education_type', ['kunduzgi', 'sirtqi', 'kechki'])->default('kunduzgi');
                $table->timestamps();
            });
        }

        // Teachers table
        if (!Schema::hasTable('teachers')) {
            Schema::create('teachers', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('middle_name')->nullable();
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
                $table->enum('degree', ['bakalavr', 'magistr', 'phd', 'dsc', 'professor'])->nullable();
                $table->string('position')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // Students table
        if (!Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('middle_name')->nullable();
                $table->string('student_id')->unique();
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
                $table->date('birth_date')->nullable();
                $table->enum('gender', ['male', 'female']);
                $table->date('admission_date');
                $table->enum('status', ['active', 'academic_leave', 'graduated', 'expelled'])->default('active');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // Subjects table
        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
                $table->integer('credits');
                $table->integer('total_hours');
                $table->integer('lecture_hours')->default(0);
                $table->integer('practice_hours')->default(0);
                $table->integer('lab_hours')->default(0);
                $table->integer('seminar_hours')->default(0);
                $table->integer('independent_hours')->default(0);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('students');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('buildings');
    }
};
