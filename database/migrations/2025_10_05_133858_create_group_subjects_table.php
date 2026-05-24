<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only create if doesn't exist - prevents duplicate table error
        if (!Schema::hasTable('group_subjects')) {
            Schema::create('group_subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
                $table->integer('semester')->default(1);
                $table->string('schedule')->nullable();
                $table->string('room')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['group_id', 'subject_id', 'academic_year_id', 'semester'], 'group_subject_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('group_subjects');
    }
};
