<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dars jadvali asosiy jadvali - Skip if exists
        if (!Schema::hasTable('schedules')) {
            Schema::create('schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('student_groups')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');

                // Kun va vaqt
                $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
                $table->integer('pair_number'); // 1, 2, 3, 4, 5
                $table->time('start_time');
                $table->time('end_time');

                // Dars turi va joy
                $table->enum('lesson_type', ['lecture', 'practice', 'seminar', 'lab'])->default('lecture');
                $table->string('room')->nullable();
                $table->string('building')->nullable();

                // Muddat
                $table->date('start_date'); // Qachondan
                $table->date('end_date')->nullable(); // Qachongacha (null = cheksiz)

                // Holat
                $table->enum('status', ['active', 'cancelled', 'rescheduled'])->default('active');
                $table->text('notes')->nullable();

                $table->timestamps();

                // Indexes
                $table->index(['group_id', 'day_of_week', 'pair_number']);
                $table->index(['teacher_id', 'day_of_week', 'pair_number']);
                $table->index(['start_date', 'end_date']);
            });
        }

        // Dars o'zgarishlari (bekor qilish, vaqt o'zgartirish) - Skip if exists
        if (!Schema::hasTable('schedule_changes')) {
            Schema::create('schedule_changes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');

                $table->date('change_date'); // Qaysi sanada o'zgarish
                $table->enum('change_type', ['cancelled', 'rescheduled', 'room_changed', 'teacher_changed']);

                // Yangi ma'lumotlar (agar o'zgartirilsa)
                $table->time('new_start_time')->nullable();
                $table->time('new_end_time')->nullable();
                $table->string('new_room')->nullable();
                $table->string('new_building')->nullable();
                $table->foreignId('new_teacher_id')->nullable()->constrained('users');

                $table->text('reason')->nullable();
                $table->foreignId('changed_by')->constrained('users');

                $table->timestamps();

                $table->index(['schedule_id', 'change_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_changes');
        Schema::dropIfExists('schedules');
    }
};
