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
        // Add indexes to lms_courses table for better query performance
        $this->addIndexIfNotExists('lms_courses', [
            'is_published',
            'is_featured',
            'is_archived',
            'subject_id',
            'teacher_id',
            'level',
            'created_at'
        ]);

        // Add indexes to lms_materials table
        $this->addIndexIfNotExists('lms_materials', [
            'is_active',
            'subject_id',
            'teacher_id',
            'material_type',
            'created_at'
        ]);

        // Add indexes to lms_videos table
        $this->addIndexIfNotExists('lms_videos', [
            'is_active',
            'subject_id',
            'teacher_id',
            'created_at'
        ]);

        // Add indexes to lms_practice_tests table
        $this->addIndexIfNotExists('lms_practice_tests', [
            'is_active',
            'subject_id',
            'teacher_id',
            'available_from',
            'available_until'
        ]);

        // Add indexes to lms_library_books table
        $this->addIndexIfNotExists('lms_library_books', [
            'is_active',
            'is_featured',
            'category_id',
            'created_at'
        ]);

        // Add indexes to lms_forum_posts table
        $this->addIndexIfNotExists('lms_forum_posts', [
            'parent_id',
            'subject_id',
            'user_id',
            'created_at'
        ]);

        // Add indexes to lms_course_enrollments table
        $this->addIndexIfNotExists('lms_course_enrollments', [
            'course_id',
            'user_id',
            'status'
        ]);

        // Add indexes to lms_certificates table
        $this->addIndexIfNotExists('lms_certificates', [
            'user_id',
            'course_id',
            'subject_id'
        ]);

        // Add indexes to lms_content_views table
        $this->addIndexIfNotExists('lms_content_views', [
            'user_id',
            'viewable_type',
            'viewable_id'
        ]);
    }

    /**
     * Helper function to add index if it doesn't exist
     */
    private function addIndexIfNotExists($tableName, $columns)
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        foreach ($columns as $column) {
            try {
                $indexName = $tableName . '_' . $column . '_index';
                DB::statement("ALTER TABLE `{$tableName}` ADD INDEX `{$indexName}` (`{$column}`)");
            } catch (\Exception $e) {
                // Index already exists, skip
                if (strpos($e->getMessage(), 'Duplicate key name') === false) {
                    \Log::warning("Could not add index {$indexName}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't drop indexes in down() to avoid breaking existing queries
        // If needed, they can be dropped manually
    }
};
