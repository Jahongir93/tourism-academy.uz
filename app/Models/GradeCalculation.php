<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeCalculation extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'semester_id',
        'joriy_score',
        'oraliq_score',
        'yakuniy_score',
        'additional_points',
        'total_weighted_score',
        'final_grade',
        'gpa_points',
        'credits',
        'calculated_at'
    ];

    protected $casts = [
        'joriy_score' => 'decimal:2',
        'oraliq_score' => 'decimal:2',
        'yakuniy_score' => 'decimal:2',
        'additional_points' => 'decimal:2',
        'total_weighted_score' => 'decimal:2',
        'gpa_points' => 'decimal:2',
        'credits' => 'integer',
        'calculated_at' => 'datetime'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function calculateFinalGrade()
    {
        $total = $this->total_weighted_score;
        
        if ($total >= 86) {
            return '5';
        } elseif ($total >= 71) {
            return '4';
        } elseif ($total >= 55) {
            return '3';
        } else {
            return '2';
        }
    }

    public function calculateGPA()
    {
        $gradeToGPA = [
            '5' => 5.0,
            '4' => 4.0,
            '3' => 3.0,
            '2' => 0.0
        ];

        return $gradeToGPA[$this->final_grade] ?? 0.0;
    }
}
