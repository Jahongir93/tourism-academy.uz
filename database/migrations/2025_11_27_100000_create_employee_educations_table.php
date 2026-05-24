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
        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('education_level')->nullable(); // secondary, secondary_special, bachelor, master, phd, dsc, candidate, doctor
            $table->string('institution')->nullable();
            $table->string('faculty')->nullable();
            $table->string('speciality')->nullable();
            $table->string('diploma_number')->nullable();
            $table->date('graduation_date')->nullable();
            $table->boolean('is_foreign')->default(false);
            $table->string('country')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('employee_id');
            $table->index('education_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_educations');
    }
};
