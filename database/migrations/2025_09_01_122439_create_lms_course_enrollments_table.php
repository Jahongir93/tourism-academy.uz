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
        Schema::create('lms_course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->datetime('enrolled_at');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('status')->default('enrolled'); // enrolled, in_progress, completed, dropped
            $table->boolean('certificate_issued')->default(false);
            $table->string('certificate_number')->nullable();
            $table->datetime('certificate_issued_at')->nullable();
            $table->integer('login_count')->default(0);
            $table->integer('total_study_time')->default(0); // minutlarda
            $table->datetime('last_accessed_at')->nullable();
            $table->json('completed_resources')->nullable(); // Tugatilgan resurslar ro'yxati
            $table->timestamps();
            
            $table->unique(['course_id', 'user_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_course_enrollments');
    }
};