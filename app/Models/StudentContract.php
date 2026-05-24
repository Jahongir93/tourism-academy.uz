<?php

namespace App\Models;

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
        'contract_date',
        'contract_type',
        'amount',
        'payment_type',
        'payment_schedule',
        'status',
        'file_url',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'contract_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'payment_schedule' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class, 'contract_id');
    }

    public function getContractTypeLabelAttribute(): string
    {
        return match($this->contract_type) {
            'education' => 'Ta\'lim shartnomasi',
            'dormitory' => 'Yotoqxona shartnomasi',
            'additional_service' => 'Qo\'shimcha xizmat shartnomasi',
            default => $this->contract_type
        };
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match($this->payment_type) {
            'full' => 'To\'liq to\'lov',
            'quarterly' => 'Har chorakda',
            'monthly' => 'Oylik',
            default => $this->payment_type
        };
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount - $this->total_paid);
    }

    public function getPaymentProgressAttribute(): float
    {
        if ($this->amount == 0) {
            return 100;
        }
        
        return round(($this->total_paid / $this->amount) * 100, 2);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isPaid(): bool
    {
        return $this->remaining_amount <= 0;
    }

    public function hasOverduePayments(): bool
    {
        if (!$this->payment_schedule) {
            return false;
        }

        $now = now();
        foreach ($this->payment_schedule as $schedule) {
            $dueDate = \Carbon\Carbon::parse($schedule['due_date']);
            $isPaid = $this->payments()
                ->where('status', 'completed')
                ->whereDate('payment_date', '<=', $dueDate)
                ->sum('amount') >= $schedule['amount'];
            
            if ($dueDate->isPast() && !$isPaid) {
                return true;
            }
        }

        return false;
    }
}