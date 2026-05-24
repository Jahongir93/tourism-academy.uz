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
        Schema::create('group_subject_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_subject_id')->constrained('group_subjects')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // Baho turlari
            $table->decimal('current_grade', 5, 2)->nullable(); // Joriy baho (0-100)
            $table->decimal('midterm_grade', 5, 2)->nullable(); // Oraliq baho (0-100)
            $table->decimal('final_grade', 5, 2)->nullable(); // Yakuniy baho (0-100)

            // Qo'shimcha ustunlar (dinamik)
            $table->json('additional_grades')->nullable(); // {"Lab 1": 85, "Amaliy ish 1": 90}

            // Umumiy natija
            $table->decimal('total_score', 5, 2)->nullable(); // Umumiy ball (0-100)
            $table->string('letter_grade', 2)->nullable(); // A, B, C, D, F
            $table->boolean('is_passed')->default(false); // O'tdi/o'tmadi

            $table->text('notes')->nullable(); // Izohlar
            $table->timestamps();

            // Bir talaba bir fanda faqat bitta baho yozuvi
            $table->unique(['group_subject_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_subject_grades');
    }
};
