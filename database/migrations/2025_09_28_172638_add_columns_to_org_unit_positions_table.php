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
        Schema::table('org_unit_positions', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('org_unit_positions', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->after('position_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('org_unit_positions', 'appointment_type')) {
                $table->enum('appointment_type', ['main', 'acting', 'deputy', 'temporary'])->default('main')->after('employee_id');
            }
            if (!Schema::hasColumn('org_unit_positions', 'appointment_date')) {
                $table->date('appointment_date')->nullable()->after('appointment_type');
            }
            if (!Schema::hasColumn('org_unit_positions', 'end_date')) {
                $table->date('end_date')->nullable()->after('appointment_date');
            }
            if (!Schema::hasColumn('org_unit_positions', 'workload_percentage')) {
                $table->integer('workload_percentage')->default(100)->after('end_date');
            }
            if (!Schema::hasColumn('org_unit_positions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('workload_percentage');
            }
            if (!Schema::hasColumn('org_unit_positions', 'notes')) {
                $table->text('notes')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('org_unit_positions', function (Blueprint $table) {
            // Drop foreign key constraint first
            if (Schema::hasColumn('org_unit_positions', 'employee_id')) {
                $table->dropForeign(['employee_id']);
            }

            // Drop columns
            $columns = [
                'employee_id',
                'appointment_type',
                'appointment_date',
                'end_date',
                'workload_percentage',
                'is_active',
                'notes'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('org_unit_positions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};