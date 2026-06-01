<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make all fields in students table nullable where needed, only if they exist
        // VARCHAR(191) max — utf8mb4 indexed columns: 191*4=764 bytes < 1000 byte limit
        if (Schema::hasColumn('students', 'first_name')) DB::statement('ALTER TABLE students MODIFY COLUMN first_name VARCHAR(191) NULL');
        if (Schema::hasColumn('students', 'last_name')) DB::statement('ALTER TABLE students MODIFY COLUMN last_name VARCHAR(191) NULL');
        if (Schema::hasColumn('students', 'middle_name')) DB::statement('ALTER TABLE students MODIFY COLUMN middle_name VARCHAR(191) NULL');
        if (Schema::hasColumn('students', 'full_name')) DB::statement('ALTER TABLE students MODIFY COLUMN full_name VARCHAR(191) NULL');
        if (Schema::hasColumn('students', 'email')) DB::statement('ALTER TABLE students MODIFY COLUMN email VARCHAR(191) NULL');
        if (Schema::hasColumn('students', 'phone')) DB::statement('ALTER TABLE students MODIFY COLUMN phone VARCHAR(191) NULL');
        if (Schema::hasColumn('students', 'group_id')) DB::statement('ALTER TABLE students MODIFY COLUMN group_id BIGINT UNSIGNED NULL');
        if (Schema::hasColumn('students', 'faculty_id')) DB::statement('ALTER TABLE students MODIFY COLUMN faculty_id BIGINT UNSIGNED NULL');
        if (Schema::hasColumn('students', 'specialty_id')) DB::statement('ALTER TABLE students MODIFY COLUMN specialty_id BIGINT UNSIGNED NULL');
        if (Schema::hasColumn('students', 'birth_date')) DB::statement('ALTER TABLE students MODIFY COLUMN birth_date DATE NULL');
        if (Schema::hasColumn('students', 'gender')) DB::statement('ALTER TABLE students MODIFY COLUMN gender ENUM("male", "female") NULL');
        if (Schema::hasColumn('students', 'admission_date')) DB::statement('ALTER TABLE students MODIFY COLUMN admission_date DATE NULL');
        if (Schema::hasColumn('students', 'registration_date')) DB::statement('ALTER TABLE students MODIFY COLUMN registration_date DATE NULL');
        if (Schema::hasColumn('students', 'passport_series')) DB::statement('ALTER TABLE students MODIFY COLUMN passport_series VARCHAR(10) NULL');
        if (Schema::hasColumn('students', 'passport_number')) DB::statement('ALTER TABLE students MODIFY COLUMN passport_number VARCHAR(20) NULL');
        if (Schema::hasColumn('students', 'address')) DB::statement('ALTER TABLE students MODIFY COLUMN address TEXT NULL');
        if (Schema::hasColumn('students', 'course')) DB::statement('ALTER TABLE students MODIFY COLUMN course INT NULL');
        if (Schema::hasColumn('students', 'semester')) DB::statement('ALTER TABLE students MODIFY COLUMN semester INT NULL');
        if (Schema::hasColumn('students', 'photo_url')) DB::statement('ALTER TABLE students MODIFY COLUMN photo_url VARCHAR(191) NULL');
    }

    public function down(): void
    {
        // Revert is not recommended as it may cause data loss
        // If needed, manually restore the original schema
    }
};
