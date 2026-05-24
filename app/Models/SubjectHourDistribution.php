<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectHourDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'program_id',
        'lecture_hours',
        'practice_hours',
        'seminar_hours',
        'lab_hours',
        'independent_hours',
        'course_work_hours'
    ];

    protected $casts = [
        'lecture_hours' => 'integer',
        'practice_hours' => 'integer',
        'seminar_hours' => 'integer',
        'lab_hours' => 'integer',
        'independent_hours' => 'integer',
        'course_work_hours' => 'integer',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(EducationalProgram::class, 'program_id');
    }

    public function getTotalAuditoryHoursAttribute()
    {
        return $this->lecture_hours + 
               $this->practice_hours + 
               $this->seminar_hours + 
               $this->lab_hours;
    }

    public function getTotalHoursAttribute()
    {
        return $this->total_auditory_hours + 
               $this->independent_hours + 
               ($this->course_work_hours ?? 0);
    }

    public function getAuditoryPercentAttribute()
    {
        $total = $this->total_hours;
        if ($total == 0) return 0;
        
        return round(($this->total_auditory_hours / $total) * 100, 1);
    }

    public function getIndependentPercentAttribute()
    {
        $total = $this->total_hours;
        if ($total == 0) return 0;
        
        return round(($this->independent_hours / $total) * 100, 1);
    }

    public function isValid()
    {
        // Check if auditory hours don't exceed 50%
        return $this->auditory_percent <= 50;
    }
}