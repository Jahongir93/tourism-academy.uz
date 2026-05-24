<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'order_number',
        'question_type',
        'question_text',
        'question_hint',
        'media',
        'options',
        'correct_answer',
        'explanation',
        'points',
        'partial_credit',
        'negative_marking',
        'difficulty',
        'category',
        'is_required',
        'is_active'
    ];

    protected $casts = [
        'media' => 'array',
        'options' => 'array',
        'correct_answer' => 'array',
        'points' => 'decimal:2',
        'negative_marking' => 'decimal:2',
        'partial_credit' => 'boolean',
        'is_required' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function exam(): BelongsTo
    {
        return $this->belongsTo(LmsExam::class, 'exam_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(LmsExamAnswer::class, 'question_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    // Helper methods
    public function getQuestionTypeLabel(): string
    {
        return match($this->question_type) {
            'single_choice' => 'Bir to\'g\'ri javob',
            'multiple_choice' => 'Ko\'p javobli',
            'true_false' => 'Ha/Yo\'q',
            'text' => 'Qisqa javob',
            'essay' => 'Insho',
            'matching' => 'Moslashtirish',
            'fill_blank' => 'Bo\'sh joyni to\'ldirish',
            default => $this->question_type
        };
    }

    public function getDifficultyLabel(): string
    {
        return match($this->difficulty) {
            'easy' => 'Oson',
            'medium' => 'O\'rtacha',
            'hard' => 'Qiyin',
            default => $this->difficulty
        };
    }

    public function getDifficultyColor(): string
    {
        return match($this->difficulty) {
            'easy' => 'green',
            'medium' => 'yellow',
            'hard' => 'red',
            default => 'gray'
        };
    }

    public function checkAnswer($givenAnswer): array
    {
        $result = [
            'is_correct' => false,
            'points_earned' => 0,
            'feedback' => ''
        ];

        if ($givenAnswer === null || $givenAnswer === '' || (is_array($givenAnswer) && empty($givenAnswer))) {
            $result['feedback'] = 'Javob berilmagan';
            return $result;
        }

        switch ($this->question_type) {
            case 'single_choice':
            case 'true_false':
                $correct = is_array($this->correct_answer) ? ($this->correct_answer[0] ?? null) : $this->correct_answer;
                $given = is_array($givenAnswer) ? ($givenAnswer[0] ?? null) : $givenAnswer;

                if ($correct == $given) {
                    $result['is_correct'] = true;
                    $result['points_earned'] = $this->points;
                } else {
                    $result['points_earned'] = -$this->negative_marking;
                }
                break;

            case 'multiple_choice':
                $correctAnswers = is_array($this->correct_answer) ? $this->correct_answer : [$this->correct_answer];
                $givenAnswers = is_array($givenAnswer) ? $givenAnswer : [$givenAnswer];

                sort($correctAnswers);
                sort($givenAnswers);

                if ($correctAnswers == $givenAnswers) {
                    $result['is_correct'] = true;
                    $result['points_earned'] = $this->points;
                } elseif ($this->partial_credit) {
                    // Partial credit: points for correct answers minus wrong answers
                    $correctCount = count(array_intersect($givenAnswers, $correctAnswers));
                    $wrongCount = count(array_diff($givenAnswers, $correctAnswers));
                    $totalCorrect = count($correctAnswers);

                    $points = ($correctCount / $totalCorrect) * $this->points;
                    $points -= $wrongCount * $this->negative_marking;
                    $result['points_earned'] = max(0, $points);
                } else {
                    $result['points_earned'] = -$this->negative_marking;
                }
                break;

            case 'text':
            case 'fill_blank':
                $correctAnswers = is_array($this->correct_answer) ? $this->correct_answer : [$this->correct_answer];
                $givenLower = strtolower(trim($givenAnswer));

                foreach ($correctAnswers as $correct) {
                    if (strtolower(trim($correct)) == $givenLower) {
                        $result['is_correct'] = true;
                        $result['points_earned'] = $this->points;
                        break;
                    }
                }
                break;

            case 'essay':
                // Essays need manual grading
                $result['feedback'] = 'Bu savol o\'qituvchi tomonidan baholanadi';
                $result['needs_manual_grading'] = true;
                break;

            case 'matching':
                $correctPairs = $this->correct_answer;
                $givenPairs = $givenAnswer;

                if (is_array($correctPairs) && is_array($givenPairs)) {
                    $correctCount = 0;
                    $totalPairs = count($correctPairs);

                    foreach ($correctPairs as $key => $value) {
                        if (isset($givenPairs[$key]) && $givenPairs[$key] == $value) {
                            $correctCount++;
                        }
                    }

                    if ($correctCount == $totalPairs) {
                        $result['is_correct'] = true;
                        $result['points_earned'] = $this->points;
                    } elseif ($this->partial_credit && $correctCount > 0) {
                        $result['points_earned'] = ($correctCount / $totalPairs) * $this->points;
                    }
                }
                break;
        }

        return $result;
    }

    public function getShuffledOptions(): ?array
    {
        if (!$this->options || !is_array($this->options)) {
            return null;
        }

        $options = $this->options;
        shuffle($options);
        return $options;
    }
}
