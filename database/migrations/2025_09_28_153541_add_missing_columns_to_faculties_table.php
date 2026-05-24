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
        Schema::table('faculties', function (Blueprint $table) {
            // Rename existing 'name' column to 'name_uz'
            $table->renameColumn('name', 'name_uz');

            // Add missing columns after code column
            $table->after('code', function (Blueprint $table) {
                $table->string('name_ru')->nullable();
                $table->string('name_en')->nullable();
                $table->string('short_name')->nullable();
                $table->unsignedBigInteger('dean_user_id')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('room')->nullable();
                $table->integer('order_number')->default(0);
                $table->boolean('is_active')->default(true);
                $table->string('abbreviation')->nullable();
                $table->string('logo')->nullable();
                $table->string('website')->nullable();
                $table->date('established_date')->nullable();
                $table->integer('student_capacity')->nullable();
                $table->integer('teacher_capacity')->nullable();
                $table->integer('state_funded_places')->nullable();
                $table->integer('contract_places')->nullable();
                $table->unsignedBigInteger('university_id')->nullable();
            });

            // Add foreign key constraints
            $table->foreign('dean_user_id')->references('id')->on('users')->onDelete('set null');
            // Note: university_id foreign key will be added when universities table is properly set up
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculties', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['dean_user_id']);

            // Drop added columns
            $table->dropColumn([
                'name_ru', 'name_en', 'short_name', 'dean_user_id',
                'phone', 'email', 'room', 'order_number', 'is_active',
                'abbreviation', 'logo', 'website', 'established_date',
                'student_capacity', 'teacher_capacity', 'state_funded_places',
                'contract_places', 'university_id'
            ]);

            // Rename 'name_uz' back to 'name'
            $table->renameColumn('name_uz', 'name');
        });
    }
};
