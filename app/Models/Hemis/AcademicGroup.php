<?php

namespace App\Models\Hemis;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicGroup extends Model
{
    protected $fillable = [
        'specialty_id',
        'faculty_id',
        'name',
        'course',
        'max_students',
        'current_students',
        'curator_id',
        'curator_name',
        'monitor_name',
        'monitor_phone',
        'academic_year',
        'semester',
        'language',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'course' => 'integer',
        'max_students' => 'integer',
        'current_students' => 'integer',
        'academic_year' => 'integer',
    ];

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'group_id');
    }
}