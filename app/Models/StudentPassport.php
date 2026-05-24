<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPassport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'series',
        'number',
        'issue_date',
        'issued_by',
        'expiry_date',
        'passport_scan_url'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getFullNumberAttribute(): string
    {
        return $this->series . $this->number;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}