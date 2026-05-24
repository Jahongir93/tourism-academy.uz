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
            // Photo URL - rasm saqlash uchun
            if (!Schema::hasColumn('students', 'photo_url')) {
                $table->string('photo_url')->nullable()->after('address');
            }

            // Specialty ID - yo'nalish
            if (!Schema::hasColumn('students', 'specialty_id')) {
                $table->unsignedBigInteger('specialty_id')->nullable()->after('faculty_id');
            }

            // Course - kurs
            if (!Schema::hasColumn('students', 'course')) {
                $table->tinyInteger('course')->nullable()->default(1)->after('group_id');
            }

            // Education form - ta'lim shakli
            if (!Schema::hasColumn('students', 'education_form')) {
                $table->string('education_form', 20)->nullable();
            }

            // Education type - ta'lim turi
            if (!Schema::hasColumn('students', 'education_type')) {
                $table->string('education_type', 30)->nullable();
            }

            // Parent phone - ota-ona telefoni
            if (!Schema::hasColumn('students', 'parent_phone')) {
                $table->string('parent_phone', 20)->nullable()->after('phone');
            }

            // Temporary address - vaqtinchalik manzil
            if (!Schema::hasColumn('students', 'temporary_address')) {
                $table->text('temporary_address')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columns = ['photo_url', 'specialty_id', 'course', 'education_form', 'education_type', 'parent_phone', 'temporary_address'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
