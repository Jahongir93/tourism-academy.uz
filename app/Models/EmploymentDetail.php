<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'position_id',
        'department_id',
        'faculty_id',
        'employment_type',
        'contract_type',
        'stavka',
        'hire_date',
        'contract_end_date',
        'probation_end_date',
        'salary_grade',
        'base_salary'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'contract_end_date' => 'date',
        'probation_end_date' => 'date',
        'stavka' => 'decimal:2',
        'base_salary' => 'decimal:2'
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    // Check if contract is expiring soon
    public function getIsContractExpiringSoonAttribute()
    {
        if (!$this->contract_end_date) {
            return false;
        }
        
        $daysUntilExpiry = now()->diffInDays($this->contract_end_date, false);
        return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
    }

    // Check if in probation
    public function getIsInProbationAttribute()
    {
        if (!$this->probation_end_date) {
            return false;
        }
        
        return now()->lt($this->probation_end_date);
    }

    // Scopes
    public function scopeMain($query)
    {
        return $query->where('employment_type', 'asosiy');
    }

    public function scopeAdditional($query)
    {
        return $query->where('employment_type', 'qoshimcha');
    }

    public function scopePermanent($query)
    {
        return $query->where('contract_type', 'muddatsiz');
    }

    public function scopeTemporary($query)
    {
        return $query->where('contract_type', 'muddatli');
    }

    public function scopeExpiringContracts($query, $days = 30)
    {
        return $query->whereNotNull('contract_end_date')
            ->whereBetween('contract_end_date', [now(), now()->addDays($days)]);
    }
}