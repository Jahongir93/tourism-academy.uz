<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DormitoryBuilding extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'floors',
        'total_rooms',
        'total_capacity',
        'gender_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'floors' => 'integer',
        'total_rooms' => 'integer',
        'total_capacity' => 'integer',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(DormitoryRoom::class, 'building_id');
    }

    public function activeRooms(): HasMany
    {
        return $this->hasMany(DormitoryRoom::class, 'building_id')->where('is_active', true);
    }

    public function residents(): HasMany
    {
        return $this->hasMany(DormitoryResident::class)
            ->whereHas('room', function ($q) {
                $q->where('building_id', $this->id);
            });
    }

    public function activeResidents(): HasMany
    {
        return $this->residents()->where('status', 'active');
    }

    public function getGenderTypeLabelAttribute(): string
    {
        return match($this->gender_type) {
            'erkak' => 'Erkaklar',
            'ayol' => 'Ayollar',
            'aralash' => 'Aralash',
            default => $this->gender_type
        };
    }

    public function getCurrentOccupancyAttribute(): int
    {
        return $this->activeRooms()->sum('occupied');
    }

    public function getAvailableCapacityAttribute(): int
    {
        return $this->total_capacity - $this->current_occupancy;
    }

    public function getOccupancyRateAttribute(): float
    {
        if ($this->total_capacity == 0) {
            return 0;
        }
        
        return round(($this->current_occupancy / $this->total_capacity) * 100, 2);
    }

    public function isFull(): bool
    {
        return $this->available_capacity <= 0;
    }

    public function isMaleOnly(): bool
    {
        return $this->gender_type === 'erkak';
    }

    public function isFemaleOnly(): bool
    {
        return $this->gender_type === 'ayol';
    }

    public function isMixed(): bool
    {
        return $this->gender_type === 'aralash';
    }
}