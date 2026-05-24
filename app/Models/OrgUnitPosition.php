<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrgUnitPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'org_unit_type',
        'org_unit_id',
        'position_id',
        'position_name',
        'position_code',
        'employee_id',
        'employee_name',
        'rate',
        'status',
        'is_head',
        'appointment_type',
        'appointment_date',
        'appointment_order_id',
        'end_date',
        'workload_percentage',
        'salary',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'end_date' => 'date',
        'workload_percentage' => 'integer',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function appointmentOrder(): BelongsTo
    {
        return $this->belongsTo(AppointmentOrder::class);
    }

    public function orgUnit()
    {
        return match($this->org_unit_type) {
            'university' => $this->belongsTo(University::class, 'org_unit_id'),
            'faculty' => $this->belongsTo(Faculty::class, 'org_unit_id'),
            'department' => $this->belongsTo(Department::class, 'org_unit_id'),
            'division' => $this->belongsTo(Division::class, 'org_unit_id'),
            'center' => $this->belongsTo(Center::class, 'org_unit_id'),
            default => null,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByOrgUnit($query, $type, $id)
    {
        return $query->where('org_unit_type', $type)
                     ->where('org_unit_id', $id);
    }

    public function scopeMain($query)
    {
        return $query->where('appointment_type', 'main');
    }

    public function scopeActing($query)
    {
        return $query->where('appointment_type', 'acting');
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }
}