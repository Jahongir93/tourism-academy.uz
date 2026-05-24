<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_passports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('passport_series')->nullable();
            $table->string('passport_number');
            $table->date('issue_date');
            $table->string('issued_by');
            $table->date('expiry_date')->nullable();
            $table->string('nationality')->default('Uzbekistan');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->string('registration_address')->nullable();
            $table->string('actual_address')->nullable();
            $table->timestamps();

            $table->unique(['passport_series', 'passport_number']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_passports');
    }
};