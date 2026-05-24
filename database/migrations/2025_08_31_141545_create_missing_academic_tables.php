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
        // Student enrollments table
        if (!Schema::hasTable('student_enrollments')) {
            Schema::create('student_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('academic_year_id')->constrained('academic_years');
                $table->foreignId('semester_id')->nullable();
                $table->foreignId('group_id')->constrained('academic_groups');
                $table->foreignId('specialty_id')->constrained('specialties');
                $table->date('enrollment_date');
                $table->date('graduation_date')->nullable();
                $table->enum('status', ['active', 'graduated', 'expelled', 'academic_leave'])->default('active');
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['student_id', 'is_active']);
                $table->index(['academic_year_id', 'semester_id']);
            });
        }

        // Positions table
        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->id();
                $table->string('name_uz');
                $table->string('name_ru')->nullable();
                $table->string('name_en')->nullable();
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->enum('level', ['top', 'middle', 'operational'])->default('operational');
                $table->integer('hierarchy_level')->default(1);
                $table->foreignId('parent_position_id')->nullable()->constrained('positions');
                $table->boolean('is_teaching')->default(false);
                $table->boolean('is_administrative')->default(false);
                $table->decimal('base_salary', 12, 2)->nullable();
                $table->integer('max_workload_hours')->nullable();
                $table->json('required_qualifications')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['code', 'is_active']);
                $table->index('parent_position_id');
            });
        }

        // Divisions table (Bo'limlar)
        if (!Schema::hasTable('divisions')) {
            Schema::create('divisions', function (Blueprint $table) {
                $table->id();
                $table->string('name_uz');
                $table->string('name_ru')->nullable();
                $table->string('name_en')->nullable();
                $table->string('short_name')->nullable();
                $table->string('code')->unique();
                $table->enum('type', ['rektorat', 'administrative', 'academic', 'support', 'technical'])->default('administrative');
                $table->foreignId('parent_id')->nullable()->constrained('divisions');
                $table->foreignId('head_position_id')->nullable()->constrained('positions');
                $table->foreignId('head_employee_id')->nullable()->constrained('employees');
                $table->text('description')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('office_location')->nullable();
                $table->integer('hierarchy_level')->default(1);
                $table->integer('order_number')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['code', 'is_active']);
                $table->index('parent_id');
                $table->index('type');
            });
        }

        // Staff allocations table (Shtat birliklari)
        if (!Schema::hasTable('staff_allocations')) {
            Schema::create('staff_allocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_id')->nullable()->constrained('divisions');
                $table->foreignId('department_id')->nullable()->constrained('departments');
                $table->foreignId('position_id')->constrained('positions');
                $table->decimal('rate', 3, 2)->default(1.00); // 0.25, 0.5, 0.75, 1.00
                $table->integer('allocated_count')->default(1);
                $table->integer('filled_count')->default(0);
                $table->integer('vacant_count')->default(1);
                $table->enum('status', ['active', 'frozen', 'cancelled'])->default('active');
                $table->date('effective_from');
                $table->date('effective_to')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['division_id', 'position_id']);
                $table->index(['department_id', 'position_id']);
                $table->index('status');
            });
        }

        // Division staff table (Bo'lim xodimlari)
        if (!Schema::hasTable('division_staff')) {
            Schema::create('division_staff', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_id')->constrained('divisions');
                $table->foreignId('employee_id')->constrained('employees');
                $table->foreignId('position_id')->constrained('positions');
                $table->foreignId('staff_allocation_id')->nullable()->constrained('staff_allocations');
                $table->decimal('rate', 3, 2)->default(1.00);
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->boolean('is_primary')->default(true);
                $table->enum('status', ['active', 'inactive', 'leave'])->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['division_id', 'employee_id']);
                $table->index(['employee_id', 'is_primary']);
                $table->unique(['division_id', 'employee_id', 'position_id', 'start_date'], 'unique_assignment');
            });
        }

        // Create seed data for divisions
        $this->seedInitialData();
    }

    /**
     * Seed initial data
     */
    private function seedInitialData()
    {
        // Create basic positions
        $positions = [
            ['name' => 'Rektor', 'code' => 'RECTOR', 'description' => 'University Rector', 'is_active' => true],
            ['name' => 'Prorektor', 'code' => 'VICE_RECTOR', 'description' => 'Vice Rector', 'is_active' => true],
            ['name' => 'Dekan', 'code' => 'DEAN', 'description' => 'Faculty Dean', 'is_active' => true],
            ['name' => 'Kafedra mudiri', 'code' => 'HEAD_DEPT', 'description' => 'Department Head', 'is_active' => true],
            ['name' => 'Professor', 'code' => 'PROFESSOR', 'description' => 'Professor', 'is_active' => true],
            ['name' => 'Dotsent', 'code' => 'DOCENT', 'description' => 'Associate Professor', 'is_active' => true],
            ['name' => 'Katta o\'qituvchi', 'code' => 'SENIOR_TEACHER', 'description' => 'Senior Teacher', 'is_active' => true],
            ['name' => 'O\'qituvchi', 'code' => 'TEACHER', 'description' => 'Teacher', 'is_active' => true],
            ['name' => 'Bo\'lim boshlig\'i', 'code' => 'HEAD_DIV', 'description' => 'Division Head', 'is_active' => true],
            ['name' => 'Mutaxassis', 'code' => 'SPECIALIST', 'description' => 'Specialist', 'is_active' => true],
        ];

        foreach ($positions as $position) {
            \DB::table('positions')->insertOrIgnore($position + ['created_at' => now(), 'updated_at' => now()]);
        }

        // Create main divisions
        $divisions = [
            ['name_uz' => 'Rektorat', 'code' => 'RECTORATE', 'type' => 'rektorat'],
            ['name_uz' => 'O\'quv ishlari bo\'limi', 'code' => 'ACADEMIC_DEPT', 'type' => 'administrative'],
            ['name_uz' => 'Ilmiy ishlar bo\'limi', 'code' => 'RESEARCH_DEPT', 'type' => 'administrative'],
            ['name_uz' => 'Moliya bo\'limi', 'code' => 'FINANCE_DEPT', 'type' => 'administrative'],
            ['name_uz' => 'Kadrlar bo\'limi', 'code' => 'HR_DEPT', 'type' => 'administrative'],
            ['name_uz' => 'Marketing bo\'limi', 'code' => 'MARKETING_DEPT', 'type' => 'administrative'],
            ['name_uz' => 'IT bo\'limi', 'code' => 'IT_DEPT', 'type' => 'technical'],
            ['name_uz' => 'Xo\'jalik bo\'limi', 'code' => 'FACILITY_DEPT', 'type' => 'support'],
        ];

        foreach ($divisions as $division) {
            \DB::table('divisions')->insertOrIgnore($division + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('division_staff');
        Schema::dropIfExists('staff_allocations');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('student_enrollments');
    }
};