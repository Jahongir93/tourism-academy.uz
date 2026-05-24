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
        Schema::create('specialty_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialty_id')->constrained('specialties')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('semester'); // 1-8
            $table->integer('course_year'); // 1-4
            $table->boolean('is_required')->default(true); // majburiy yoki tanlov
            $table->integer('credits')->nullable();
            $table->integer('hours_total')->nullable();
            $table->timestamps();

            // Bir specialty da bir fan bir semestrda faqat bir marta
            $table->unique(['specialty_id', 'subject_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialty_subjects');
    }
};
