<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsEventRegistration extends Model
{
    protected $fillable = [
        'event_id', 'name', 'email', 'phone',
        'organization', 'position', 'notes',
        'status', 'confirmation_code', 'confirmed_at'
    ];

    protected $casts = [
        'confirmed_at' => 'datetime'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(CmsEvent::class, 'event_id');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}