<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Main employees table
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('jshshir', 14)->unique()->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->unsignedBigInteger('nationality_id')->nullable();
            $table->unsignedBigInteger('citizenship_id')->nullable();
            $table->string('passport_series', 2)->nullable();
            $table->string('passport_number', 7)->nullable();
            $table->date('passport_issued_date')->nullable();
            $table->string('passport_issued_by')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('telegram')->nullable();
            $table->text('address_permanent');
            $table->text('address_current')->nullable();
            $table->enum('employee_type', ['teacher', 'admin', 'support']);
            $table->enum('status', ['active', 'inactive', 'leave', 'terminated'])->default('active');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['employee_code', 'jshshir', 'employee_type', 'status']);
        });

        // Employment details
        Schema::create('employment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->enum('employment_type', ['asosiy', 'qoshimcha'])->default('asosiy');
            $table->enum('contract_type', ['muddatli', 'muddatsiz'])->default('muddatsiz');
            $table->decimal('stavka', 3, 2)->default(1.00);
            $table->date('hire_date');
            $table->date('contract_end_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->string('salary_grade')->nullable();
            $table->decimal('base_salary', 12, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('set null');
            $table->index(['employee_id', 'employment_type']);
        });

        // Employee education
        Schema::create('employee_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('education_level', ['oliy', 'orta_maxsus', 'orta'])->default('oliy');
            $table->string('institution_name');
            $table->string('specialization');
            $table->year('graduation_year');
            $table->string('diploma_number')->nullable();
            $table->string('diploma_file_url')->nullable();
            $table->timestamps();
            
            $table->index('employee_id');
        });

        // Scientific degrees
        Schema::create('employee_degrees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('degree_type', ['PhD', 'DSc', 'fan_nomzodi', 'fan_doktori'])->nullable();
            $table->enum('degree_title', ['professor', 'dotsent'])->nullable();
            $table->string('specialization')->nullable();
            $table->date('awarded_date')->nullable();
            $table->string('diploma_number')->nullable();
            $table->text('dissertation_topic')->nullable();
            $table->timestamps();
            
            $table->index('employee_id');
        });

        // Teacher subjects assignment
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->foreignId('subject_id')->constrained();
            $table->unsignedBigInteger('academic_year_id');
            $table->integer('semester_id');
            $table->json('group_ids')->nullable();
            $table->integer('lecture_hours')->default(0);
            $table->integer('practice_hours')->default(0);
            $table->integer('lab_hours')->default(0);
            $table->integer('total_hours')->default(0);
            $table->enum('language', ['uz', 'ru', 'en'])->default('uz');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
            
            $table->foreign('teacher_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['teacher_id', 'academic_year_id', 'semester_id']);
        });

        // Teacher groups (murabbiy)
        Schema::create('teacher_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->enum('role', ['murabbiy', 'curator'])->default('murabbiy');
            $table->date('assigned_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->foreign('teacher_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['teacher_id', 'academic_year_id']);
        });

        // Teacher workload
        Schema::create('teacher_workload', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->integer('teaching_hours')->default(0);
            $table->integer('research_hours')->default(0);
            $table->integer('methodical_hours')->default(0);
            $table->integer('educational_hours')->default(0);
            $table->integer('total_hours')->default(0);
            $table->integer('planned_hours')->default(680);
            $table->integer('completed_hours')->default(0);
            $table->enum('status', ['planned', 'in_progress', 'completed'])->default('planned');
            $table->timestamps();
            
            $table->foreign('teacher_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['teacher_id', 'academic_year_id']);
        });

        // Employment orders
        Schema::create('employment_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->date('order_date');
            $table->enum('order_type', [
                'ishga_qabul',
                'lavozimga_tayinlash',
                'otkazish',
                'ragbatlantirish',
                'intizomiy_jazo',
                'ishdan_boshatish'
            ]);
            $table->foreignId('employee_id')->constrained();
            $table->text('content');
            $table->text('basis')->nullable();
            $table->text('notes')->nullable();
            $table->string('file_url')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->enum('status', ['draft', 'approved', 'cancelled'])->default('draft');
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['order_type', 'status', 'order_date']);
        });

        // Employee leaves
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->enum('leave_type', [
                'mehnat_tatili',
                'oqitish_tatili',
                'tibbiy_tatil',
                'homiladorlik',
                'bola_parvarish',
                'haq_tolanmaydigan'
            ]);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count');
            $table->unsignedBigInteger('substitute_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->timestamps();
            
            $table->foreign('substitute_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('employment_orders')->onDelete('set null');
            $table->index(['employee_id', 'status', 'start_date', 'end_date']);
        });

        // Employee documents
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('document_type');
            $table->string('document_name');
            $table->string('file_url');
            $table->date('uploaded_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
            
            $table->index(['employee_id', 'document_type']);
        });

        // Employee attendance
        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->enum('status', ['present', 'absent', 'leave', 'mission'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['employee_id', 'date']);
            $table->index(['date', 'status']);
        });

        // Nationalities reference table
        Schema::create('nationalities', function (Blueprint $table) {
            $table->id();
            $table->string('name_uz');
            $table->string('name_ru')->nullable();
            $table->string('name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Citizenships reference table
        Schema::create('citizenships', function (Blueprint $table) {
            $table->id();
            $table->string('name_uz');
            $table->string('name_ru')->nullable();
            $table->string('name_en')->nullable();
            $table->string('code', 3)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_attendance');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_leaves');
        Schema::dropIfExists('employment_orders');
        Schema::dropIfExists('teacher_workload');
        Schema::dropIfExists('teacher_groups');
        Schema::dropIfExists('teacher_subjects');
        Schema::dropIfExists('employee_degrees');
        Schema::dropIfExists('employee_education');
        Schema::dropIfExists('employment_details');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('citizenships');
        Schema::dropIfExists('nationalities');
    }
};