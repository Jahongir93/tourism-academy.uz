<?php

namespace App\Models\Hemis;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'teacher_id',
        'group_id',
        'academic_year',
        'semester',
        'current_control_1',
        'current_control_2',
        'current_control_avg',
        'midterm_grade',
        'final_grade',
        'total_grade',
        'total_points',
        'final_mark',
        'status',
        'retake_count',
        'retake_date',
        'notes',
        'updated_by',
        'graded_at',
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'current_control_1' => 'decimal:2',
        'current_control_2' => 'decimal:2',
        'current_control_avg' => 'decimal:2',
        'midterm_grade' => 'decimal:2',
        'final_grade' => 'decimal:2',
        'total_grade' => 'decimal:2',
        'total_points' => 'integer',
        'retake_count' => 'integer',
        'retake_date' => 'date',
        'graded_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class, 'group_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}