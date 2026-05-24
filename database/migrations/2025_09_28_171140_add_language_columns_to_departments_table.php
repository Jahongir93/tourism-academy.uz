<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('departments', 'name_uz')) {
                $table->string('name_uz')->after('name')->nullable();
            }
            if (!Schema::hasColumn('departments', 'name_ru')) {
                $table->string('name_ru')->after('name_uz')->nullable();
            }
            if (!Schema::hasColumn('departments', 'name_en')) {
                $table->string('name_en')->after('name_ru')->nullable();
            }
        });

        // After adding columns, copy existing name values if they exist
        if (Schema::hasColumn('departments', 'name')) {
            DB::statement('UPDATE departments SET name_uz = name WHERE name_uz IS NULL');
            DB::statement('UPDATE departments SET name_ru = name WHERE name_ru IS NULL');
            DB::statement('UPDATE departments SET name_en = name WHERE name_en IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['name_uz', 'name_ru', 'name_en']);
        });
    }
};