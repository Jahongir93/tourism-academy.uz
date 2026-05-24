<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add category_id and other columns to existing library_books table
        Schema::table('lms_library_books', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('lms_library_books', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('lms_library_categories');
            }
            
            if (!Schema::hasColumn('lms_library_books', 'file_path')) {
                $table->string('file_path')->nullable();
            }
            
            if (!Schema::hasColumn('lms_library_books', 'file_name')) {
                $table->string('file_name')->nullable();
            }
            
            if (!Schema::hasColumn('lms_library_books', 'file_type')) {
                $table->string('file_type')->nullable();
            }
            
            if (!Schema::hasColumn('lms_library_books', 'file_size')) {
                $table->integer('file_size')->default(0);
            }
            
            if (!Schema::hasColumn('lms_library_books', 'allow_download')) {
                $table->boolean('allow_download')->default(true);
            }
            
            if (!Schema::hasColumn('lms_library_books', 'allow_online_reading')) {
                $table->boolean('allow_online_reading')->default(true);
            }
            
            if (!Schema::hasColumn('lms_library_books', 'uploaded_by')) {
                $table->foreignId('uploaded_by')->nullable()->constrained('users');
            }
        });
    }

    public function down()
    {
        Schema::table('lms_library_books', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->dropColumn(['file_path', 'file_name', 'file_type', 'file_size']);
            $table->dropColumn(['allow_download', 'allow_online_reading']);
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn('uploaded_by');
        });
    }
};