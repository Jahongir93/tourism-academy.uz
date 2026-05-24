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
        $tables = [
            'lms_materials',
            'lms_courses',
            'lms_videos',
            'lms_practice_tests'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'teacher_id')) {
                Schema::table($table, function (Blueprint $table) {
                    // Drop existing foreign key if exists
                    try {
                        $table->dropForeign(['teacher_id']);
                    } catch (\Exception $e) {
                        // Foreign key might not exist
                    }

                    // Make teacher_id nullable first
                    $table->unsignedBigInteger('teacher_id')->nullable()->change();

                    // Add new foreign key to users table
                    $table->foreign('teacher_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'lms_materials',
            'lms_courses',
            'lms_videos',
            'lms_practice_tests'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'teacher_id')) {
                Schema::table($table, function (Blueprint $table) {
                    // Drop foreign key to users
                    try {
                        $table->dropForeign(['teacher_id']);
                    } catch (\Exception $e) {
                        // Ignore
                    }

                    // Restore foreign key to employees table
                    $table->foreign('teacher_id')
                          ->references('id')
                          ->on('employees')
                          ->onDelete('cascade');
                });
            }
        }
    }
};
