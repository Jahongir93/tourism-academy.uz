<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer',
        'text_answer',
        'is_correct',
        'points_earned',
        'feedback',
        'graded_by',
        'graded_at',
        'time_spent_seconds',
        'answered_at',
        'is_flagged'
    ];

    protected $casts = [
        'answer' => 'array',
        'graded_at' => 'datetime',
        'answered_at' => 'datetime',
        'is_correct' => 'boolean',
        'is_flagged' => 'boolean',
        'points_earned' => 'decimal:2'
    ];

    // Relationships
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(LmsExamAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(LmsExamQuestion::class, 'question_id');
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Helper methods
    public function grade(): void
    {
        $result = $this->question->checkAnswer($this->answer ?? $this->text_answer);

        $this->is_correct = $result['is_correct'];
        $this->points_earned = $result['points_earned'];

        if (isset($result['feedback'])) {
            $this->feedback = $result['feedback'];
        }

        $this->save();
    }

    public function manualGrade(float $points, ?string $feedback = null, int $gradedBy = null): void
    {
        $this->points_earned = min($points, $this->question->points);
        $this->is_correct = $points >= ($this->question->points * 0.5);
        $this->feedback = $feedback;
        $this->graded_by = $gradedBy;
        $this->graded_at = now();
        $this->save();

        // Recalculate attempt results
        $this->attempt->calculateResults();
    }

    public function getFormattedAnswer(): string
    {
        if ($this->text_answer) {
            return $this->text_answer;
        }

        if (!$this->answer) {
            return 'Javob berilmagan';
        }

        $options = $this->question->options;
        $answer = $this->answer;

        if (!is_array($answer)) {
            $answer = [$answer];
        }

        // For choice questions, show the option text
        if (in_array($this->question->question_type, ['single_choice', 'multiple_choice'])) {
            $texts = [];
            foreach ($answer as $key) {
                if (isset($options[$key])) {
                    $texts[] = $options[$key];
                } else {
                    $texts[] = $key;
                }
            }
            return implode(', ', $texts);
        }

        return is_array($answer) ? implode(', ', $answer) : (string)$answer;
    }

    public function getCorrectAnswerFormatted(): string
    {
        $correctAnswer = $this->question->correct_answer;
        $options = $this->question->options;

        if (!is_array($correctAnswer)) {
            $correctAnswer = [$correctAnswer];
        }

        // For choice questions, show the option text
        if (in_array($this->question->question_type, ['single_choice', 'multiple_choice'])) {
            $texts = [];
            foreach ($correctAnswer as $key) {
                if (isset($options[$key])) {
                    $texts[] = $options[$key];
                } else {
                    $texts[] = $key;
                }
            }
            return implode(', ', $texts);
        }

        return implode(', ', $correctAnswer);
    }
}
