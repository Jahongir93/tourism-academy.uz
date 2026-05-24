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
            // Check and add missing columns
            if (!Schema::hasColumn('lms_library_books', 'edition')) {
                $table->string('edition')->nullable()->after('publication_year');
            }
            if (!Schema::hasColumn('lms_library_books', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('language')->constrained('lms_library_categories')->onDelete('set null');
            }
            if (!Schema::hasColumn('lms_library_books', 'tags')) {
                $table->json('tags')->nullable()->after('subjects');
            }
            if (!Schema::hasColumn('lms_library_books', 'keywords')) {
                $table->text('keywords')->nullable()->after('tags');
            }
            if (!Schema::hasColumn('lms_library_books', 'allow_download')) {
                $table->boolean('allow_download')->default(true)->after('file_path');
            }
            if (!Schema::hasColumn('lms_library_books', 'allow_online_reading')) {
                $table->boolean('allow_online_reading')->default(true)->after('allow_download');
            }
            if (!Schema::hasColumn('lms_library_books', 'file_name')) {
                $table->string('file_name')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('lms_library_books', 'file_type')) {
                $table->string('file_type')->nullable()->after('file_name');
            }
            if (!Schema::hasColumn('lms_library_books', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('file_type');
            }
            if (!Schema::hasColumn('lms_library_books', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
            }
            if (!Schema::hasColumn('lms_library_books', 'uploaded_by')) {
                $table->foreignId('uploaded_by')->nullable()->after('is_active')->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_library_books', function (Blueprint $table) {
            $columns = ['edition', 'category_id', 'tags', 'keywords', 'allow_download',
                       'allow_online_reading', 'file_name', 'file_type', 'file_size', 'slug', 'uploaded_by'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('lms_library_books', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
