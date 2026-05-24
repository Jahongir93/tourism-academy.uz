<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id',
        'department_id',
        'position_id',
        'rate',
        'allocated_count',
        'filled_count',
        'vacant_count',
        'status',
        'effective_from',
        'effective_to',
        'notes',
    ];

    protected $casts = [
        'allocated_count' => 'integer',
        'filled_count' => 'integer',
        'vacant_count' => 'integer',
        'rate' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function updateVacantCount(): void
    {
        $this->vacant_count = $this->allocated_count - $this->filled_count;
        $this->save();
    }

    public function hasVacancy(): bool
    {
        return $this->vacant_count > 0;
    }

    public function getUtilizationRate(): float
    {
        if ($this->allocated_count == 0) {
            return 0;
        }
        return round(($this->filled_count / $this->allocated_count) * 100, 2);
    }

    public function scopeWithVacancies($query)
    {
        return $query->where('vacant_count', '>', 0);
    }
}