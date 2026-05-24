<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $fillable = [
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'type',
        'deadline',
        'max_score',
        'late_penalty_percent',
        'attachments',
        'group_ids'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'max_score' => 'decimal:2',
        'late_penalty_percent' => 'decimal:2',
        'attachments' => 'array',
        'group_ids' => 'array'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class)->with('user');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function isOverdue(): bool
    {
        return $this->deadline->isPast();
    }

    public function calculateLateScore($originalScore, $submittedAt)
    {
        if ($submittedAt <= $this->deadline) {
            return $originalScore;
        }

        $daysLate = $this->deadline->diffInDays($submittedAt);
        $penalty = $daysLate * $this->late_penalty_percent;
        
        return max(0, $originalScore - ($originalScore * $penalty / 100));
    }
}
