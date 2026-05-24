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
        Schema::table('grades', function (Blueprint $table) {
            // Change assessment_type from enum to string to allow custom assessment types
            DB::statement("ALTER TABLE grades MODIFY COLUMN assessment_type VARCHAR(100) DEFAULT 'exam'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // Revert back to enum
            DB::statement("ALTER TABLE grades MODIFY COLUMN assessment_type ENUM('exam', 'test', 'coursework', 'project', 'practice', 'other') DEFAULT 'exam'");
        });
    }
};
