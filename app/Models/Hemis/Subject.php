<?php

namespace App\Models\Hemis;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name_uz',
        'name_ru',
        'name_en',
        'department_id',
        'type',
        'category',
        'total_hours',
        'lecture_hours',
        'practice_hours',
        'lab_hours',
        'seminar_hours',
        'independent_hours',
        'credits',
        'description',
        'objectives',
        'outcomes',
        'prerequisites',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_hours' => 'integer',
        'lecture_hours' => 'integer',
        'practice_hours' => 'integer',
        'lab_hours' => 'integer',
        'seminar_hours' => 'integer',
        'independent_hours' => 'integer',
        'credits' => 'integer',
        'prerequisites' => 'array',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}