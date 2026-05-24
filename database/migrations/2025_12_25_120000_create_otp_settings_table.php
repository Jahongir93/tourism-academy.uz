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
        Schema::create('otp_settings', function (Blueprint $table) {
            $table->id();

            // SMS Provider Settings
            $table->string('sms_provider')->default('eskiz'); // eskiz, playmobile, custom
            $table->string('sms_api_url')->nullable();
            $table->string('sms_api_key')->nullable();
            $table->string('sms_api_secret')->nullable();
            $table->string('sms_sender_name')->default('TourismAcad');

            // Email Settings
            $table->string('email_provider')->default('smtp'); // smtp, mailgun, sendgrid
            $table->boolean('email_verification_enabled')->default(true);

            // OTP Configuration
            $table->integer('otp_length')->default(6);
            $table->integer('otp_expiry_minutes')->default(5);
            $table->integer('max_attempts')->default(3);
            $table->integer('resend_cooldown_seconds')->default(60);

            // Rate Limiting
            $table->integer('max_otp_per_hour')->default(5);
            $table->integer('max_otp_per_day')->default(10);

            // Message Templates
            $table->text('sms_template_uz')->nullable();
            $table->text('sms_template_ru')->nullable();
            $table->text('sms_template_en')->nullable();
            $table->text('email_subject_uz')->nullable();
            $table->text('email_subject_ru')->nullable();
            $table->text('email_subject_en')->nullable();
            $table->text('email_template_uz')->nullable();
            $table->text('email_template_ru')->nullable();
            $table->text('email_template_en')->nullable();

            // Status
            $table->boolean('sms_enabled')->default(true);
            $table->boolean('is_test_mode')->default(false);
            $table->string('test_otp_code')->nullable(); // For testing

            $table->timestamps();
        });

        // Insert default settings
        DB::table('otp_settings')->insert([
            'sms_provider' => 'eskiz',
            'sms_api_url' => 'https://notify.eskiz.uz/api',
            'sms_sender_name' => 'TourismAcad',
            'otp_length' => 6,
            'otp_expiry_minutes' => 5,
            'max_attempts' => 3,
            'resend_cooldown_seconds' => 60,
            'max_otp_per_hour' => 5,
            'max_otp_per_day' => 10,
            'sms_template_uz' => 'Tourism Academy Samarkand. Sizning tasdiqlash kodingiz: {otp}. Kod {minutes} daqiqa amal qiladi.',
            'sms_template_ru' => 'Tourism Academy Samarkand. Ваш код подтверждения: {otp}. Код действителен {minutes} минут.',
            'sms_template_en' => 'Tourism Academy Samarkand. Your verification code is: {otp}. Code is valid for {minutes} minutes.',
            'email_subject_uz' => 'Tasdiqlash kodi - Tourism Academy',
            'email_subject_ru' => 'Код подтверждения - Tourism Academy',
            'email_subject_en' => 'Verification Code - Tourism Academy',
            'email_template_uz' => '<h2>Tasdiqlash kodi</h2><p>Sizning tasdiqlash kodingiz: <strong>{otp}</strong></p><p>Kod {minutes} daqiqa amal qiladi.</p>',
            'email_template_ru' => '<h2>Код подтверждения</h2><p>Ваш код подтверждения: <strong>{otp}</strong></p><p>Код действителен {minutes} минут.</p>',
            'email_template_en' => '<h2>Verification Code</h2><p>Your verification code is: <strong>{otp}</strong></p><p>Code is valid for {minutes} minutes.</p>',
            'sms_enabled' => true,
            'email_verification_enabled' => true,
            'is_test_mode' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_settings');
    }
};
