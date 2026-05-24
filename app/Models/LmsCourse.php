<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class LmsCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'objectives',
        'requirements',
        'subject_id',
        'teacher_id',
        'course_code',
        'language',
        'level',
        'duration_weeks',
        'hours_per_week',
        'credit_hours',
        'thumbnail',
        'intro_video',
        'price',
        'is_published',
        'is_featured',
        'is_archived',
        'archived_at',
        'auto_enrollment',
        'max_students',
        'start_date',
        'end_date',
        'enrollment_start',
        'enrollment_end',
        'passing_score',
        'certificate_available',
        'tags',
        'view_count',
        'enrollment_count',
        'rating',
        'rating_count'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'is_archived' => 'boolean',
        'auto_enrollment' => 'boolean',
        'certificate_available' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'enrollment_start' => 'datetime',
        'enrollment_end' => 'datetime',
        'archived_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
            if (empty($course->course_code)) {
                $course->course_code = static::generateCourseCode();
            }
        });
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(LmsCourseResource::class, 'course_id');
    }

    public function topics(): HasMany
    {
        return $this->hasMany(LmsCourseTopic::class, 'course_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(LmsCourseEnrollment::class, 'course_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lms_course_enrollments', 'course_id', 'user_id')
            ->withPivot(['enrolled_at', 'progress_percentage', 'status'])
            ->withTimestamps();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LmsMaterial::class, 'subject_id', 'subject_id')
            ->where('teacher_id', $this->teacher_id);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(LmsVideo::class, 'subject_id', 'subject_id')
            ->where('teacher_id', $this->teacher_id);
    }

    public function tests(): HasMany
    {
        return $this->hasMany(LmsPracticeTest::class, 'subject_id', 'subject_id')
            ->where('teacher_id', $this->teacher_id);
    }

    public static function generateCourseCode(): string
    {
        do {
            $code = 'CRS-' . strtoupper(Str::random(6));
        } while (static::where('course_code', $code)->exists());
        
        return $code;
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function isEnrollmentOpen(): bool
    {
        $now = now();
        
        if ($this->enrollment_start && $now < $this->enrollment_start) {
            return false;
        }
        
        if ($this->enrollment_end && $now > $this->enrollment_end) {
            return false;
        }
        
        if ($this->max_students && $this->enrollment_count >= $this->max_students) {
            return false;
        }
        
        return $this->is_published;
    }

    public function isActive(): bool
    {
        $now = now();
        
        if ($this->start_date && $now < $this->start_date) {
            return false;
        }
        
        if ($this->end_date && $now > $this->end_date) {
            return false;
        }
        
        return $this->is_published;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }
}