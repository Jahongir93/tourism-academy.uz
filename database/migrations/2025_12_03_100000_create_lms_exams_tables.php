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
        // Imtihonlar jadvali
        Schema::create('lms_exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable(); // Imtihon ko'rsatmalari

            // Bog'lanishlar
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('lms_courses')->onDelete('set null');
            $table->json('group_ids')->nullable(); // Qaysi guruhlarga

            // Imtihon turi
            $table->enum('exam_type', ['joriy', 'oraliq', 'yakuniy', 'practice'])->default('practice');
            $table->integer('week_number')->nullable(); // Joriy nazorat uchun hafta raqami

            // Vaqt sozlamalari
            $table->integer('duration_minutes')->default(60); // Davomiyligi (daqiqa)
            $table->datetime('start_time')->nullable(); // Boshlanish vaqti
            $table->datetime('end_time')->nullable(); // Tugash vaqti
            $table->boolean('strict_time')->default(true); // Qat'iy vaqt chegarasi

            // Baholash
            $table->decimal('max_score', 5, 2)->default(100);
            $table->decimal('passing_score', 5, 2)->default(60); // O'tish bali
            $table->decimal('weight_percentage', 5, 2)->default(100); // Jurnalga qanday foizda o'tadi

            // Urinishlar
            $table->integer('max_attempts')->default(1); // Maksimal urinishlar
            $table->boolean('allow_retake')->default(false); // Qayta topshirish
            $table->integer('retake_delay_hours')->nullable(); // Qayta topshirish orasidagi vaqt

            // Savollar sozlamalari
            $table->integer('questions_count')->nullable(); // Nechta savol (null = barcha savollar)
            $table->boolean('shuffle_questions')->default(true); // Savollarni aralashtirish
            $table->boolean('shuffle_answers')->default(true); // Javoblarni aralashtirish
            $table->boolean('show_correct_answers')->default(false); // To'g'ri javobni ko'rsatish
            $table->boolean('show_score_immediately')->default(true); // Natijani darhol ko'rsatish

            // Xavfsizlik
            $table->boolean('browser_lockdown')->default(false); // Brauzer blokirovkasi
            $table->boolean('prevent_copy_paste')->default(true); // Nusxalashni taqiqlash
            $table->boolean('require_webcam')->default(false); // Webcam talab qilish
            $table->string('access_password')->nullable(); // Kirish paroli
            $table->json('allowed_ip_addresses')->nullable(); // Ruxsat etilgan IP lar

            // Jurnalga o'tkazish
            $table->boolean('sync_to_journal')->default(true); // Jurnalga avtomatik o'tkazish
            $table->boolean('auto_publish_results')->default(false); // Natijalarni avtomatik e'lon qilish

            // Holat
            $table->enum('status', ['draft', 'scheduled', 'active', 'completed', 'archived'])->default('draft');
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->index(['subject_id', 'teacher_id']);
            $table->index(['exam_type', 'status']);
            $table->index(['start_time', 'end_time']);
        });

        // Imtihon savollari
        Schema::create('lms_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('lms_exams')->onDelete('cascade');
            $table->integer('order_number')->default(0);

            // Savol
            $table->enum('question_type', ['single_choice', 'multiple_choice', 'true_false', 'text', 'essay', 'matching', 'fill_blank']);
            $table->text('question_text');
            $table->text('question_hint')->nullable(); // Savol bo'yicha izoh
            $table->json('media')->nullable(); // Rasm, video, audio

            // Javoblar (JSON formatda)
            $table->json('options')->nullable(); // Variant javoblar
            $table->json('correct_answer'); // To'g'ri javob(lar)
            $table->text('explanation')->nullable(); // Javob izohi

            // Baholash
            $table->decimal('points', 5, 2)->default(1); // Ball
            $table->boolean('partial_credit')->default(false); // Qisman ball berish
            $table->decimal('negative_marking', 5, 2)->default(0); // Noto'g'ri javob uchun minus ball

            // Qiyinlik darajasi
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('category')->nullable(); // Mavzu/bo'lim

            $table->boolean('is_required')->default(true); // Majburiy savol
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['exam_id', 'order_number']);
            $table->index('question_type');
        });

        // Talaba imtihon urinishlari
        Schema::create('lms_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('lms_exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->integer('attempt_number')->default(1);

            // Vaqt
            $table->datetime('started_at');
            $table->datetime('finished_at')->nullable();
            $table->integer('time_spent_seconds')->nullable(); // Sarflangan vaqt

            // Natija
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('unanswered')->default(0);
            $table->boolean('passed')->nullable();

            // Holat
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'expired', 'cancelled'])->default('in_progress');
            $table->json('question_order')->nullable(); // Savol tartibi (aralashtirish uchun)

            // Xavfsizlik
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('tab_switches')->default(0); // Tab almashtirishlar soni
            $table->json('activity_log')->nullable(); // Faollik logi

            // Jurnalga o'tkazilganmi
            $table->boolean('synced_to_journal')->default(false);
            $table->datetime('synced_at')->nullable();

            $table->timestamps();

            $table->index(['exam_id', 'student_id']);
            $table->index(['student_id', 'status']);
            $table->unique(['exam_id', 'student_id', 'attempt_number']);
        });

        // Talaba javoblari
        Schema::create('lms_exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('lms_exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('lms_exam_questions')->onDelete('cascade');

            // Javob
            $table->json('answer')->nullable(); // Berilgan javob
            $table->text('text_answer')->nullable(); // Matnli javob (essay uchun)

            // Baholash
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_earned', 5, 2)->nullable();
            $table->text('feedback')->nullable(); // O'qituvchi izohi
            $table->foreignId('graded_by')->nullable()->constrained('users');
            $table->datetime('graded_at')->nullable();

            // Vaqt
            $table->integer('time_spent_seconds')->nullable();
            $table->datetime('answered_at')->nullable();
            $table->boolean('is_flagged')->default(false); // Keyinga qoldirilgan

            $table->timestamps();

            $table->index(['attempt_id', 'question_id']);
            $table->unique(['attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_exam_answers');
        Schema::dropIfExists('lms_exam_attempts');
        Schema::dropIfExists('lms_exam_questions');
        Schema::dropIfExists('lms_exams');
    }
};
