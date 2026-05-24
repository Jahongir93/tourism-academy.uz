<?php

namespace App\Models\Finance;

use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'type',
        'category',
        'amount',
        'transaction_date',
        'description',
        'related_student_id',
        'related_payment_id',
        'receipt_file_url',
        'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = 'TXN' . date('Ymd') . str_pad(static::count() + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'related_student_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(StudentPayment::class, 'related_payment_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function isIncome(): bool
    {
        return $this->type === 'income';
    }

    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'income' => 'Daromad',
            'expense' => 'Xarajat',
            default => $this->type
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'tuition' => 'Kontrakt to\'lovi',
            'scholarship' => 'Stipendiya',
            'grant' => 'Grant',
            'donation' => 'Xayriya',
            'salary' => 'Ish haqi',
            'utility' => 'Kommunal xizmatlar',
            'other' => 'Boshqa',
            default => $this->category
        };
    }
}
