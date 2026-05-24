<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrgChangesLog extends Model
{
    use HasFactory;

    protected $table = 'org_changes_log';

    protected $fillable = [
        'change_type',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'changed_by',
        'order_number',
        'notes',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function entity()
    {
        return match($this->entity_type) {
            'faculty' => $this->belongsTo(Faculty::class, 'entity_id'),
            'department' => $this->belongsTo(Department::class, 'entity_id'),
            'division' => $this->belongsTo(Division::class, 'entity_id'),
            'center' => $this->belongsTo(Center::class, 'entity_id'),
            'position' => $this->belongsTo(Position::class, 'entity_id'),
            default => null,
        };
    }

    public function scopeByEntity($query, $type, $id)
    {
        return $query->where('entity_type', $type)
                     ->where('entity_id', $id);
    }

    public function scopeByChangeType($query, $type)
    {
        return $query->where('change_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}