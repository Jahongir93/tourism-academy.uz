<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'code',
        'name',  // For backward compatibility
        'name_uz',
        'name_ru',
        'name_en',
        'short_name',
        'type',
        'head_id',
        'room_number',
        'phone',
        'email',
        'established_date',
        'staff_capacity',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'established_date' => 'date',
        'staff_capacity' => 'integer',
    ];

    // Return empty collection if table doesn't exist
    public static function all($columns = ['*'])
    {
        try {
            return parent::all($columns);
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    // Return 0 if table doesn't exist
    public static function safeCount()
    {
        try {
            return parent::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function specialties(): HasMany
    {
        return $this->hasMany(Specialty::class);
    }


    public function positions(): HasMany
    {
        return $this->hasMany(OrgUnitPosition::class, 'org_unit_id')
            ->where('org_unit_type', 'department');
    }

    public function staffAllocations(): HasMany
    {
        return $this->hasMany(StaffAllocation::class, 'department_id');
    }

    public function getNameAttribute()
    {
        return $this->name_uz ?? $this->name_ru ?? $this->name_en;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}