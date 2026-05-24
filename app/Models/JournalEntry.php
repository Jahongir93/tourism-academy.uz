<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $fillable = [
        'subject_id',
        'group_id',
        'teacher_id',
        'academic_year_id',
        'semester_id'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function teacher(): BelongsTo
    {
        // teacher_id references teachers table (foreign key constraint)
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Get teacher name
     */
    public function getTeacherNameAttribute(): string
    {
        if ($this->teacher) {
            return $this->teacher->full_name ?? $this->teacher->first_name . ' ' . $this->teacher->last_name;
        }
        return 'Noma\'lum';
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(JournalGrade::class);
    }
}
