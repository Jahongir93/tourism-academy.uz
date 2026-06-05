<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The schedule uses the unified teacher source (employees, employee_type=teacher).
 * Repoint schedule_slots.teacher_id from the legacy `teachers` table to `employees`.
 * Safe: schedule_slots is empty.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('schedule_slots') || !Schema::hasColumn('schedule_slots', 'teacher_id')) {
            return;
        }

        Schema::table('schedule_slots', function (Blueprint $table) {
            try { $table->dropForeign(['teacher_id']); } catch (\Throwable $e) {}
        });

        Schema::table('schedule_slots', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('schedule_slots')) {
            return;
        }
        Schema::table('schedule_slots', function (Blueprint $table) {
            try { $table->dropForeign(['teacher_id']); } catch (\Throwable $e) {}
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }
};
