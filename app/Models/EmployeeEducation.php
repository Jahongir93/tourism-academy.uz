<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $table = 'employee_educations';

    protected $fillable = [
        'employee_id',
        'education_level',
        'institution',
        'faculty',
        'speciality',
        'diploma_number',
        'graduation_date',
        'is_foreign',
        'country',
        'notes'
    ];

    protected $casts = [
        'graduation_date' => 'date',
        'is_foreign' => 'boolean'
    ];

    /**
     * Get the employee that owns the education
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope for bachelor degrees
     */
    public function scopeBachelor($query)
    {
        return $query->where('education_level', 'bachelor');
    }

    /**
     * Scope for master degrees
     */
    public function scopeMaster($query)
    {
        return $query->where('education_level', 'master');
    }

    /**
     * Scope for PhD degrees
     */
    public function scopePhd($query)
    {
        return $query->where('education_level', 'phd');
    }

    /**
     * Scope for DSc degrees
     */
    public function scopeDsc($query)
    {
        return $query->where('education_level', 'dsc');
    }

    /**
     * Get education level display name
     */
    public function getEducationLevelDisplayAttribute()
    {
        $levels = [
            'secondary' => "O'rta ma'lumot",
            'secondary_special' => "O'rta maxsus ma'lumot",
            'bachelor' => 'Bakalavr',
            'master' => 'Magistr',
            'phd' => 'PhD',
            'dsc' => 'DSc',
            'candidate' => 'Fan nomzodi',
            'doctor' => 'Fan doktori'
        ];

        return $levels[$this->education_level] ?? $this->education_level;
    }
}