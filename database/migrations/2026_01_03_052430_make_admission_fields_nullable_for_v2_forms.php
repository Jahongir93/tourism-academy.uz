<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // NULL qiymatlarni bo'sh string/0 ga almashtir — NOT NULL qilishdan oldin
        DB::statement("UPDATE admission_applications SET passport_series  = ''  WHERE passport_series  IS NULL");
        DB::statement("UPDATE admission_applications SET passport_number  = ''  WHERE passport_number  IS NULL");
        DB::statement("UPDATE admission_applications SET jshshir          = ''  WHERE jshshir          IS NULL");
        DB::statement("UPDATE admission_applications SET region           = ''  WHERE region           IS NULL");
        DB::statement("UPDATE admission_applications SET district         = ''  WHERE district         IS NULL");
        DB::statement("UPDATE admission_applications SET address          = ''  WHERE address          IS NULL");
        DB::statement("UPDATE admission_applications SET education_type   = 'school' WHERE education_type  IS NULL");
        DB::statement("UPDATE admission_applications SET education_name   = ''  WHERE education_name   IS NULL");
        DB::statement("UPDATE admission_applications SET graduation_year  = 0   WHERE graduation_year  IS NULL");
        DB::statement("UPDATE admission_applications SET education_form   = 'kunduzgi' WHERE education_form IS NULL");
        DB::statement("UPDATE admission_applications SET education_language = 'uz' WHERE education_language IS NULL");
        DB::statement("UPDATE admission_applications SET photo_path       = ''  WHERE photo_path       IS NULL");
        DB::statement("UPDATE admission_applications SET passport_copy_path = '' WHERE passport_copy_path IS NULL");
        DB::statement("UPDATE admission_applications SET diploma_copy_path  = '' WHERE diploma_copy_path  IS NULL");

        Schema::table('admission_applications', function (Blueprint $table) {
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

        Schema::table('admission_applications', function (Blueprint $table) {
            $table->unique('jshshir');
        });
    }
};
