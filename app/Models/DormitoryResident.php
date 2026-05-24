<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DormitoryResident extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'room_id',
        'bed_number',
        'check_in_date',
        'check_out_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(DormitoryRoom::class, 'room_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Yashayapti',
            'checked_out' => 'Chiqib ketgan',
            'temporary_leave' => 'Vaqtinchalik ta\'til',
            default => $this->status
        };
    }

    public function getResidenceDurationAttribute(): int
    {
        $startDate = $this->check_in_date;
        $endDate = $this->check_out_date ?? now();
        
        return $startDate->diffInDays($endDate);
    }

    public function getResidenceDurationFormattedAttribute(): string
    {
        $days = $this->residence_duration;
        
        if ($days >= 365) {
            $years = floor($days / 365);
            $remainingDays = $days % 365;
            $months = floor($remainingDays / 30);
            $remainingDays = $remainingDays % 30;
            
            $result = $years . ' yil';
            if ($months > 0) {
                $result .= ', ' . $months . ' oy';
            }
            if ($remainingDays > 0) {
                $result .= ', ' . $remainingDays . ' kun';
            }
            
            return $result;
        } elseif ($days >= 30) {
            $months = floor($days / 30);
            $remainingDays = $days % 30;
            
            $result = $months . ' oy';
            if ($remainingDays > 0) {
                $result .= ', ' . $remainingDays . ' kun';
            }
            
            return $result;
        } else {
            return $days . ' kun';
        }
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCheckedOut(): bool
    {
        return $this->status === 'checked_out';
    }

    public function isOnTemporaryLeave(): bool
    {
        return $this->status === 'temporary_leave';
    }

    public function checkOut(): void
    {
        $this->update([
            'status' => 'checked_out',
            'check_out_date' => now()
        ]);

        $this->room->decrement('occupied');
    }

    public function setTemporaryLeave(?string $notes = null): void
    {
        $this->update([
            'status' => 'temporary_leave',
            'notes' => $notes
        ]);
    }

    public function returnFromLeave(): void
    {
        $this->update([
            'status' => 'active',
            'notes' => null
        ]);
    }
}