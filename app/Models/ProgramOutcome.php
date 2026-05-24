<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProgramOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'code',
        'description_uz',
        'description_ru',
        'description_en',
        'category'
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(EducationalProgram::class, 'program_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_outcome_mappings', 'outcome_id', 'subject_id')
            ->withPivot('contribution_level')
            ->withTimestamps();
    }

    public function getDescriptionAttribute()
    {
        return $this->description_uz ?? $this->description_ru ?? $this->description_en;
    }

    public function getCategoryTextAttribute()
    {
        return match($this->category) {
            'knowledge' => "Bilim",
            'skills' => "Ko'nikma",
            'competencies' => "Kompetensiya",
            default => $this->category
        };
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}