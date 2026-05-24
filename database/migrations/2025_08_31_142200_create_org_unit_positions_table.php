<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('org_unit_positions', function (Blueprint $table) {
            $table->id();
            $table->enum('org_unit_type', ['university', 'faculty', 'department', 'division', 'center'])->default('department');
            $table->foreignId('org_unit_id'); // References faculty_id, department_id, etc based on type
            $table->foreignId('position_id')->nullable()->constrained('positions');
            $table->string('position_name')->nullable();
            $table->string('position_code')->nullable();
            $table->foreignId('employee_id')->nullable()->constrained('employees');
            $table->string('employee_name')->nullable();
            $table->decimal('rate', 3, 2)->default(1.00);
            $table->date('appointment_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'vacant', 'temporary', 'acting'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_head')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['org_unit_type', 'org_unit_id']);
            $table->index(['org_unit_type', 'is_active']);
            $table->index(['employee_id', 'is_active']);
        });

        // Seed initial data
        $this->seedInitialData();
    }

    private function seedInitialData()
    {
        // Get faculty IDs
        $faculties = \DB::table('faculties')->get();
        
        foreach ($faculties as $faculty) {
            \DB::table('org_unit_positions')->insert([
                'org_unit_type' => 'faculty',
                'org_unit_id' => $faculty->id,
                'position_name' => 'Dekan',
                'position_code' => 'DEAN',
                'employee_name' => $faculty->dean_name ?? 'Vacant',
                'status' => $faculty->dean_name ? 'active' : 'vacant',
                'is_active' => true,
                'is_head' => true,
                'appointment_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add deputy dean positions
            \DB::table('org_unit_positions')->insert([
                'org_unit_type' => 'faculty',
                'org_unit_id' => $faculty->id,
                'position_name' => "Dekan o'rinbosari (o'quv ishlari)",
                'position_code' => 'VICE_DEAN_ACADEMIC',
                'status' => 'vacant',
                'is_active' => true,
                'is_head' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \DB::table('org_unit_positions')->insert([
                'org_unit_type' => 'faculty',
                'org_unit_id' => $faculty->id,
                'position_name' => "Dekan o'rinbosari (ilmiy ishlar)",
                'position_code' => 'VICE_DEAN_RESEARCH',
                'status' => 'vacant',
                'is_active' => true,
                'is_head' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get department IDs if they exist
        if (Schema::hasTable('departments')) {
            $departments = \DB::table('departments')->get();
            
            foreach ($departments as $department) {
                \DB::table('org_unit_positions')->insert([
                    'org_unit_type' => 'department',
                    'org_unit_id' => $department->id,
                    'position_name' => 'Kafedra mudiri',
                    'position_code' => 'HEAD_DEPT',
                    'employee_name' => $department->head_name ?? 'Vacant',
                    'status' => $department->head_name ? 'active' : 'vacant',
                    'is_active' => true,
                    'is_head' => true,
                    'appointment_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Add university level positions
        \DB::table('org_unit_positions')->insert([
            [
                'org_unit_type' => 'university',
                'org_unit_id' => 1,
                'position_name' => 'Rektor',
                'position_code' => 'RECTOR',
                'status' => 'active',
                'is_active' => true,
                'is_head' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'org_unit_type' => 'university',
                'org_unit_id' => 1,
                'position_name' => "Birinchi prorektor",
                'position_code' => 'FIRST_VICE_RECTOR',
                'status' => 'active',
                'is_active' => true,
                'is_head' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'org_unit_type' => 'university',
                'org_unit_id' => 1,
                'position_name' => "O'quv ishlari bo'yicha prorektor",
                'position_code' => 'VICE_RECTOR_ACADEMIC',
                'status' => 'active',
                'is_active' => true,
                'is_head' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('org_unit_positions');
    }
};