<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'action_type',
        'image_path',
        'confidence_score',
        'timestamp',
        'metadata'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'confidence_score' => 'float',
        'metadata' => 'array'
    ];

    /**
     * Get the attendance that owns the log
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}