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
        Schema::table('employees', function (Blueprint $table) {
            // Full name - to'liq ism
            if (!Schema::hasColumn('employees', 'full_name')) {
                $table->string('full_name')->nullable()->after('middle_name');
            }

            // Department ID - bo'lim
            if (!Schema::hasColumn('employees', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable();
            }

            // Position - lavozim
            if (!Schema::hasColumn('employees', 'position')) {
                $table->string('position')->nullable();
            }

            // Hire date - ishga qabul sanasi
            if (!Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable();
            }

            // Address (general)
            if (!Schema::hasColumn('employees', 'address')) {
                $table->text('address')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $columns = ['full_name', 'department_id', 'position', 'hire_date', 'address'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
