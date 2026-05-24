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
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'student_id')) {
                $table->foreignId('student_id')->nullable()->after('user_id')->constrained('students')->onDelete('cascade');
            }
            if (!Schema::hasColumn('attendances', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->after('student_id')->constrained('subjects')->onDelete('cascade');
            }

            // Add indexes
            if (!Schema::hasColumn('attendances', 'student_id')) {
                $table->index('student_id');
            }
            if (!Schema::hasColumn('attendances', 'subject_id')) {
                $table->index('subject_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'student_id')) {
                $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
            if (Schema::hasColumn('attendances', 'subject_id')) {
                $table->dropForeign(['subject_id']);
                $table->dropColumn('subject_id');
            }
        });
    }
};
