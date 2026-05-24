<?php

namespace App\Models\Hemis;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'faculty_id',
        'code',
        'name_uz',
        'name_ru',
        'name_en',
        'short_name',
        'head_user_id',
        'head_name',
        'phone',
        'email',
        'room',
        'type',
        'order_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_number' => 'integer',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function specialties(): HasMany
    {
        return $this->hasMany(Specialty::class);
    }
}