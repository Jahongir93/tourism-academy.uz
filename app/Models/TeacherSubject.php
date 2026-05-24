<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'academic_year_id',
        'semester_id',
        'group_ids',
        'lecture_hours',
        'practice_hours',
        'lab_hours',
        'total_hours',
        'language',
        'status'
    ];

    protected $casts = [
        'group_ids' => 'array',
        'lecture_hours' => 'integer',
        'practice_hours' => 'integer',
        'lab_hours' => 'integer',
        'total_hours' => 'integer'
    ];

    // Auto-calculate total hours
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->total_hours = $model->lecture_hours + $model->practice_hours + $model->lab_hours;
        });
    }

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    // Get groups
    public function getGroupsAttribute()
    {
        if (!$this->group_ids) {
            return collect();
        }
        
        return Group::whereIn('id', $this->group_ids)->get();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCurrentYear($query)
    {
        $currentYear = date('Y');
        return $query->where('academic_year_id', $currentYear);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester_id', $semester);
    }
}