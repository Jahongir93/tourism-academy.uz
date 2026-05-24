<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Make subject_id nullable in materials table
        Schema::table('lms_materials', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->change();
        });
        
        // Make subject_id nullable in forum posts table
        Schema::table('lms_forum_posts', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->change();
        });
        
        // Make subject_id nullable in videos table if it exists
        if (Schema::hasTable('lms_videos')) {
            Schema::table('lms_videos', function (Blueprint $table) {
                $table->foreignId('subject_id')->nullable()->change();
            });
        }
    }

    public function down()
    {
        Schema::table('lms_materials', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable(false)->change();
        });
        
        Schema::table('lms_forum_posts', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable(false)->change();
        });
        
        if (Schema::hasTable('lms_videos')) {
            Schema::table('lms_videos', function (Blueprint $table) {
                $table->foreignId('subject_id')->nullable(false)->change();
            });
        }
    }
};