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
        Schema::create('lms_course_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('resource_type'); // video, document, presentation, audio, link, assignment, quiz
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('external_url')->nullable(); // YouTube, Vimeo links
            $table->integer('duration')->nullable(); // Video/audio davomiyligi (sekundlarda)
            $table->integer('week_number')->nullable();
            $table->integer('order_number')->default(0);
            $table->boolean('is_mandatory')->default(true); // Majburiy resurs
            $table->boolean('is_downloadable')->default(true);
            $table->boolean('is_published')->default(true);
            $table->datetime('available_from')->nullable();
            $table->datetime('available_until')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->json('metadata')->nullable(); // Qo'shimcha ma'lumotlar
            $table->timestamps();
            
            $table->index(['course_id', 'resource_type']);
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_course_resources');
    }
};