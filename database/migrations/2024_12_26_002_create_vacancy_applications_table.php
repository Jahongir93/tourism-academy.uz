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
        Schema::create('vacancy_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();

            // Shaxsiy ma'lumotlar
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();

            // Manzil
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();

            // Ma'lumot va tajriba
            $table->string('education_level')->nullable(); // Oliy, O'rta maxsus, etc.
            $table->string('education_institution')->nullable();
            $table->string('education_specialty')->nullable();
            $table->year('graduation_year')->nullable();
            $table->integer('experience_years')->nullable();
            $table->text('work_experience')->nullable(); // Ish tajribasi tavsifi

            // Qo'shimcha
            $table->text('skills')->nullable();
            $table->text('languages')->nullable();
            $table->text('cover_letter')->nullable(); // Motivatsiya xati
            $table->string('resume_path')->nullable(); // CV fayl
            $table->string('photo_path')->nullable();

            // Qo'shimcha ma'lumotlar (JSON)
            $table->json('additional_data')->nullable();

            // Holat
            $table->enum('status', [
                'new',           // Yangi
                'reviewed',      // Ko'rib chiqilgan
                'shortlisted',   // Tanlangan
                'interview',     // Suhbatga chaqirilgan
                'offered',       // Taklif yuborilgan
                'hired',         // Ishga qabul qilingan
                'rejected'       // Rad etilgan
            ])->default('new');

            $table->text('internal_notes')->nullable(); // Ichki izohlar
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            // Javob
            $table->text('response_message')->nullable();
            $table->timestamp('response_sent_at')->nullable();
            $table->foreignId('response_sent_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vacancy_id', 'status']);
            $table->index('email');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancy_applications');
    }
};
