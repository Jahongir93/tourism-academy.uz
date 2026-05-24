<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_count',
        'substitute_id',
        'order_id',
        'reason',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_count' => 'integer'
    ];

    // Leave type labels
    const LEAVE_TYPES = [
        'mehnat_tatili' => "Mehnat ta'tili",
        'oqitish_tatili' => "O'qitish ta'tili",
        'tibbiy_tatil' => "Tibbiy ta'til",
        'homiladorlik' => "Homiladorlik va tug'ruq ta'tili",
        'bola_parvarish' => "Bola parvarish ta'tili",
        'haq_tolanmaydigan' => "Haq to'lanmaydigan ta'til"
    ];

    // Auto-calculate days count
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->start_date && $model->end_date) {
                $model->days_count = $model->start_date->diffInDays($model->end_date) + 1;
            }
        });
    }

    // Get leave type label
    public function getLeaveTypeLabelAttribute()
    {
        return self::LEAVE_TYPES[$this->leave_type] ?? $this->leave_type;
    }

    // Check if leave is active
    public function getIsActiveAttribute()
    {
        return $this->status === 'approved' && 
               now()->between($this->start_date, $this->end_date);
    }

    // Check if leave is upcoming
    public function getIsUpcomingAttribute()
    {
        return $this->status === 'approved' && 
               now()->lt($this->start_date);
    }

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function substitute(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'substitute_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(EmploymentOrder::class, 'order_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'approved')
            ->whereDate('start_date', '>', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('leave_type', $type);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }
}