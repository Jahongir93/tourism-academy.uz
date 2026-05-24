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
        Schema::table('lms_library_books', function (Blueprint $table) {
            if (Schema::hasColumn('lms_library_books', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('lms_library_books', 'book_type')) {
                $table->dropColumn('book_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_library_books', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->string('book_type')->nullable();
        });
    }
};
