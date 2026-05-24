<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_groups')) {
            Schema::create('student_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique()->nullable();
                $table->unsignedBigInteger('faculty_id')->nullable();
                $table->unsignedBigInteger('specialty_id')->nullable();
                $table->unsignedBigInteger('curator_id')->nullable();
                $table->integer('course')->default(1);
                $table->integer('max_students')->default(30);
                $table->integer('current_students')->default(0);
                $table->enum('education_form', ['kunduzgi', 'sirtqi', 'kechki', 'masofaviy'])->default('kunduzgi');
                $table->enum('education_type', ['byudjet', 'shartnoma'])->default('shartnoma');
                $table->string('academic_year')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index('faculty_id');
                $table->index('specialty_id');
                $table->index('curator_id');
                $table->index('course');
                $table->index('is_active');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_groups');
    }
};