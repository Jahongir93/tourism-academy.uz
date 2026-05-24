<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_uz',
        'name_ru',
        'name_en',
        'subject_type',
        'department_id',
        'credits',
        'total_hours',
        'prerequisites',
        'description',
        'objectives',
        'outcomes',
        'is_active',
        'category'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credits' => 'integer',
        'total_hours' => 'integer',
        'prerequisites' => 'array',
    ];

    protected $attributes = [
        'category' => 'general',
        'is_active' => true,
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    public function hourDistributions(): HasMany
    {
        return $this->hasMany(SubjectHourDistribution::class);
    }

    public function groupSubjects(): HasMany
    {
        return $this->hasMany(GroupSubject::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(SubjectTopic::class);
    }

    public function prerequisiteSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_prerequisites', 'subject_id', 'prerequisite_id')
            ->withPivot('type')
            ->withTimestamps();
    }
    
    // Alias for prerequisiteSubjects
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_prerequisites', 'subject_id', 'prerequisite_id')
            ->withPivot('type')
            ->withTimestamps();
    }

    public function dependentSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_prerequisites', 'prerequisite_id', 'subject_id')
            ->withPivot('type')
            ->withTimestamps();
    }

    public function programOutcomes(): BelongsToMany
    {
        return $this->belongsToMany(ProgramOutcome::class, 'subject_outcome_mappings', 'subject_id', 'outcome_id')
            ->withPivot('contribution_level')
            ->withTimestamps();
    }

    public function getNameAttribute()
    {
        return $this->name_uz ?? $this->name_ru ?? $this->name_en;
    }

    public function getSubjectTypeTextAttribute()
    {
        return match($this->subject_type) {
            'majburiy' => "Majburiy",
            'tanlov' => "Tanlov",
            'umumkasbiy' => "Umumkasbiy",
            'mutaxassislik' => "Mutaxassislik",
            default => $this->subject_type
        };
    }

    public function getDefaultHourDistribution()
    {
        return [
            'lecture' => round($this->total_hours * 0.3),
            'practice' => round($this->total_hours * 0.2),
            'seminar' => round($this->total_hours * 0.1),
            'lab' => 0,
            'independent' => round($this->total_hours * 0.4),
        ];
    }

    public function hasPrerequisites()
    {
        return $this->prerequisiteSubjects()->exists() || 
               (!empty($this->prerequisites) && count($this->prerequisites) > 0);
    }

    public function getPrograms()
    {
        return EducationalProgram::whereHas('curricula', function($q) {
            $q->where('subject_id', $this->id);
        })->distinct()->get();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('subject_type', $type);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}