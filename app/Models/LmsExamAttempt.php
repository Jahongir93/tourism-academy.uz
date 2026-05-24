<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Models\VedomostSheet;
use App\Models\VedomostAssessmentColumn;
use App\Models\Grade;
use App\Models\AcademicYear;

class LmsExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'attempt_number',
        'started_at',
        'finished_at',
        'time_spent_seconds',
        'score',
        'percentage',
        'correct_answers',
        'wrong_answers',
        'unanswered',
        'passed',
        'status',
        'question_order',
        'ip_address',
        'user_agent',
        'tab_switches',
        'activity_log',
        'synced_to_journal',
        'synced_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'synced_at' => 'datetime',
        'question_order' => 'array',
        'activity_log' => 'array',
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
        'synced_to_journal' => 'boolean'
    ];

    // Relationships
    public function exam(): BelongsTo
    {
        return $this->belongsTo(LmsExam::class, 'exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(LmsExamAnswer::class, 'attempt_id');
    }

    // Scopes
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['submitted', 'graded']);
    }

    // Helper methods
    public function isExpired(): bool
    {
        if (!$this->exam->strict_time) {
            return false;
        }

        $endTime = $this->started_at->addMinutes($this->exam->duration_minutes);
        return now() > $endTime;
    }

    public function getRemainingTime(): int
    {
        if ($this->status !== 'in_progress') {
            return 0;
        }

        $endTime = $this->started_at->addMinutes($this->exam->duration_minutes);
        $remaining = $endTime->diffInSeconds(now(), false);

        return max(0, -$remaining);
    }

    public function getFormattedRemainingTime(): string
    {
        $seconds = $this->getRemainingTime();
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%02d:%02d', $minutes, $secs);
    }

    public function getFormattedTimeSpent(): string
    {
        $seconds = $this->time_spent_seconds ?? 0;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d soat %d daqiqa', $hours, $minutes);
        }

        return sprintf('%d daqiqa %d soniya', $minutes, $secs);
    }

    public function calculateResults(): void
    {
        $answers = $this->answers()->with('question')->get();

        $totalQuestions = $this->exam->questions()->count();
        $correctCount = 0;
        $wrongCount = 0;
        $totalPoints = 0;

        foreach ($answers as $answer) {
            if ($answer->is_correct) {
                $correctCount++;
            } elseif ($answer->is_correct === false) {
                $wrongCount++;
            }
            $totalPoints += $answer->points_earned ?? 0;
        }

        $unanswered = $totalQuestions - $answers->count();

        $this->correct_answers = $correctCount;
        $this->wrong_answers = $wrongCount;
        $this->unanswered = $unanswered;
        $this->score = max(0, $totalPoints);
        $this->percentage = $this->exam->max_score > 0
            ? round(($totalPoints / $this->exam->max_score) * 100, 2)
            : 0;
        $this->passed = $this->score >= $this->exam->passing_score;
        $this->time_spent_seconds = $this->started_at->diffInSeconds($this->finished_at ?? now());

        $this->save();
    }

    public function submit(): void
    {
        $this->finished_at = now();
        $this->status = 'submitted';

        // Grade all answers
        foreach ($this->answers as $answer) {
            if ($answer->is_correct === null && $answer->question->question_type !== 'essay') {
                $result = $answer->question->checkAnswer($answer->answer ?? $answer->text_answer);
                $answer->is_correct = $result['is_correct'];
                $answer->points_earned = $result['points_earned'];
                $answer->save();
            }
        }

        $this->calculateResults();

        // Check if all questions are graded
        $needsManualGrading = $this->answers()
            ->whereNull('is_correct')
            ->whereHas('question', function ($q) {
                $q->where('question_type', 'essay');
            })
            ->exists();

        if (!$needsManualGrading) {
            $this->status = 'graded';
            $this->save();
        }

        // Sync to journal if enabled
        if ($this->exam->sync_to_journal && $this->status === 'graded') {
            $this->syncToJournal();
            $this->syncToVedomost(); // Also sync to vedomost
        }
    }

    public function syncToVedomost(): bool
    {
        try {
            $exam = $this->exam;
            $student = $this->student;

            if (!$exam->subject_id || !$student->group_id) {
                return false;
            }

            // Find current academic year
            $currentYear = AcademicYear::where('is_current', true)->first();
            if (!$currentYear) {
                return false;
            }

            // Determine semester (assuming current semester is stored somewhere)
            $currentSemester = $currentYear->current_semester ?? 1;

            // Find or create vedomost sheet
            $vedomost = VedomostSheet::firstOrCreate(
                [
                    'subject_id' => $exam->subject_id,
                    'group_id' => $student->group_id,
                    'academic_year_id' => $currentYear->id,
                    'semester' => $currentSemester,
                ],
                [
                    'teacher_id' => $exam->teacher_id,
                    'assessment_type' => $exam->exam_type === 'yakuniy' ? 'exam' : 'test',
                    'credits' => 3, // Default credits
                    'status' => 'draft',
                ]
            );

            // Convert percentage to 0-100 scale
            $gradeValue = round($this->percentage, 0);
            $gradePoint = Grade::calculateGradePoint($gradeValue);
            $letterGrade = Grade::getLetterGrade($gradeValue);

            // Determine which column to update based on exam type
            $isFinal = $exam->exam_type === 'yakuniy';
            $assessmentColumnId = null;

            // If exam type is 'oraliq', create or find a dynamic column
            if ($exam->exam_type === 'oraliq') {
                $columnName = $exam->title;
                $column = VedomostAssessmentColumn::firstOrCreate(
                    [
                        'vedomost_sheet_id' => $vedomost->id,
                        'name' => $columnName,
                    ],
                    [
                        'column_type' => 'numeric',
                        'max_score' => 100,
                        'order' => VedomostAssessmentColumn::where('vedomost_sheet_id', $vedomost->id)->max('order') + 1 ?? 1,
                        'is_final' => false,
                        'is_active' => true,
                    ]
                );
                $assessmentColumnId = $column->id;
            }

            // Create or update grade
            Grade::updateOrCreate(
                [
                    'vedomost_sheet_id' => $vedomost->id,
                    'student_id' => $student->id,
                    'is_final' => $isFinal,
                    'assessment_column_id' => $assessmentColumnId,
                ],
                [
                    'subject_id' => $exam->subject_id,
                    'academic_year' => $currentYear->name,
                    'semester' => $currentSemester,
                    'grade' => $gradeValue,
                    'grade_point' => $gradePoint,
                    'letter_grade' => $letterGrade,
                    'credits' => $vedomost->credits,
                    'assessment_type' => $exam->exam_type,
                    'assessment_date' => now(),
                    'teacher_id' => $exam->teacher_id,
                    'is_retake' => $this->attempt_number > 1,
                    'course' => $student->group->course ?? 1,
                    'attempt_number' => $this->attempt_number,
                    'comments' => "LMS imtihon: {$exam->title}",
                ]
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to sync exam result to vedomost: ' . $e->getMessage());
            return false;
        }
    }

    public function syncToJournal(): bool
    {
        if ($this->synced_to_journal) {
            return true;
        }

        try {
            $exam = $this->exam;
            $student = $this->student;

            // Find journal entry for this subject and group
            $journalEntry = JournalEntry::where('subject_id', $exam->subject_id)
                ->where('group_id', $student->group_id)
                ->first();

            if (!$journalEntry) {
                return false;
            }

            // Calculate weighted score
            $weightedScore = ($this->score / $exam->max_score) * $exam->weight_percentage;

            // Determine grade type
            $gradeType = match($exam->exam_type) {
                'joriy' => 'joriy',
                'oraliq' => 'oraliq',
                'yakuniy' => 'yakuniy',
                default => null
            };

            if (!$gradeType) {
                return false;
            }

            // Create or update journal grade
            $grade = JournalGrade::updateOrCreate(
                [
                    'journal_entry_id' => $journalEntry->id,
                    'student_id' => $student->id,
                    'grade_type' => $gradeType,
                ],
                [
                    'score' => $weightedScore,
                    'max_score' => $exam->weight_percentage,
                    'weight_percentage' => $exam->weight_percentage,
                    'graded_date' => now()->toDateString(),
                    'graded_by' => $exam->teacher_id,
                    'notes' => "LMS Imtihon: {$exam->title} (Ball: {$this->score}/{$exam->max_score})"
                ]
            );

            $this->synced_to_journal = true;
            $this->synced_at = now();
            $this->save();

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to sync exam result to journal: ' . $e->getMessage());
            return false;
        }
    }

    public function logActivity(string $action, array $data = []): void
    {
        $log = $this->activity_log ?? [];
        $log[] = [
            'action' => $action,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
            'ip' => request()->ip()
        ];
        $this->activity_log = $log;
        $this->save();
    }

    public function incrementTabSwitch(): void
    {
        $this->increment('tab_switches');
        $this->logActivity('tab_switch');
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'in_progress' => 'Davom etmoqda',
            'submitted' => 'Topshirildi',
            'graded' => 'Baholangan',
            'expired' => 'Vaqti tugagan',
            'cancelled' => 'Bekor qilingan',
            default => $this->status
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'in_progress' => 'yellow',
            'submitted' => 'blue',
            'graded' => 'green',
            'expired' => 'red',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function getGrade(): string
    {
        if ($this->percentage === null) {
            return '-';
        }

        if ($this->percentage >= 86) return '5';
        if ($this->percentage >= 71) return '4';
        if ($this->percentage >= 56) return '3';
        return '2';
    }
}
