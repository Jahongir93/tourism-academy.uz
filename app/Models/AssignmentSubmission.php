<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'submitted_at',
        'files',
        'text_content',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
        'status'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'files' => 'array',
        'score' => 'decimal:2'
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isLate(): bool
    {
        return $this->submitted_at > $this->assignment->deadline;
    }

    public function getFinalScoreAttribute()
    {
        if ($this->isLate()) {
            return $this->assignment->calculateLateScore($this->score, $this->submitted_at);
        }
        return $this->score;
    }
}
