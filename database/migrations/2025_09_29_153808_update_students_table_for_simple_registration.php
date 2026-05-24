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
        Schema::table('students', function (Blueprint $table) {
            // Yangi ustunlar qo'shish
            if (!Schema::hasColumn('students', 'student_id')) {
                $table->string('student_id')->unique()->after('id');
            }

            if (!Schema::hasColumn('students', 'first_name')) {
                $table->string('first_name')->nullable();
            }

            if (!Schema::hasColumn('students', 'last_name')) {
                $table->string('last_name')->nullable();
            }

            if (!Schema::hasColumn('students', 'middle_name')) {
                $table->string('middle_name')->nullable();
            }

            if (!Schema::hasColumn('students', 'full_name')) {
                $table->string('full_name')->nullable();
            }

            if (!Schema::hasColumn('students', 'birth_date')) {
                $table->date('birth_date')->nullable();
            }

            if (!Schema::hasColumn('students', 'gender')) {
                $table->enum('gender', ['male', 'female'])->nullable();
            }

            if (!Schema::hasColumn('students', 'passport_series')) {
                $table->string('passport_series', 10)->nullable();
            }

            if (!Schema::hasColumn('students', 'passport_number')) {
                $table->string('passport_number', 20)->nullable();
            }

            if (!Schema::hasColumn('students', 'address')) {
                $table->text('address')->nullable();
            }

            if (!Schema::hasColumn('students', 'email')) {
                $table->string('email')->nullable();
            }

            if (!Schema::hasColumn('students', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (!Schema::hasColumn('students', 'faculty_id')) {
                $table->unsignedBigInteger('faculty_id')->nullable();
            }

            if (!Schema::hasColumn('students', 'group_id')) {
                $table->unsignedBigInteger('group_id')->nullable();
            }

            if (!Schema::hasColumn('students', 'registration_date')) {
                $table->date('registration_date')->nullable();
            }

            if (!Schema::hasColumn('students', 'profile_completed')) {
                $table->boolean('profile_completed')->default(false);
            }

            // Index qo'shish
            $table->index('student_id');
            $table->index('email');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'student_id',
                'first_name',
                'last_name',
                'middle_name',
                'full_name',
                'birth_date',
                'gender',
                'passport_series',
                'passport_number',
                'address',
                'email',
                'phone',
                'faculty_id',
                'group_id',
                'registration_date',
                'profile_completed'
            ]);
        });
    }
};