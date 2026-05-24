<?php

namespace App\Models\Finance;

use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'contract_number',
        'total_amount',
        'paid_amount',
        'discount_amount',
        'discount_reason',
        'contract_date',
        'start_date',
        'end_date',
        'payment_type',
        'installment_count',
        'status',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'contract_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class, 'contract_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_amount - $this->discount_amount;
    }

    public function getPaymentProgressAttribute(): float
    {
        if ($this->total_amount == 0) return 0;
        return ($this->paid_amount / $this->total_amount) * 100;
    }

    public function isFullyPaid(): bool
    {
        return $this->remaining_amount <= 0;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Faol',
            'completed' => 'Yakunlangan',
            'cancelled' => 'Bekor qilingan',
            'suspended' => 'To\'xtatilgan',
            default => $this->status
        };
    }
}
