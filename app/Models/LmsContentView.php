<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LmsContentView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'viewable_type',
        'viewable_id',
        'view_duration',
        'completion_percentage',
        'is_completed',
        'completed_at',
        'last_viewed_at',
        'view_count'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'view_duration' => 'integer',
        'completion_percentage' => 'decimal:2',
        'view_count' => 'integer',
        'completed_at' => 'datetime',
        'last_viewed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function recordView(int $duration = 0)
    {
        $this->view_count++;
        $this->view_duration += $duration;
        $this->last_viewed_at = now();
        $this->save();
    }

    public function updateProgress(float $percentage)
    {
        $this->completion_percentage = min(100, $percentage);
        
        if ($this->completion_percentage >= 100) {
            $this->is_completed = true;
            $this->completed_at = now();
        }
        
        $this->save();
    }

    public function markAsCompleted()
    {
        $this->is_completed = true;
        $this->completed_at = now();
        $this->completion_percentage = 100;
        $this->save();
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}