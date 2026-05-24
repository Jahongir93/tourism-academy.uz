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
        Schema::table('curricula', function (Blueprint $table) {
            $table->foreignId('program_id')->constrained('educational_programs')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('academic_year', 20);
            $table->integer('semester_number');
            $table->integer('sequence_number')->default(0);
            $table->integer('credits');
            $table->integer('total_hours');
            $table->integer('lecture_hours')->default(0);
            $table->integer('practice_hours')->default(0);
            $table->integer('lab_hours')->default(0);
            $table->integer('seminar_hours')->default(0);
            $table->integer('independent_hours')->default(0);
            $table->enum('control_type', ['imtihon', 'sinov', 'kurs_ishi', 'differensial_sinov'])->default('imtihon');
            $table->boolean('is_optional')->default(false);
            $table->string('prerequisites')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            
            $table->index(['program_id', 'academic_year']);
            $table->index(['semester_number', 'sequence_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropForeign(['subject_id']);
            $table->dropColumn([
                'program_id',
                'subject_id',
                'academic_year',
                'semester_number',
                'sequence_number',
                'credits',
                'total_hours',
                'lecture_hours',
                'practice_hours',
                'lab_hours',
                'seminar_hours',
                'independent_hours',
                'control_type',
                'is_optional',
                'prerequisites',
                'description',
                'active'
            ]);
        });
    }
};