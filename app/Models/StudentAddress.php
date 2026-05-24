<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'address_type',
        'region_id',
        'district_id',
        'mahalla',
        'street',
        'house_number',
        'apartment_number',
        'full_address'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [];
        
        if ($this->region) {
            $parts[] = $this->region->name;
        }
        
        if ($this->district) {
            $parts[] = $this->district->name;
        }
        
        if ($this->mahalla) {
            $parts[] = $this->mahalla;
        }
        
        if ($this->street) {
            $parts[] = $this->street;
        }
        
        if ($this->house_number) {
            $parts[] = $this->house_number . '-uy';
        }
        
        if ($this->apartment_number) {
            $parts[] = $this->apartment_number . '-xonadon';
        }
        
        return implode(', ', $parts);
    }

    public function isPermanent(): bool
    {
        return $this->address_type === 'permanent';
    }

    public function isTemporary(): bool
    {
        return $this->address_type === 'temporary';
    }
}