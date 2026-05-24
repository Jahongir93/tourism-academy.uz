<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('face_recognition_settings')) {
            Schema::create('face_recognition_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('type')->default('string'); // string, boolean, json, number
                $table->text('description')->nullable();
                $table->timestamps();
            });

            // Default settings
            DB::table('face_recognition_settings')->insert([
                [
                    'key' => 'api_url',
                    'value' => 'http://localhost:5000',
                    'type' => 'string',
                    'description' => 'Face recognition API URL',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'enabled',
                    'value' => 'true',
                    'type' => 'boolean',
                    'description' => 'Face recognition feature enabled',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'confidence_threshold',
                    'value' => '0.8',
                    'type' => 'number',
                    'description' => 'Minimum confidence score for face recognition',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'max_faces_per_image',
                    'value' => '1',
                    'type' => 'number',
                    'description' => 'Maximum number of faces to detect per image',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('face_recognition_settings');
    }
};