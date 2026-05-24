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
        Schema::create('lms_course_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('week_number')->default(1);
            $table->integer('order_number')->default(1);
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['course_id', 'week_number', 'order_number']);
        });

        // Create topic resources table for attaching materials/videos/tests
        Schema::create('lms_topic_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('lms_course_topics')->onDelete('cascade');
            $table->string('resource_type'); // 'material', 'video', 'test', 'file', 'link'
            $table->unsignedBigInteger('resource_id')->nullable(); // For existing materials/videos/tests
            $table->string('file_path')->nullable(); // For uploaded files
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('external_link')->nullable(); // For external links
            $table->text('description')->nullable();
            $table->integer('order_number')->default(1);
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_downloadable')->default(true);
            $table->timestamps();

            $table->index(['topic_id', 'resource_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_topic_resources');
        Schema::dropIfExists('lms_course_topics');
    }
};
