<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleSlot extends Model
{
    protected $fillable = [
        'schedule_id',
        'day_of_week',
        'time_slot',
        'subject_id',
        'teacher_id',
        'room_id',
        'lesson_type',
        'week_type'
    ];

    // Map string day names to integers
    protected static $dayMapping = [
        'monday' => 1, 'dushanba' => 1,
        'tuesday' => 2, 'seshanba' => 2,
        'wednesday' => 3, 'chorshanba' => 3,
        'thursday' => 4, 'payshanba' => 4,
        'friday' => 5, 'juma' => 5,
        'saturday' => 6, 'shanba' => 6,
        'sunday' => 7, 'yakshanba' => 7,
    ];

    /**
     * Get day_of_week as integer
     */
    public function getDayNumberAttribute(): int
    {
        $day = $this->day_of_week;

        if (is_numeric($day)) {
            return (int)$day;
        }

        return self::$dayMapping[strtolower($day)] ?? 1;
    }

    /**
     * Convert day name/number to integer for queries
     */
    public static function normalizeDayOfWeek($day): int
    {
        if (is_numeric($day)) {
            return (int)$day;
        }

        return self::$dayMapping[strtolower($day)] ?? 1;
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'room_id');
    }

    public function scopeByDay($query, $day)
    {
        // Handle both string and integer day values
        if (is_numeric($day)) {
            $dayNames = ['', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            return $query->where(function($q) use ($day, $dayNames) {
                $q->where('day_of_week', $day)
                  ->orWhere('day_of_week', $dayNames[$day] ?? '');
            });
        }
        return $query->where('day_of_week', $day);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }
}
