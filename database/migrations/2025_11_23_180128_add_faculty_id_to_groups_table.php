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
        Schema::table('groups', function (Blueprint $table) {
            $table->foreignId('faculty_id')->nullable()->after('department_id')->constrained()->onDelete('cascade');
            $table->integer('academic_year')->nullable()->after('course');
            $table->enum('semester', ['1', '2'])->default('1')->after('academic_year');
            $table->integer('max_students')->default(35)->after('students_count');
            $table->integer('current_students')->default(0)->after('max_students');
            $table->foreignId('curator_id')->nullable()->after('current_students')->constrained('users')->onDelete('set null');
            $table->foreignId('specialty_id')->nullable()->after('curator_id')->constrained()->onDelete('set null');
            $table->string('language')->default('uz')->after('specialty_id');
            $table->boolean('is_active')->default(true)->after('language');
        });

        // Populate faculty_id from department relationship
        DB::statement('
            UPDATE groups g
            JOIN departments d ON g.department_id = d.id
            SET g.faculty_id = d.faculty_id
            WHERE d.faculty_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->dropForeign(['curator_id']);
            $table->dropForeign(['specialty_id']);
            $table->dropColumn([
                'faculty_id',
                'academic_year',
                'semester',
                'max_students',
                'current_students',
                'curator_id',
                'specialty_id',
                'language',
                'is_active'
            ]);
        });
    }
};
