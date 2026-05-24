<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'subject_id',
        'academic_year',
        'semester_number',
        'week_number',
        'lesson_number',
        'topic_name_uz',
        'topic_name_ru',
        'topic_name_en',
        'description',
        'lesson_type',
        'hours',
        'learning_outcomes',
        'teaching_methods',
        'assessment_methods',
        'resources',
        'homework',
        'is_online',
        'sequence_number'
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'week_number' => 'integer',
        'lesson_number' => 'integer',
        'hours' => 'integer',
        'semester_number' => 'integer',
        'sequence_number' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(EducationalProgram::class, 'program_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function getTopicNameAttribute()
    {
        return $this->topic_name_uz ?? $this->topic_name_ru ?? $this->topic_name_en;
    }

    public function getLessonTypeTextAttribute()
    {
        return match($this->lesson_type) {
            'lecture' => "Ma'ruza",
            'practice' => "Amaliyot",
            'seminar' => "Seminar",
            'lab' => "Laboratoriya",
            'independent' => "Mustaqil ta'lim",
            default => $this->lesson_type
        };
    }

    public function getLessonTypeBadgeClassAttribute()
    {
        return match($this->lesson_type) {
            'lecture' => 'bg-primary',
            'practice' => 'bg-info',
            'seminar' => 'bg-warning',
            'lab' => 'bg-danger',
            'independent' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    public function getLessonTypeIconAttribute()
    {
        return match($this->lesson_type) {
            'lecture' => 'fas fa-chalkboard-teacher',
            'practice' => 'fas fa-users',
            'seminar' => 'fas fa-comments',
            'lab' => 'fas fa-flask',
            'independent' => 'fas fa-book-reader',
            default => 'fas fa-question'
        };
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester_number', $semester);
    }

    public function scopeByWeek($query, $week)
    {
        return $query->where('week_number', $week);
    }

    public function scopeByLessonType($query, $type)
    {
        return $query->where('lesson_type', $type);
    }
}