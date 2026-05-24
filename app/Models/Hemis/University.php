<?php

namespace App\Models\Hemis;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    protected $fillable = [
        'code',
        'name_uz',
        'name_ru',
        'name_en',
        'short_name',
        'address',
        'phone',
        'email',
        'website',
        'rector_name',
        'inn',
        'bank_account',
        'bank_name',
        'bank_mfo',
        'type',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getStatistics(): array
    {
        return [
            'total_faculties' => $this->faculties()->count(),
            'total_departments' => Department::whereIn('faculty_id', $this->faculties->pluck('id'))->count(),
            'total_specialties' => Specialty::whereIn('faculty_id', $this->faculties->pluck('id'))->count(),
            'total_students' => Student::where('status', 'active')
                ->whereIn('faculty_id', $this->faculties->pluck('id'))
                ->count(),
            'total_teachers' => User::role('Teacher')
                ->whereHas('employee', function($q) {
                    $q->where('university_id', $this->id);
                })->count(),
        ];
    }

    public function getCurrentAcademicYear(): int
    {
        return date('Y');
    }

    public function getCurrentSemester(): int
    {
        return date('n') >= 9 || date('n') <= 1 ? 1 : 2;
    }
}