<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_date',
        'order_type',
        'title',
        'description',
        'affected_students',
        'file_url',
        'created_by',
        'approved_by',
        'status'
    ];

    protected $casts = [
        'order_date' => 'date',
        'affected_students' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StudentMovement::class, 'order_id');
    }

    public function getOrderTypeLabelAttribute(): string
    {
        return match($this->order_type) {
            'qabul' => 'Qabul qilish',
            'otkazish' => 'O\'tkazish',
            'chetlashtirish' => 'Chetlashtirish',
            'tiklash' => 'Tiklash',
            'akademik_tatil' => 'Akademik ta\'til',
            'tatildan_qaytish' => 'Ta\'tildan qaytish',
            'bitirish' => 'Bitirish',
            'ism_ozgartirish' => 'Ism o\'zgartirish',
            'stipendiya' => 'Stipendiya',
            'intizomiy_jazo' => 'Intizomiy jazo',
            'ragbatlantirish' => 'Rag\'batlantirish',
            'boshqa' => 'Boshqa',
            default => $this->order_type
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Qoralama',
            'approved' => 'Tasdiqlangan',
            'cancelled' => 'Bekor qilingan',
            default => $this->status
        };
    }

    public function getAffectedStudentsCountAttribute(): int
    {
        return $this->affected_students ? count($this->affected_students) : 0;
    }

    public function students()
    {
        if (!$this->affected_students) {
            return collect();
        }
        
        return Student::whereIn('id', $this->affected_students)->get();
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function approve(User $approver): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $year . '-' . $newNumber;
    }
}