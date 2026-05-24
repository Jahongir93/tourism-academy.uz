<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AcademicYear;

class EducationalProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name_uz',
        'name_ru',
        'name_en',
        'level',
        'education_form',
        'duration_years',
        'total_credits',
        'faculty_id',
        'department_id',
        'qualification',
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'duration_years' => 'integer',
        'total_credits' => 'integer',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class, 'program_id');
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(ProgramOutcome::class, 'program_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(CurriculumVersion::class, 'program_id');
    }

    public function hourDistributions(): HasMany
    {
        return $this->hasMany(SubjectHourDistribution::class, 'program_id');
    }

    public function getNameAttribute()
    {
        return $this->name_uz ?? $this->name_ru ?? $this->name_en;
    }

    public function getLevelTextAttribute()
    {
        return match($this->level) {
            'bakalavriat' => "Bakalavriat",
            'magistratura' => "Magistratura",
            'doktorantura' => "Doktorantura",
            'ordinatura' => "Ordinatura",
            default => $this->level
        };
    }

    public function getEducationFormTextAttribute()
    {
        return match($this->education_form) {
            'kunduzgi' => "Kunduzgi",
            'kechki' => "Kechki",
            'sirtqi' => "Sirtqi",
            default => $this->education_form
        };
    }

    public function getCurrentCurriculum($academicYear = null)
    {
        $year = $academicYear ?? AcademicYear::getCurrentYear();
        return $this->curricula()
            ->where('academic_year', $year)
            ->orderBy('semester_number')
            ->orderBy('sequence_number')
            ->get();
    }

    public function getCreditsBySemester($academicYear = null)
    {
        return $this->getCurrentCurriculum($academicYear)
            ->groupBy('semester_number')
            ->map(function ($subjects) {
                return $subjects->sum('credits');
            });
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}