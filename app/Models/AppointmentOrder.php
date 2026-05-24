<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AppointmentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_date',
        'employee_id',
        'position_id',
        'org_unit_type',
        'org_unit_id',
        'appointment_type',
        'start_date',
        'end_date',
        'salary',
        'notes',
        'file_url',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orgUnitPosition(): HasOne
    {
        return $this->hasOne(OrgUnitPosition::class);
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

    public function scopeByAppointmentType($query, $type)
    {
        return $query->where('appointment_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        });
    }

    public function isActive(): bool
    {
        return !$this->end_date || $this->end_date->isFuture();
    }
}