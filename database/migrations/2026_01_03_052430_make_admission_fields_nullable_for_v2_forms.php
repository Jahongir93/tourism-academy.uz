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
        Schema::table('admission_applications', function (Blueprint $table) {
            // Make fields nullable for version 2 forms (module-based applications)
            $table->string('passport_series', 2)->nullable()->change();
            $table->string('passport_number', 7)->nullable()->change();
            $table->string('jshshir', 14)->nullable()->change();
            $table->string('region')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('education_type')->nullable()->change();
            $table->string('education_name')->nullable()->change();
            $table->integer('graduation_year')->nullable()->change();
            $table->foreignId('faculty_id')->nullable()->change();
            $table->foreignId('specialty_id')->nullable()->change();
            $table->string('education_form')->nullable()->change();
            $table->string('education_language')->nullable()->change();
            $table->string('photo_path')->nullable()->change();
            $table->string('passport_copy_path')->nullable()->change();
            $table->string('diploma_copy_path')->nullable()->change();
        });

        // Drop unique constraint on jshshir to allow empty values
        Schema::table('admission_applications', function (Blueprint $table) {
            $table->dropUnique(['jshshir']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_applications', function (Blueprint $table) {
            // Revert fields back to NOT NULL
            $table->string('passport_series', 2)->nullable(false)->change();
            $table->string('passport_number', 7)->nullable(false)->change();
            $table->string('jshshir', 14)->nullable(false)->change();
            $table->string('region')->nullable(false)->change();
            $table->string('district')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->string('education_type')->nullable(false)->change();
            $table->string('education_name')->nullable(false)->change();
            $table->integer('graduation_year')->nullable(false)->change();
            $table->foreignId('faculty_id')->nullable(false)->change();
            $table->foreignId('specialty_id')->nullable(false)->change();
            $table->string('education_form')->nullable(false)->change();
            $table->string('education_language')->nullable(false)->change();
            $table->string('photo_path')->nullable(false)->change();
            $table->string('passport_copy_path')->nullable(false)->change();
            $table->string('diploma_copy_path')->nullable(false)->change();
        });

        // Re-add unique constraint on jshshir
        Schema::table('admission_applications', function (Blueprint $table) {
            $table->unique('jshshir');
        });
    }
};
