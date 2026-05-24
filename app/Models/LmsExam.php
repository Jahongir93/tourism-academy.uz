<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LmsExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'instructions',
        'subject_id',
        'teacher_id',
        'course_id',
        'group_ids',
        'exam_type',
        'week_number',
        'duration_minutes',
        'start_time',
        'end_time',
        'strict_time',
        'max_score',
        'passing_score',
        'weight_percentage',
        'max_attempts',
        'allow_retake',
        'retake_delay_hours',
        'questions_count',
        'shuffle_questions',
        'shuffle_answers',
        'show_correct_answers',
        'show_score_immediately',
        'browser_lockdown',
        'prevent_copy_paste',
        'require_webcam',
        'access_password_hash', // SECURITY FIX: Changed from access_password to hashed version
        'allowed_ip_addresses',
        'sync_to_journal',
        'auto_publish_results',
        'status',
        'is_published'
    ];

    // SECURITY: Protect sensitive fields from direct mass assignment
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    // SECURITY: Hide password hash from JSON/Array serialization
    protected $hidden = [
        'access_password_hash',
    ];

    protected $casts = [
        'group_ids' => 'array',
        'allowed_ip_addresses' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'strict_time' => 'boolean',
        'max_score' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'weight_percentage' => 'decimal:2',
        'allow_retake' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'show_correct_answers' => 'boolean',
        'show_score_immediately' => 'boolean',
        'browser_lockdown' => 'boolean',
        'prevent_copy_paste' => 'boolean',
        'require_webcam' => 'boolean',
        'sync_to_journal' => 'boolean',
        'auto_publish_results' => 'boolean',
        'is_published' => 'boolean'
    ];

    protected static function booted()
    {
        static::creating(function ($exam) {
            if (empty($exam->slug)) {
                $exam->slug = Str::slug($exam->title) . '-' . time();
            }
        });
    }

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LmsExamQuestion::class, 'exam_id')->orderBy('order_number');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(LmsExamAttempt::class, 'exam_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('exam_type', $type);
    }

    public function scopeAvailable($query)
    {
        $now = now();
        return $query->where('is_published', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_time')
                    ->orWhere('start_time', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_time')
                    ->orWhere('end_time', '>=', $now);
            });
    }

    // Helper methods
    public function isAvailable(): bool
    {
        if (!$this->is_published) return false;

        $now = now();
        if ($this->start_time && $now < $this->start_time) return false;
        if ($this->end_time && $now > $this->end_time) return false;

        return true;
    }

    public function isExpired(): bool
    {
        return $this->end_time && now() > $this->end_time;
    }

    public function canStudentAttempt(Student $student): array
    {
        $result = ['can_attempt' => false, 'reason' => ''];

        // Check if published
        if (!$this->is_published) {
            $result['reason'] = 'Imtihon hali e\'lon qilinmagan';
            return $result;
        }

        // Check time constraints
        if (!$this->isAvailable()) {
            if ($this->start_time && now() < $this->start_time) {
                $result['reason'] = 'Imtihon hali boshlanmagan. Boshlanish vaqti: ' . $this->start_time->format('d.m.Y H:i');
            } else {
                $result['reason'] = 'Imtihon vaqti tugagan';
            }
            return $result;
        }

        // Check group access
        if ($this->group_ids && !empty($this->group_ids)) {
            if (!in_array($student->group_id, $this->group_ids)) {
                $result['reason'] = 'Sizning guruhingiz bu imtihonga kiritilmagan';
                return $result;
            }
        }

        // Check attempt limits
        $attemptCount = $this->attempts()
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->count();

        if ($attemptCount >= $this->max_attempts) {
            $result['reason'] = 'Maksimal urinishlar soni tugagan (' . $this->max_attempts . ' ta)';
            return $result;
        }

        // Check retake delay
        if ($attemptCount > 0 && $this->retake_delay_hours) {
            $lastAttempt = $this->attempts()
                ->where('student_id', $student->id)
                ->latest()
                ->first();

            if ($lastAttempt && $lastAttempt->finished_at) {
                $canRetakeAt = $lastAttempt->finished_at->addHours($this->retake_delay_hours);
                if (now() < $canRetakeAt) {
                    $result['reason'] = 'Qayta topshirish uchun kutish kerak. Keyingi urinish: ' . $canRetakeAt->format('d.m.Y H:i');
                    return $result;
                }
            }
        }

        // Check for in-progress attempt
        $inProgressAttempt = $this->attempts()
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($inProgressAttempt) {
            $result['can_attempt'] = true;
            $result['reason'] = 'Davom etayotgan urinish mavjud';
            $result['attempt'] = $inProgressAttempt;
            return $result;
        }

        $result['can_attempt'] = true;
        $result['attempt_number'] = $attemptCount + 1;
        return $result;
    }

    public function getExamTypeLabel(): string
    {
        return match($this->exam_type) {
            'joriy' => 'Joriy nazorat',
            'oraliq' => 'Oraliq nazorat',
            'yakuniy' => 'Yakuniy nazorat',
            'practice' => 'Mashq test',
            default => $this->exam_type
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'draft' => 'Qoralama',
            'scheduled' => 'Rejalashtirilgan',
            'active' => 'Faol',
            'completed' => 'Yakunlangan',
            'archived' => 'Arxivlangan',
            default => $this->status
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'scheduled' => 'blue',
            'active' => 'green',
            'completed' => 'purple',
            'archived' => 'gray',
            default => 'gray'
        };
    }

    public function getTotalPoints(): float
    {
        return $this->questions()->sum('points');
    }

    public function getAverageScore(): ?float
    {
        return $this->attempts()
            ->whereIn('status', ['submitted', 'graded'])
            ->avg('score');
    }

    public function getPassRate(): float
    {
        $total = $this->attempts()->whereIn('status', ['submitted', 'graded'])->count();
        if ($total === 0) return 0;

        $passed = $this->attempts()
            ->whereIn('status', ['submitted', 'graded'])
            ->where('passed', true)
            ->count();

        return round(($passed / $total) * 100, 2);
    }
}
