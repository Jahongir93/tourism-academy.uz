<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name_uz',
        'name_ru',
        'name_en',
        'short_name',
        'logo',
        'address',
        'phone',
        'fax',
        'email',
        'website',
        'rector_name',
        'founded_year',
        'license_number',
        'accreditation_number',
        'student_count',
        'teacher_count',
        'description_uz',
        'description_ru',
        'description_en',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'student_count' => 'integer',
        'teacher_count' => 'integer',
        'founded_year' => 'integer',
    ];

    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function centers(): HasMany
    {
        return $this->hasMany(Center::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(OrgUnitPosition::class, 'org_unit_id')
            ->where('org_unit_type', 'university');
    }

    public function getNameAttribute()
    {
        return $this->name_uz ?? $this->name_ru ?? $this->name_en;
    }
}