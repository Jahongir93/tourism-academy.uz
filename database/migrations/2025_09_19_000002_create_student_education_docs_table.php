<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_education_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('document_type', [
                'attestat',
                'diplom',
                'academic_transcript',
                'certificate',
                'other'
            ])->default('attestat');
            $table->string('document_number');
            $table->date('issue_date');
            $table->string('issued_by');
            $table->string('institution_name')->nullable();
            $table->string('specialization')->nullable();
            $table->string('graduation_year')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('student_id');
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_education_docs');
    }
};