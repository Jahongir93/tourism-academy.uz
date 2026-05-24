<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('students', ['group_id', 'status'], 'students_group_id_status_index');
        $this->addIndexIfMissing('students', ['user_id'], 'students_user_id_index');
        $this->addIndexIfMissing('journal_grades', ['student_id'], 'journal_grades_student_id_index');
        $this->addIndexIfMissing('journal_grades', ['journal_entry_id'], 'journal_grades_journal_entry_id_index');
        $this->addIndexIfMissing('journal_grades', ['grade_type'], 'journal_grades_grade_type_index');
        $this->addIndexIfMissing('journal_entries', ['teacher_id'], 'journal_entries_teacher_id_index');
        $this->addIndexIfMissing('journal_entries', ['group_id'], 'journal_entries_group_id_index');
        $this->addIndexIfMissing('journal_entries', ['subject_id'], 'journal_entries_subject_id_index');
        $this->addIndexIfMissing('group_subjects', ['teacher_id', 'is_active'], 'group_subjects_teacher_id_is_active_index');
        $this->addIndexIfMissing('group_subjects', ['student_group_id'], 'group_subjects_student_group_id_index');
        $this->addIndexIfMissing('attendances', ['user_id', 'date'], 'attendances_user_id_date_index');
        $this->addIndexIfMissing('attendances', ['student_id', 'date'], 'attendances_student_id_date_index');
        $this->addIndexIfMissing('cms_news', ['status', 'published_at'], 'cms_news_status_published_at_index');
        $this->addIndexIfMissing('lms_courses', ['teacher_id', 'is_published'], 'lms_courses_teacher_id_is_published_index');
    }

    public function down(): void
    {
        $indexes = [
            'students'        => ['students_group_id_status_index', 'students_user_id_index'],
            'journal_grades'  => ['journal_grades_student_id_index', 'journal_grades_journal_entry_id_index', 'journal_grades_grade_type_index'],
            'journal_entries' => ['journal_entries_teacher_id_index', 'journal_entries_group_id_index', 'journal_entries_subject_id_index'],
            'group_subjects'  => ['group_subjects_teacher_id_is_active_index', 'group_subjects_student_group_id_index'],
            'attendances'     => ['attendances_user_id_date_index', 'attendances_student_id_date_index'],
            'cms_news'        => ['cms_news_status_published_at_index'],
            'lms_courses'     => ['lms_courses_teacher_id_is_published_index'],
        ];

        foreach ($indexes as $table => $names) {
            if (Schema::hasTable($table)) {
                foreach ($names as $name) {
                    Schema::table($table, fn(Blueprint $t) => $t->dropIndexIfExists($name));
                }
            }
        }
    }

    private function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        // Check if index already exists
        $exists = collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->contains('Key_name', $indexName);

        if (!$exists) {
            Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                $t->index($columns, $indexName);
            });
        }
    }
};
