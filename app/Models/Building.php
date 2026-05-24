<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'total_floors',
        'total_rooms',
        'type',
        'is_active',
        'description'
    ];

    protected $casts = [
        'total_floors' => 'integer',
        'total_rooms' => 'integer',
        'is_active' => 'boolean'
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAcademic($query)
    {
        return $query->where('type', 'academic');
    }
}
