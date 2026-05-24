<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'faculty_id',
        'specialty_id',
        'course',
        'language',
        'academic_year',
        'semester',
        'max_students',
        'current_students',
        'curator_id',
        'curator_name',
        'monitor_name',
        'monitor_phone',
        'is_active'
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'group_id');
    }

    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    public function getCurrentStudentCountAttribute(): int
    {
        return $this->students()->where('is_active', true)->count();
    }

    public function getAvailableSlotsAttribute(): int
    {
        return $this->max_students - $this->current_student_count;
    }
}