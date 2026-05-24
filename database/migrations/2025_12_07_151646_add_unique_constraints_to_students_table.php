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
        // Add jshshir column if it doesn't exist
        if (!Schema::hasColumn('students', 'jshshir')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('jshshir', 14)->nullable()->after('passport_number');
            });
        }

        // Clean up duplicate data before adding constraints
        DB::statement('
            DELETE s1 FROM students s1
            INNER JOIN students s2
            WHERE s1.id > s2.id
            AND s1.jshshir IS NOT NULL
            AND s1.jshshir = s2.jshshir
        ');

        // Add unique constraints
        Schema::table('students', function (Blueprint $table) {
            // JSHSHIR unique constraint (nullable unique)
            if (!$this->indexExists('students', 'students_jshshir_unique')) {
                $table->unique('jshshir', 'students_jshshir_unique');
            }

            // Student ID unique constraint (if not already exists)
            if (!$this->indexExists('students', 'students_student_id_unique')) {
                $table->unique('student_id', 'students_student_id_unique');
            }

            // For MySQL/MariaDB: Can't use WHERE clause in index
            // Instead, create composite unique on (passport_series, passport_number)
            // Application level validation will ensure both are provided together
            if (!$this->indexExists('students', 'students_passport_unique')) {
                $table->unique(['passport_series', 'passport_number'], 'students_passport_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop unique constraints if they exist
            if ($this->indexExists('students', 'students_jshshir_unique')) {
                $table->dropUnique('students_jshshir_unique');
            }

            if ($this->indexExists('students', 'students_student_id_unique')) {
                $table->dropUnique('students_student_id_unique');
            }

            if ($this->indexExists('students', 'students_passport_unique')) {
                $table->dropUnique('students_passport_unique');
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
