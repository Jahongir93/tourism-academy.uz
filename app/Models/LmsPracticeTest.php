<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LmsPracticeTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'questions',
        'time_limit',
        'passing_score',
        'test_type',
        'week_number',
        'show_correct_answers',
        'allow_retake',
        'max_attempts',
        'is_active',
        'available_from',
        'available_until'
    ];

    protected $casts = [
        'questions' => 'array',
        'is_active' => 'boolean',
        'show_correct_answers' => 'boolean',
        'allow_retake' => 'boolean',
        'time_limit' => 'integer',
        'passing_score' => 'integer',
        'week_number' => 'integer',
        'max_attempts' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function views(): MorphMany
    {
        return $this->morphMany(LmsContentView::class, 'viewable');
    }

    public function isAvailable(): bool
    {
        $now = now();
        
        if ($this->available_from && $now < $this->available_from) {
            return false;
        }
        
        if ($this->available_until && $now > $this->available_until) {
            return false;
        }
        
        return $this->is_active;
    }

    public function getQuestionCountAttribute(): int
    {
        return count($this->questions ?? []);
    }
}