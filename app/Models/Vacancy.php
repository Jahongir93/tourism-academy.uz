<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacancy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'title_ru',
        'title_en',
        'department',
        'description',
        'description_ru',
        'description_en',
        'requirements',
        'requirements_ru',
        'requirements_en',
        'responsibilities',
        'responsibilities_ru',
        'responsibilities_en',
        'benefits',
        'benefits_ru',
        'benefits_en',
        'employment_type',
        'salary_range',
        'experience_required',
        'education_required',
        'deadline',
        'positions_count',
        'is_active',
        'is_featured',
        'views_count',
        'applications_count',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'positions_count' => 'integer',
        'views_count' => 'integer',
        'applications_count' => 'integer',
    ];

    // Employment type labels
    const EMPLOYMENT_TYPES = [
        'full_time' => 'To\'liq stavka',
        'part_time' => 'Yarim stavka',
        'contract' => 'Shartnoma asosida',
        'internship' => 'Amaliyot',
    ];

    // Relationships
    public function applications()
    {
        return $this->hasMany(VacancyApplication::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('deadline')
              ->orWhere('deadline', '>=', now());
        });
    }

    public function scopePublic($query)
    {
        return $query->active()->notExpired();
    }

    // Accessors
    public function getEmploymentTypeLabelAttribute()
    {
        return self::EMPLOYMENT_TYPES[$this->employment_type] ?? $this->employment_type;
    }

    public function getIsExpiredAttribute()
    {
        return $this->deadline && $this->deadline->isPast();
    }

    public function getNewApplicationsCountAttribute()
    {
        return $this->applications()->where('status', 'new')->count();
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementApplications()
    {
        $this->increment('applications_count');
    }
}
