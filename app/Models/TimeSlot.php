<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = [
        'slot_number',
        'start_time',
        'end_time',
        'is_break',
        'slot_type'
    ];

    protected $casts = [
        'is_break' => 'boolean',
        'slot_number' => 'integer'
    ];

    public function scopeRegular($query)
    {
        return $query->where('slot_type', 'regular');
    }

    public function scopeEvening($query)
    {
        return $query->where('slot_type', 'evening');
    }

    public function scopeNotBreak($query)
    {
        return $query->where('is_break', false);
    }

    public function getFormattedTimeAttribute()
    {
        return sprintf('%s - %s', 
            substr($this->start_time, 0, 5),
            substr($this->end_time, 0, 5)
        );
    }
}
