<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faculty extends Model
{
    use HasFactory;

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
        'abbreviation',
        'logo',
        'website',
        'established_date',
        'student_capacity',
        'teacher_capacity',
        'state_funded_places',
        'contract_places'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'established_date' => 'date',
        'student_capacity' => 'integer',
        'teacher_capacity' => 'integer',
        'state_funded_places' => 'integer',
        'contract_places' => 'integer',
    ];

    /**
     * Get the name attribute (defaults to name_uz)
     */
    public function getNameAttribute()
    {
        return $this->name_uz ?? $this->name_ru ?? $this->name_en;
    }

    public function specialties(): HasMany
    {
        return $this->hasMany(Specialty::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(AcademicGroup::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function dean(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dean_user_id');
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(OrgUnitPosition::class, 'org_unit_id')
            ->where('org_unit_type', 'faculty');
    }

    public function staffAllocations(): HasMany
    {
        // staff_allocations jadvalida division_id bor, org_unit_id yo'q
        return $this->hasMany(StaffAllocation::class, 'division_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}