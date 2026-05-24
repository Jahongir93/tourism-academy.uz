<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'group_id',
        'academic_year_id',
        'role',
        'assigned_date',
        'status'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'academic_year_id' => 'integer'
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
