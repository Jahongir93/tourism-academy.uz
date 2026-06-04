<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Public teacher profile fields (also shown on /teachers, /cms/teachers)
            if (!Schema::hasColumn('employees', 'bio_uz')) {
                $table->text('bio_uz')->nullable()->after('position');
            }
            if (!Schema::hasColumn('employees', 'bio_ru')) {
                $table->text('bio_ru')->nullable()->after('bio_uz');
            }
            if (!Schema::hasColumn('employees', 'bio_en')) {
                $table->text('bio_en')->nullable()->after('bio_ru');
            }
            // Show on public site toggle + ordering
            if (!Schema::hasColumn('employees', 'show_on_site')) {
                $table->boolean('show_on_site')->default(true)->after('bio_en');
            }
            if (!Schema::hasColumn('employees', 'public_order')) {
                $table->integer('public_order')->default(0)->after('show_on_site');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            foreach (['bio_uz', 'bio_ru', 'bio_en', 'show_on_site', 'public_order'] as $col) {
                if (Schema::hasColumn('employees', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
