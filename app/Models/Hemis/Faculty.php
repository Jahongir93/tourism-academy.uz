<?php

namespace App\Models\Hemis;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    protected $fillable = [
        'university_id',
        'code',
        'name_uz',
        'name_ru',
        'name_en',
        'short_name',
        'dean_name',
        'dean_user_id',
        'phone',
        'email',
        'room',
        'order_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_number' => 'integer',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function dean(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dean_user_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function specialties(): HasMany
    {
        return $this->hasMany(Specialty::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(AcademicGroup::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getActiveStudentsCount(): int
    {
        return $this->students()->where('status', 'active')->count();
    }

    public function getStatistics(): array
    {
        return [
            'departments' => $this->departments()->count(),
            'specialties' => $this->specialties()->count(),
            'groups' => $this->groups()->where('is_active', true)->count(),
            'students' => $this->getActiveStudentsCount(),
            'bakalavr' => $this->students()
                ->whereHas('specialty', fn($q) => $q->where('degree', 'bakalavr'))
                ->where('status', 'active')
                ->count(),
            'magistr' => $this->students()
                ->whereHas('specialty', fn($q) => $q->where('degree', 'magistr'))
                ->where('status', 'active')
                ->count(),
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_number')->orderBy('name_uz');
    }
}