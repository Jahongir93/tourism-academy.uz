<?php

namespace App\Models\Finance;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_scholarship_id',
        'amount',
        'payment_date',
        'payment_reference',
        'status',
        'notes',
        'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function studentScholarship(): BelongsTo
    {
        return $this->belongsTo(StudentScholarship::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Kutilmoqda',
            'paid' => 'To\'langan',
            'cancelled' => 'Bekor qilingan',
            default => $this->status
        };
    }
}
