<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'check_in',
        'check_out',
        'date',
        'status',
        'face_confidence_score',
        'face_confidence',
        'location',
        'total_hours',
        'manual_override',
        'override_reason',
        'override_by',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'face_confidence_score' => 'float',
        'face_confidence' => 'float',
        'total_hours' => 'float',
        'manual_override' => 'boolean'
    ];

    /**
     * Get the user that owns the attendance
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get attendance logs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    /**
     * Get working hours attribute
     */
    public function getWorkingHoursAttribute()
    {
        if (!$this->check_in || !$this->check_out) {
            return '0:00';
        }

        $diff = $this->check_out->diff($this->check_in);
        return sprintf('%d:%02d', $diff->h, $diff->i);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'present' => 'success',
            'late' => 'warning',
            'very_late' => 'danger',
            'absent' => 'secondary',
            'leave' => 'info',
            default => 'primary'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'present' => 'Hozir',
            'late' => 'Kechikdi',
            'very_late' => 'Juda kechikdi',
            'absent' => 'Kelmadi',
            'leave' => "Ta'tilda",
            default => ucfirst($this->status)
        };
    }

    /**
     * Scope for today's attendance
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    /**
     * Scope for present attendance
     */
    public function scopePresent($query)
    {
        return $query->whereNotNull('check_in');
    }

    /**
     * Scope for late attendance
     */
    public function scopeLate($query)
    {
        return $query->whereIn('status', ['late', 'very_late']);
    }
}