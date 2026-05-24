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
        // Face encodings table
        if (!Schema::hasTable('face_encodings')) {
            Schema::create('face_encodings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->text('encoding_data'); // JSON encoded face data
                $table->string('image_path')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->unique('user_id'); // One encoding per user
            });
        }

        // Update attendances table if it doesn't have all columns
        if (Schema::hasTable('attendances')) {
            if (!Schema::hasColumn('attendances', 'face_confidence_score')) {
                Schema::table('attendances', function (Blueprint $table) {
                    $table->decimal('face_confidence_score', 5, 2)->nullable()->after('status');
                });
            }
        }

        // Attendance logs table
        if (!Schema::hasTable('attendance_logs')) {
            Schema::create('attendance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
                $table->enum('action_type', ['check_in', 'check_out', 'manual_entry']);
                $table->string('image_path')->nullable();
                $table->decimal('confidence_score', 5, 2)->nullable();
                $table->timestamp('timestamp');
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['attendance_id', 'action_type']);
                $table->index('timestamp');
            });
        }

        // Face recognition settings table
        if (!Schema::hasTable('face_recognition_settings')) {
            Schema::create('face_recognition_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value');
                $table->string('type')->default('string'); // string, integer, boolean, json
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Insert default settings
        $this->insertDefaultSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('face_recognition_settings');
        Schema::dropIfExists('attendance_logs');

        if (Schema::hasColumn('attendances', 'face_confidence_score')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn(['face_confidence_score', 'location']);
            });
        }

        Schema::dropIfExists('face_encodings');
    }

    /**
     * Insert default face recognition settings
     */
    private function insertDefaultSettings(): void
    {
        $settings = [
            [
                'key' => 'min_confidence_score',
                'value' => '85',
                'type' => 'integer',
                'description' => 'Minimum confidence score for face recognition (0-100)',
            ],
            [
                'key' => 'recognition_tolerance',
                'value' => '0.6',
                'type' => 'float',
                'description' => 'Face recognition tolerance (lower is stricter)',
            ],
            [
                'key' => 'min_images_for_registration',
                'value' => '3',
                'type' => 'integer',
                'description' => 'Minimum number of images required for registration',
            ],
            [
                'key' => 'office_start_time',
                'value' => '09:00',
                'type' => 'string',
                'description' => 'Office start time',
            ],
            [
                'key' => 'late_tolerance_minutes',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Minutes after office start to be considered late',
            ],
            [
                'key' => 'api_url',
                'value' => 'http://localhost:5000',
                'type' => 'string',
                'description' => 'Face recognition API URL',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('face_recognition_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
};