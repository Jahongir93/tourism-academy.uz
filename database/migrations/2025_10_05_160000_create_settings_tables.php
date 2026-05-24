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
        // Track if we created the system_settings table
        $createdSystemSettings = false;

        // System settings table - Skip if exists
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('type')->default('string'); // string, boolean, integer, json, file
                $table->string('group')->default('general'); // general, academic, finance, system, security
                $table->string('label');
                $table->text('description')->nullable();
                $table->timestamps();
            });
            $createdSystemSettings = true;
        }

        // Academic year settings - Skip if exists
        if (!Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // 2024-2025
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_current')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Semester settings - Skip if exists
        if (!Schema::hasTable('semesters')) {
            Schema::create('semesters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
                $table->string('name'); // 1-semestr, 2-semestr
                $table->integer('semester_number'); // 1 or 2
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_current')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Notification settings - Skip if exists
        if (!Schema::hasTable('notification_settings')) {
            Schema::create('notification_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->boolean('email_notifications')->default(true);
                $table->boolean('sms_notifications')->default(false);
                $table->boolean('push_notifications')->default(true);
                $table->boolean('attendance_alerts')->default(true);
                $table->boolean('payment_reminders')->default(true);
                $table->boolean('grade_notifications')->default(true);
                $table->boolean('announcement_notifications')->default(true);
                $table->timestamps();
            });
        }

        // Email templates - Skip if exists
        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('subject');
                $table->text('body');
                $table->json('variables')->nullable(); // Available template variables
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Backup logs - Skip if exists
        if (!Schema::hasTable('backup_logs')) {
            Schema::create('backup_logs', function (Blueprint $table) {
                $table->id();
                $table->string('filename');
                $table->string('type'); // database, files, full
                $table->bigInteger('size'); // in bytes
                $table->string('status'); // success, failed, in_progress
                $table->text('path')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        // Activity logs - Skip if exists
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('action'); // create, update, delete, login, logout, etc.
                $table->string('model_type')->nullable(); // Model class name
                $table->unsignedBigInteger('model_id')->nullable();
                $table->text('description');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['model_type', 'model_id']);
            });
        }

        // Only insert default system settings if we created the table
        if ($createdSystemSettings) {
            DB::table('system_settings')->insert([
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Tourism Academy',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Sayt nomi',
                'description' => 'Tizimning asosiy nomi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'file',
                'group' => 'general',
                'label' => 'Tizim logotipi',
                'description' => 'Asosiy logo fayli',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_favicon',
                'value' => null,
                'type' => 'file',
                'group' => 'general',
                'label' => 'Favicon',
                'description' => 'Brauzer yorlig\'i ikonkasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Tashkent',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Vaqt mintaqasi',
                'description' => 'Tizim vaqt mintaqasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'language',
                'value' => 'uz',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Til',
                'description' => 'Asosiy til',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Academic Settings
            [
                'key' => 'attendance_required_percentage',
                'value' => '80',
                'type' => 'integer',
                'group' => 'academic',
                'label' => 'Minimal davomat foizi',
                'description' => 'Talabalar uchun minimal davomat talabi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'passing_grade',
                'value' => '56',
                'type' => 'integer',
                'group' => 'academic',
                'label' => 'O\'tish bali',
                'description' => 'Fanlardan o\'tish uchun minimal ball',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'max_absences_per_month',
                'value' => '3',
                'type' => 'integer',
                'group' => 'academic',
                'label' => 'Oylik maksimal yo\'qlama',
                'description' => 'Bir oyda ruxsat etilgan maksimal yo\'qlama soni',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Finance Settings
            [
                'key' => 'currency',
                'value' => 'UZS',
                'type' => 'string',
                'group' => 'finance',
                'label' => 'Valyuta',
                'description' => 'Asosiy valyuta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'payment_deadline_days',
                'value' => '5',
                'type' => 'integer',
                'group' => 'finance',
                'label' => 'To\'lov muddati (kunlarda)',
                'description' => 'Har oylik to\'lov muddati',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_payment_fee_percentage',
                'value' => '5',
                'type' => 'integer',
                'group' => 'finance',
                'label' => 'Kechikkan to\'lov jarima foizi',
                'description' => 'Kechikkan to\'lovlar uchun jarima foizi',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // System Settings
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'label' => 'Texnik ishlar rejimi',
                'description' => 'Tizimni texnik ishlar rejimiga o\'tkazish',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'auto_backup_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'system',
                'label' => 'Avtomatik zaxira nusxa',
                'description' => 'Avtomatik zaxira nusxa yaratish',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'group' => 'system',
                'label' => 'Zaxira chastotasi',
                'description' => 'Zaxira nusxa yaratish chastotasi (daily, weekly, monthly)',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Security Settings
            [
                'key' => 'session_timeout',
                'value' => '120',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Sessiya muddati (daqiqalarda)',
                'description' => 'Foydalanuvchi sessiyasi tugash vaqti',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Maksimal login urinishlari',
                'description' => 'Bloklanishdan oldingi maksimal muvaffaqiyatsiz login urinishlari',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Parol minimal uzunligi',
                'description' => 'Parolning minimal belgilar soni',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'require_password_special_chars',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'label' => 'Maxsus belgilar talab qilinadi',
                'description' => 'Parolda maxsus belgilar bo\'lishi shart',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('backup_logs');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('notification_settings');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('system_settings');
    }
};
