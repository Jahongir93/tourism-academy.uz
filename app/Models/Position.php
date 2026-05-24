<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',  // For backward compatibility
        'name_uz',
        'name_ru',
        'name_en',
        'code',
        'category',
        'level',
        'salary_grade',
        'requirements',
        'responsibilities',
        'is_active',
    ];

    protected $casts = [
        'requirements' => 'array',
        'responsibilities' => 'array',
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    public function orgUnitPositions(): HasMany
    {
        return $this->hasMany(OrgUnitPosition::class);
    }

    public function reportsTo(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'position_hierarchy', 'position_id', 'reports_to_position_id')
            ->withPivot('hierarchy_type')
            ->withTimestamps();
    }

    public function subordinates(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'position_hierarchy', 'reports_to_position_id', 'position_id')
            ->withPivot('hierarchy_type')
            ->withTimestamps();
    }

    public function staffAllocations(): HasMany
    {
        return $this->hasMany(StaffAllocation::class);
    }

    public function appointmentOrders(): HasMany
    {
        return $this->hasMany(AppointmentOrder::class);
    }

    public function getNameAttribute()
    {
        return $this->name_uz ?? $this->name_ru ?? $this->name_en;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeLeadership($query)
    {
        return $query->where('category', 'leadership');
    }

    public function scopeAcademic($query)
    {
        return $query->where('category', 'academic');
    }
}