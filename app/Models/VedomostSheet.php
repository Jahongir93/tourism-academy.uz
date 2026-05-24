<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VedomostSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'subject_id',
        'teacher_id',
        'academic_year_id',
        'semester',
        'credits',
        'assessment_type',
        'assessment_date',
        'status',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'semester' => 'integer',
        'credits' => 'integer',
        'assessment_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'status' => 'draft',
        'credits' => 3,
        'assessment_type' => 'exam',
        'is_active' => true,
    ];

    // Relationships
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'vedomost_sheet_id');
    }

    public function assessmentColumns(): HasMany
    {
        return $this->hasMany(VedomostAssessmentColumn::class, 'vedomost_sheet_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    // Helper methods
    public function getStudents()
    {
        return Student::where('group_id', $this->group_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    public function getGradesForStudent($studentId)
    {
        return $this->grades()
            ->where('student_id', $studentId)
            ->first();
    }

    public function isComplete()
    {
        $totalStudents = Student::where('group_id', $this->group_id)->count();
        $gradedStudents = $this->grades()->distinct('student_id')->count();

        return $totalStudents > 0 && $totalStudents === $gradedStudents;
    }

    public function getCompletionPercentage()
    {
        $totalStudents = Student::where('group_id', $this->group_id)->count();
        if ($totalStudents == 0) return 0;

        $gradedStudents = $this->grades()->distinct('student_id')->count();
        return round(($gradedStudents / $totalStudents) * 100);
    }
}
