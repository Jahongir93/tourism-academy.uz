<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'type',
        'category',
        'start_date',
        'end_date',
        'max_recipients',
        'current_recipients',
        'eligibility_criteria',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function studentScholarships(): HasMany
    {
        return $this->hasMany(StudentScholarship::class);
    }

    public function activeRecipients(): HasMany
    {
        return $this->studentScholarships()->where('status', 'active');
    }

    public function hasAvailableSlots(): bool
    {
        if (!$this->max_recipients) return true;
        return $this->current_recipients < $this->max_recipients;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getTypeLabelAttribute(): string
    {
        if (!$this->type) {
            return 'Belgilanmagan';
        }

        return match($this->type) {
            'monthly' => 'Oylik',
            'one_time' => 'Bir martalik',
            'annual' => 'Yillik',
            'grant' => 'Grant',
            'scholarship' => 'Stipendiya',
            'discount' => 'Chegirma',
            'financial_aid' => 'Moliyaviy yordam',
            default => $this->type
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        if (!$this->category) {
            return 'Belgilanmagan';
        }

        return match($this->category) {
            'academic' => 'Akademik',
            'social' => 'Ijtimoiy',
            'sport' => 'Sport',
            'cultural' => 'Madaniy',
            'other' => 'Boshqa',
            default => $this->category
        };
    }
}
