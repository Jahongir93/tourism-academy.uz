<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SECURITY FIX: Add unique constraints for data integrity
        // Note: Cannot delete duplicates due to foreign key constraints
        // Just add unique constraints where data is clean

        // Add unique constraints only if no duplicates exist
        // JSHSHIR unique constraint
        $jshshirDuplicates = DB::select('
            SELECT jshshir, COUNT(*) as count
            FROM employees
            WHERE jshshir IS NOT NULL
            GROUP BY jshshir
            HAVING COUNT(*) > 1
        ');

        if (empty($jshshirDuplicates) && !$this->indexExists('employees', 'employees_jshshir_unique')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unique('jshshir', 'employees_jshshir_unique');
            });
        }

        // Employee code unique constraint (should always be unique)
        if (!$this->indexExists('employees', 'employees_employee_code_unique')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unique('employee_code', 'employees_employee_code_unique');
            });
        }

        // Phone unique constraint
        $phoneDuplicates = DB::select('
            SELECT phone, COUNT(*) as count
            FROM employees
            WHERE phone IS NOT NULL
            GROUP BY phone
            HAVING COUNT(*) > 1
        ');

        if (empty($phoneDuplicates) && !$this->indexExists('employees', 'employees_phone_unique')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unique('phone', 'employees_phone_unique');
            });
        }

        // Passport series + number composite unique
        $passportDuplicates = DB::select('
            SELECT passport_series, passport_number, COUNT(*) as count
            FROM employees
            WHERE passport_series IS NOT NULL AND passport_number IS NOT NULL
            GROUP BY passport_series, passport_number
            HAVING COUNT(*) > 1
        ');

        if (empty($passportDuplicates) && !$this->indexExists('employees', 'employees_passport_unique')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unique(['passport_series', 'passport_number'], 'employees_passport_unique');
            });
        }

        // Add performance indexes
        Schema::table('employees', function (Blueprint $table) {
            if (!$this->indexExists('employees', 'employees_employee_type_index')) {
                $table->index('employee_type', 'employees_employee_type_index');
            }
            if (!$this->indexExists('employees', 'employees_status_index')) {
                $table->index('status', 'employees_status_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop unique constraints
            if ($this->indexExists('employees', 'employees_jshshir_unique')) {
                $table->dropUnique('employees_jshshir_unique');
            }

            if ($this->indexExists('employees', 'employees_employee_code_unique')) {
                $table->dropUnique('employees_employee_code_unique');
            }

            if ($this->indexExists('employees', 'employees_phone_unique')) {
                $table->dropUnique('employees_phone_unique');
            }

            if ($this->indexExists('employees', 'employees_passport_unique')) {
                $table->dropUnique('employees_passport_unique');
            }

            // Drop indexes
            if ($this->indexExists('employees', 'employees_employee_type_index')) {
                $table->dropIndex('employees_employee_type_index');
            }
            if ($this->indexExists('employees', 'employees_status_index')) {
                $table->dropIndex('employees_status_index');
            }
        });
    }

    /**
     * Check if an index exists
     */
    private function indexExists($table, $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$index}'");
        return !empty($indexes);
    }
};
