<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'parent_id',
        'name_uz',
        'name_ru',
        'name_en',
        'code',
        'type',
        'head_id',
        'location',
        'phone',
        'email',
        'functions',
        'is_active',
    ];

    protected $casts = [
        'functions' => 'array',
        'is_active' => 'boolean',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Division::class, 'parent_id');
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(OrgUnitPosition::class, 'org_unit_id')
            ->where('org_unit_type', 'division');
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