<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'department_id',
        'code',
        'name_uz',
        'name_ru',
        'name_en',
        'direction_code',
        'degree',
        'education_form',
        'education_type',
        'duration_years',
        'credits_required',
        'tuition_fee'
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function specialtySubjects(): HasMany
    {
        return $this->hasMany(SpecialtySubject::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'specialty_subjects')
            ->withPivot('semester', 'course_year', 'is_required', 'credits', 'hours_total')
            ->withTimestamps();
    }

    // Get subjects for specific semester
    public function getSubjectsForSemester(int $semester)
    {
        return $this->subjects()->wherePivot('semester', $semester)->get();
    }
}