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
        Schema::create('lms_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->string('certificate_type'); // course_completion, achievement, participation
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->json('metadata')->nullable(); // additional certificate data
            $table->string('file_path')->nullable();
            $table->string('verification_code')->unique();
            $table->boolean('is_verified')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'subject_id']);
            $table->index('certificate_type');
            $table->index('verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_certificates');
    }
};