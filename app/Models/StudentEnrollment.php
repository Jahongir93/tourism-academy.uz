<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'faculty_id',
        'specialty_id',
        'group_id',
        'academic_year_id',
        'education_form',
        'education_type',
        'education_language',
        'enrollment_date',
        'expected_graduation_date',
        'current_course',
        'current_semester',
        'is_active'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'expected_graduation_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class, 'group_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getEducationFormLabelAttribute(): string
    {
        return match($this->education_form) {
            'kunduzgi' => 'Kunduzgi',
            'kechki' => 'Kechki',
            'sirtqi' => 'Sirtqi',
            default => $this->education_form
        };
    }

    public function getEducationTypeLabelAttribute(): string
    {
        return match($this->education_type) {
            'grant' => 'Grant',
            'contract' => 'Kontrakt',
            'super_contract' => 'Super kontrakt',
            'foreign_contract' => 'Xorijiy kontrakt',
            default => $this->education_type
        };
    }
}