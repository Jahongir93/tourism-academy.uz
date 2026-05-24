<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'curricula';

    protected $fillable = [
        'program_id',
        'academic_year',
        'semester_number',
        'subject_id',
        'lecture_hours',
        'practice_hours',
        'seminar_hours',
        'lab_hours',
        'independent_hours',
        'credits',
        'subject_type',
        'sequence_number',
        'is_approved',
        // Old columns for compatibility
        'specialty_id',
        'semester',
        'year',
        'hours_per_week'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'lecture_hours' => 'integer',
        'practice_hours' => 'integer',
        'seminar_hours' => 'integer',
        'lab_hours' => 'integer',
        'independent_hours' => 'integer',
        'credits' => 'integer',
        'semester_number' => 'integer',
        'sequence_number' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(EducationalProgram::class, 'program_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
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
        return $this->total_auditory_hours + $this->independent_hours;
    }

    public function getHourDistributionAttribute()
    {
        $total = $this->total_hours;
        if ($total == 0) return null;

        return [
            'lecture' => round(($this->lecture_hours / $total) * 100, 1),
            'practice' => round(($this->practice_hours / $total) * 100, 1),
            'seminar' => round(($this->seminar_hours / $total) * 100, 1),
            'lab' => round(($this->lab_hours / $total) * 100, 1),
            'independent' => round(($this->independent_hours / $total) * 100, 1),
        ];
    }

    public function validateHours()
    {
        // 1 kredit = 30 soat
        $expectedHours = $this->credits * 30;
        $actualHours = $this->total_hours;
        
        // Auditoriya soatlari 50% dan oshmasligi kerak
        $auditoryPercent = ($this->total_auditory_hours / $actualHours) * 100;
        
        return [
            'valid' => $expectedHours == $actualHours && $auditoryPercent <= 50,
            'expected_hours' => $expectedHours,
            'actual_hours' => $actualHours,
            'auditory_percent' => round($auditoryPercent, 1),
            'errors' => []
        ];
    }

    public function getCourseAttribute()
    {
        return ceil($this->semester_number / 2);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester_number', $semester);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}