<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DormitoryRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'room_number',
        'floor',
        'capacity',
        'occupied',
        'gender_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'floor' => 'integer',
        'capacity' => 'integer',
        'occupied' => 'integer',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(DormitoryBuilding::class, 'building_id');
    }

    public function residents(): HasMany
    {
        return $this->hasMany(DormitoryResident::class, 'room_id');
    }

    public function activeResidents(): HasMany
    {
        return $this->hasMany(DormitoryResident::class, 'room_id')->where('status', 'active');
    }

    public function getGenderTypeLabelAttribute(): string
    {
        return match($this->gender_type) {
            'erkak' => 'Erkaklar',
            'ayol' => 'Ayollar',
            default => $this->gender_type
        };
    }

    public function getAvailableBedsAttribute(): int
    {
        return $this->capacity - $this->occupied;
    }

    public function getOccupancyRateAttribute(): float
    {
        if ($this->capacity == 0) {
            return 0;
        }
        
        return round(($this->occupied / $this->capacity) * 100, 2);
    }

    public function getFullRoomNumberAttribute(): string
    {
        return $this->building->name . ' - ' . $this->room_number;
    }

    public function isFull(): bool
    {
        return $this->occupied >= $this->capacity;
    }

    public function hasAvailableBeds(): bool
    {
        return $this->available_beds > 0;
    }

    public function isMaleRoom(): bool
    {
        return $this->gender_type === 'erkak';
    }

    public function isFemaleRoom(): bool
    {
        return $this->gender_type === 'ayol';
    }

    public function canAccommodateGender(string $gender): bool
    {
        if ($gender === 'erkak' && $this->isMaleRoom()) {
            return true;
        }
        
        if ($gender === 'ayol' && $this->isFemaleRoom()) {
            return true;
        }
        
        return false;
    }

    public function checkInResident(Student $student, ?string $bedNumber = null): DormitoryResident
    {
        if ($this->isFull()) {
            throw new \Exception('Xona to\'lgan');
        }

        if (!$this->canAccommodateGender($student->gender)) {
            throw new \Exception('Talaba jinsi xona turiga mos kelmaydi');
        }

        $resident = DormitoryResident::create([
            'student_id' => $student->id,
            'room_id' => $this->id,
            'bed_number' => $bedNumber,
            'check_in_date' => now(),
            'status' => 'active'
        ]);

        $this->increment('occupied');

        return $resident;
    }

    public function checkOutResident(DormitoryResident $resident): void
    {
        $resident->update([
            'status' => 'checked_out',
            'check_out_date' => now()
        ]);

        $this->decrement('occupied');
    }
}