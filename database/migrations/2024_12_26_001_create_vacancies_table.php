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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_ru')->nullable();
            $table->string('title_en')->nullable();
            $table->string('department')->nullable(); // Bo'lim
            $table->text('description')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_en')->nullable();
            $table->text('requirements')->nullable(); // Talablar
            $table->text('requirements_ru')->nullable();
            $table->text('requirements_en')->nullable();
            $table->text('responsibilities')->nullable(); // Vazifalar
            $table->text('responsibilities_ru')->nullable();
            $table->text('responsibilities_en')->nullable();
            $table->text('benefits')->nullable(); // Imtiyozlar
            $table->text('benefits_ru')->nullable();
            $table->text('benefits_en')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship'])->default('full_time');
            $table->string('salary_range')->nullable();
            $table->string('experience_required')->nullable(); // Tajriba
            $table->string('education_required')->nullable(); // Ma'lumot
            $table->date('deadline')->nullable(); // Ariza berish muddati
            $table->integer('positions_count')->default(1); // Ochiq o'rinlar soni
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
