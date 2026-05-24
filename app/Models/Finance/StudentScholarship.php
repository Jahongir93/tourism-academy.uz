<?php

namespace App\Models\Finance;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentScholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'scholarship_id',
        'awarded_date',
        'start_date',
        'end_date',
        'amount',
        'status',
        'reason',
        'approved_by'
    ];

    protected $casts = [
        'awarded_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ScholarshipPayment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Faol',
            'suspended' => 'To\'xtatilgan',
            'completed' => 'Yakunlangan',
            'cancelled' => 'Bekor qilingan',
            default => $this->status
        };
    }
}
