<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'journal_id',
        'vedomost_sheet_id',
        'assessment_column_id',
        'academic_year',
        'semester',
        'course',
        'grade',
        'grade_point',
        'letter_grade',
        'credits',
        'assessment_type',
        'assessment_date',
        'teacher_id',
        'comments',
        'is_retake',
        'attempt_number',
        'is_final',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'assessment_date' => 'date',
        'is_retake' => 'boolean',
        'is_final' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function vedomostSheet(): BelongsTo
    {
        return $this->belongsTo(VedomostSheet::class, 'vedomost_sheet_id');
    }

    public function assessmentColumn(): BelongsTo
    {
        return $this->belongsTo(VedomostAssessmentColumn::class, 'assessment_column_id');
    }

    /**
     * Calculate grade point from numerical grade
     */
    public static function calculateGradePoint(float $grade): float
    {
        if ($grade >= 86) return 4.0;
        if ($grade >= 71) return 3.0;
        if ($grade >= 55) return 2.0;
        return 0.0;
    }

    /**
     * Get letter grade from numerical grade
     */
    public static function getLetterGrade(float $grade): string
    {
        if ($grade >= 90) return 'A';
        if ($grade >= 86) return 'A-';
        if ($grade >= 80) return 'B+';
        if ($grade >= 75) return 'B';
        if ($grade >= 71) return 'B-';
        if ($grade >= 65) return 'C+';
        if ($grade >= 60) return 'C';
        if ($grade >= 55) return 'C-';
        if ($grade >= 50) return 'D';
        return 'F';
    }

    /**
     * Check if grade is passing
     */
    public function isPassing(): bool
    {
        return $this->grade >= 55;
    }

    /**
     * Scope for specific academic year
     */
    public function scopeAcademicYear($query, string $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope for specific semester
     */
    public function scopeSemester($query, int $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope for final grades only
     */
    public function scopeFinal($query)
    {
        return $query->where('is_final', true);
    }

    /**
     * Scope for non-retake grades
     */
    public function scopeFirstAttempt($query)
    {
        return $query->where('is_retake', false);
    }
}
