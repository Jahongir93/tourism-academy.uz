<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->string('passport_series', 2);
            $table->string('passport_number', 7);
            $table->string('jshshir', 14)->unique();

            // Contact Information
            $table->string('phone', 20);
            $table->string('email')->unique();
            $table->string('region');
            $table->string('district');
            $table->text('address');

            // Education Information
            $table->enum('education_type', ['school', 'college', 'lyceum', 'bachelor']);
            $table->string('education_name');
            $table->integer('graduation_year');
            $table->integer('dtm_score')->nullable();

            // Program Selection
            $table->foreignId('faculty_id')->constrained();
            $table->foreignId('specialty_id')->constrained();
            $table->enum('education_form', ['kunduzgi', 'sirtqi', 'kechki']);
            $table->enum('education_language', ['uz', 'ru', 'en']);

            // Document Paths
            $table->string('photo_path');
            $table->string('passport_copy_path');
            $table->string('diploma_copy_path');
            $table->string('certificate_copy_path')->nullable();

            // Status
            $table->enum('status', ['pending', 'reviewing', 'accepted', 'rejected', 'waitlist'])->default('pending');
            $table->timestamp('applied_at');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('faculty_id');
            $table->index('specialty_id');
            $table->index('applied_at');
            $table->index('region');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admission_applications');
    }
};