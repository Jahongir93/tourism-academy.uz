<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'student_id',
        'lesson_date',
        'lesson_type',
        'time_slot',
        'status',
        'late_minutes',
        'excuse_document_url',
        'notes',
        'marked_by',
        'marked_at'
    ];

    protected $casts = [
        'lesson_date' => 'date',
        'marked_at' => 'datetime',
        'late_minutes' => 'integer'
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeExcused($query)
    {
        return $query->where('status', 'excused');
    }
}
