<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create resource progress tracking table if not exists
        if (!Schema::hasTable('lms_resource_progress')) {
            Schema::create('lms_resource_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
                $table->foreignId('resource_id')->constrained('lms_course_resources')->onDelete('cascade');
                $table->boolean('is_completed')->default(false);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->integer('time_spent')->default(0);
                $table->decimal('progress_percentage', 5, 2)->default(0);
                $table->timestamps();
                
                $table->unique(['user_id', 'resource_id']);
                $table->index(['user_id', 'course_id']);
            });
        }

        // Create live sessions table if not exists
        if (!Schema::hasTable('lms_live_sessions')) {
            Schema::create('lms_live_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->nullable()->constrained('lms_courses')->onDelete('cascade');
                $table->foreignId('teacher_id')->constrained('employees')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('meeting_url')->nullable();
                $table->string('meeting_id')->nullable();
                $table->string('meeting_password')->nullable();
                $table->string('platform')->default('zoom');
                $table->timestamp('scheduled_at');
                $table->integer('duration')->default(60);
                $table->integer('max_participants')->nullable();
                $table->boolean('is_recorded')->default(false);
                $table->string('recording_url')->nullable();
                $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
                $table->timestamps();
                
                $table->index(['scheduled_at', 'status']);
            });
        }

        // Create test questions table if not exists
        if (!Schema::hasTable('lms_test_questions')) {
            Schema::create('lms_test_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('test_id')->constrained('lms_practice_tests')->onDelete('cascade');
                $table->text('question');
                $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay', 'matching']);
                $table->json('options')->nullable();
                $table->json('correct_answer');
                $table->text('explanation')->nullable();
                $table->integer('points')->default(1);
                $table->integer('order_number')->default(0);
                $table->string('image')->nullable();
                $table->boolean('is_required')->default(true);
                $table->timestamps();
                
                $table->index(['test_id', 'order_number']);
            });
        }

        // Create test attempts table if not exists
        if (!Schema::hasTable('lms_test_attempts')) {
            Schema::create('lms_test_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('test_id')->constrained('lms_practice_tests')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('attempt_number')->default(1);
                $table->timestamp('started_at');
                $table->timestamp('submitted_at')->nullable();
                $table->integer('time_spent')->default(0);
                $table->decimal('score', 5, 2)->nullable();
                $table->decimal('percentage', 5, 2)->nullable();
                $table->enum('status', ['in_progress', 'submitted', 'graded', 'expired'])->default('in_progress');
                $table->json('answers')->nullable();
                $table->json('question_order')->nullable();
                $table->text('feedback')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'test_id']);
                $table->unique(['test_id', 'user_id', 'attempt_number']);
            });
        }

        // Create certificate templates if not exists
        if (!Schema::hasTable('lms_certificate_templates')) {
            Schema::create('lms_certificate_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('background_image')->nullable();
                $table->json('layout');
                $table->json('fonts')->nullable();
                $table->json('colors')->nullable();
                $table->boolean('has_qr')->default(true);
                $table->boolean('has_signature')->default(true);
                $table->boolean('has_seal')->default(true);
                $table->string('signature_image')->nullable();
                $table->string('seal_image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lms_certificate_templates');
        Schema::dropIfExists('lms_test_attempts');
        Schema::dropIfExists('lms_test_questions');
        Schema::dropIfExists('lms_live_sessions');
        Schema::dropIfExists('lms_resource_progress');
    }
};