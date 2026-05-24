<?php

namespace App\Models\Hemis;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
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
        'tuition_fee',
        'language',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_years' => 'integer',
        'credits_required' => 'integer',
        'tuition_fee' => 'decimal:2',
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
        return $this->hasMany(AcademicGroup::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}