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
        // SECURITY FIX: Add foreign key constraints for data integrity

        // 1. lms_exam_questions foreign keys
        if ($this->columnExists('lms_exam_questions', 'exam_id')) {
            Schema::table('lms_exam_questions', function (Blueprint $table) {
                if (!$this->foreignKeyExists('lms_exam_questions', 'lms_exam_questions_exam_id_foreign')) {
                    $table->foreign('exam_id')
                        ->references('id')
                        ->on('lms_exams')
                        ->onDelete('cascade'); // Delete questions when exam is deleted
                }
            });
        }

        // 2. lms_exam_attempts foreign keys
        if ($this->columnExists('lms_exam_attempts', 'exam_id')) {
            Schema::table('lms_exam_attempts', function (Blueprint $table) {
                if (!$this->foreignKeyExists('lms_exam_attempts', 'lms_exam_attempts_exam_id_foreign')) {
                    $table->foreign('exam_id')
                        ->references('id')
                        ->on('lms_exams')
                        ->onDelete('cascade'); // Delete attempts when exam is deleted
                }

                if (!$this->foreignKeyExists('lms_exam_attempts', 'lms_exam_attempts_student_id_foreign')) {
                    $table->foreign('student_id')
                        ->references('id')
                        ->on('students')
                        ->onDelete('restrict'); // Don't allow deleting students with exam attempts
                }
            });
        }

        // 3. lms_exam_answers foreign keys
        if ($this->columnExists('lms_exam_answers', 'attempt_id')) {
            Schema::table('lms_exam_answers', function (Blueprint $table) {
                if (!$this->foreignKeyExists('lms_exam_answers', 'lms_exam_answers_attempt_id_foreign')) {
                    $table->foreign('attempt_id')
                        ->references('id')
                        ->on('lms_exam_attempts')
                        ->onDelete('cascade'); // Delete answers when attempt is deleted
                }

                if (!$this->foreignKeyExists('lms_exam_answers', 'lms_exam_answers_question_id_foreign')) {
                    $table->foreign('question_id')
                        ->references('id')
                        ->on('lms_exam_questions')
                        ->onDelete('cascade'); // Delete answers when question is deleted
                }
            });
        }

        // Add indexes for better query performance
        Schema::table('lms_exam_attempts', function (Blueprint $table) {
            if (!$this->indexExists('lms_exam_attempts', 'lms_exam_attempts_student_exam_index')) {
                $table->index(['student_id', 'exam_id'], 'lms_exam_attempts_student_exam_index');
            }
            if (!$this->indexExists('lms_exam_attempts', 'lms_exam_attempts_status_index')) {
                $table->index('status', 'lms_exam_attempts_status_index');
            }
        });

        Schema::table('lms_exam_answers', function (Blueprint $table) {
            if (!$this->indexExists('lms_exam_answers', 'lms_exam_answers_attempt_question_index')) {
                $table->index(['attempt_id', 'question_id'], 'lms_exam_answers_attempt_question_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys
        Schema::table('lms_exam_answers', function (Blueprint $table) {
            if ($this->foreignKeyExists('lms_exam_answers', 'lms_exam_answers_attempt_id_foreign')) {
                $table->dropForeign('lms_exam_answers_attempt_id_foreign');
            }
            if ($this->foreignKeyExists('lms_exam_answers', 'lms_exam_answers_question_id_foreign')) {
                $table->dropForeign('lms_exam_answers_question_id_foreign');
            }
            if ($this->indexExists('lms_exam_answers', 'lms_exam_answers_attempt_question_index')) {
                $table->dropIndex('lms_exam_answers_attempt_question_index');
            }
        });

        Schema::table('lms_exam_attempts', function (Blueprint $table) {
            if ($this->foreignKeyExists('lms_exam_attempts', 'lms_exam_attempts_exam_id_foreign')) {
                $table->dropForeign('lms_exam_attempts_exam_id_foreign');
            }
            if ($this->foreignKeyExists('lms_exam_attempts', 'lms_exam_attempts_student_id_foreign')) {
                $table->dropForeign('lms_exam_attempts_student_id_foreign');
            }
            if ($this->indexExists('lms_exam_attempts', 'lms_exam_attempts_student_exam_index')) {
                $table->dropIndex('lms_exam_attempts_student_exam_index');
            }
            if ($this->indexExists('lms_exam_attempts', 'lms_exam_attempts_status_index')) {
                $table->dropIndex('lms_exam_attempts_status_index');
            }
        });

        Schema::table('lms_exam_questions', function (Blueprint $table) {
            if ($this->foreignKeyExists('lms_exam_questions', 'lms_exam_questions_exam_id_foreign')) {
                $table->dropForeign('lms_exam_questions_exam_id_foreign');
            }
        });
    }

    /**
     * Check if a column exists in a table
     */
    private function columnExists(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }

    /**
     * Check if a foreign key exists
     */
    private function foreignKeyExists(string $table, string $foreignKey): bool
    {
        $keys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = '{$table}'
            AND CONSTRAINT_NAME = '{$foreignKey}'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        return !empty($keys);
    }

    /**
     * Check if an index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$index}'");
        return !empty($indexes);
    }
};
