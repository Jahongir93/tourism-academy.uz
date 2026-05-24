<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherWorkload extends Model
{
    use HasFactory;

    protected $table = 'teacher_workload';

    protected $fillable = [
        'teacher_id',
        'academic_year_id',
        'teaching_hours',
        'research_hours',
        'methodical_hours',
        'educational_hours',
        'total_hours',
        'planned_hours',
        'completed_hours',
        'status'
    ];

    protected $casts = [
        'teaching_hours' => 'integer',
        'research_hours' => 'integer',
        'methodical_hours' => 'integer',
        'educational_hours' => 'integer',
        'total_hours' => 'integer',
        'planned_hours' => 'integer',
        'completed_hours' => 'integer',
        'academic_year_id' => 'integer'
    ];

    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}