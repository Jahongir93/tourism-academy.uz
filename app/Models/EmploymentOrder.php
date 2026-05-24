<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploymentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_date',
        'order_type',
        'employee_id',
        'content',
        'basis',
        'notes',
        'file_url',
        'created_by',
        'approved_by',
        'status'
    ];

    protected $casts = [
        'order_date' => 'date'
    ];

    // Order type labels
    const ORDER_TYPES = [
        'ishga_qabul' => 'Ishga qabul qilish',
        'lavozimga_tayinlash' => 'Lavozimga tayinlash',
        'otkazish' => "O'tkazish",
        'ragbatlantirish' => "Rag'batlantirish",
        'intizomiy_jazo' => 'Intizomiy jazo',
        'ishdan_boshatish' => "Ishdan bo'shatish"
    ];

    // Generate order number
    public static function generateOrderNumber($type = 'K')
    {
        $year = date('Y');
        $lastOrder = self::whereYear('order_date', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastOrder) {
            preg_match('/(\d+)/', $lastOrder->order_number, $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return "{$newNumber}-{$type}";
    }

    // Get order type label
    public function getOrderTypeLabelAttribute()
    {
        return self::ORDER_TYPES[$this->order_type] ?? $this->order_type;
    }

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(EmployeeLeave::class, 'order_id');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('order_type', $type);
    }

    public function scopeCurrentYear($query)
    {
        return $query->whereYear('order_date', date('Y'));
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('order_date', '>=', now()->subDays($days));
    }
}