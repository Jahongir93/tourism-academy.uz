<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupSubjectGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_subject_id',
        'student_id',
        'current_grade',
        'midterm_grade',
        'final_grade',
        'additional_grades',
        'total_score',
        'letter_grade',
        'is_passed',
        'notes'
    ];

    protected $casts = [
        'current_grade' => 'decimal:2',
        'midterm_grade' => 'decimal:2',
        'final_grade' => 'decimal:2',
        'total_score' => 'decimal:2',
        'additional_grades' => 'array',
        'is_passed' => 'boolean'
    ];

    public function groupSubject(): BelongsTo
    {
        return $this->belongsTo(GroupSubject::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Calculate total score based on weights
     */
    public function calculateTotalScore(): void
    {
        // Default weights: Joriy 30%, Oraliq 30%, Yakuniy 40%
        $currentWeight = 0.30;
        $midtermWeight = 0.30;
        $finalWeight = 0.40;

        $total = 0;
        $total += ($this->current_grade ?? 0) * $currentWeight;
        $total += ($this->midterm_grade ?? 0) * $midtermWeight;
        $total += ($this->final_grade ?? 0) * $finalWeight;

        $this->total_score = round($total, 2);
        $this->letter_grade = $this->calculateLetterGrade($this->total_score);
        $this->is_passed = $this->total_score >= 56; // 56+ o'tdi
    }

    /**
     * Calculate letter grade from score
     */
    protected function calculateLetterGrade(float $score): string
    {
        if ($score >= 86) return 'A';
        if ($score >= 71) return 'B';
        if ($score >= 56) return 'C';
        if ($score >= 41) return 'D';
        return 'F';
    }
}
