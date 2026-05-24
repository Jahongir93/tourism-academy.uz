<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    protected $fillable = [
        'name',
        'code',
        'building_id',
        'floor',
        'capacity',
        'type',
        'has_projector',
        'has_computer',
        'has_whiteboard',
        'is_active',
        'equipment',
        'notes'
    ];

    protected $casts = [
        'building_id' => 'integer',
        'floor' => 'integer',
        'capacity' => 'integer',
        'has_projector' => 'boolean',
        'has_computer' => 'boolean',
        'has_whiteboard' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'classroom_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByFloor($query, $floor)
    {
        return $query->where('floor', $floor);
    }

    public function scopeByBuilding($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }

    public function getTypeNameAttribute()
    {
        return match($this->type) {
            'lecture' => 'Ma\'ruza xonasi',
            'lab' => 'Laboratoriya',
            'seminar' => 'Seminar xonasi',
            'computer' => 'Kompyuter xonasi',
            'auditorium' => 'Auditoriya',
            default => $this->type
        };
    }

    public function getFullNameAttribute()
    {
        $building = $this->building ? $this->building->code . '-' : '';
        return $building . $this->name;
    }
}
