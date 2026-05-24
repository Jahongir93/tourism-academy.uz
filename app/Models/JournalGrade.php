<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalGrade extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'student_id',
        'grade_type',
        'score',
        'max_score',
        'weight_percentage',
        'graded_date',
        'graded_by',
        'notes'
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'weight_percentage' => 'decimal:2',
        'graded_date' => 'date'
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function getWeightedScoreAttribute()
    {
        return ($this->score / $this->max_score) * $this->weight_percentage;
    }
}
