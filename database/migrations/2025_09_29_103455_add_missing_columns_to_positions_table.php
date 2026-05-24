<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            if (!Schema::hasColumn('positions', 'name_uz')) {
                $table->string('name_uz')->nullable()->after('name');
            }
            if (!Schema::hasColumn('positions', 'name_ru')) {
                $table->string('name_ru')->nullable()->after('name_uz');
            }
            if (!Schema::hasColumn('positions', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_ru');
            }
            if (!Schema::hasColumn('positions', 'category')) {
                $table->enum('category', ['leadership', 'academic', 'administrative', 'support'])->default('academic')->after('code');
            }
            if (!Schema::hasColumn('positions', 'level')) {
                $table->integer('level')->default(5)->after('category');
            }
            if (!Schema::hasColumn('positions', 'salary_grade')) {
                $table->string('salary_grade')->nullable()->after('level');
            }
            if (!Schema::hasColumn('positions', 'requirements')) {
                $table->json('requirements')->nullable()->after('description');
            }
            if (!Schema::hasColumn('positions', 'responsibilities')) {
                $table->json('responsibilities')->nullable()->after('requirements');
            }
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn(['name_uz', 'name_ru', 'name_en', 'category', 'level', 'salary_grade', 'requirements', 'responsibilities']);
        });
    }
};