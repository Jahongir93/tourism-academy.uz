<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alumni extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'graduation_year',
        'diploma_number',
        'diploma_series',
        'diploma_date',
        'diploma_type',
        'final_gpa',
        'employment_status',
        'company_name',
        'position',
        'work_phone',
        'linkedin_profile',
        'newsletter_subscription'
    ];

    protected $casts = [
        'diploma_date' => 'date',
        'graduation_year' => 'integer',
        'final_gpa' => 'decimal:2',
        'newsletter_subscription' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getDiplomaTypeLabelAttribute(): string
    {
        return match($this->diploma_type) {
            'bakalavr' => 'Bakalavr',
            'magistr' => 'Magistr',
            'doktorantura' => 'Doktorantura',
            default => $this->diploma_type
        };
    }

    public function getEmploymentStatusLabelAttribute(): string
    {
        return match($this->employment_status) {
            'employed' => 'Ishlamoqda',
            'unemployed' => 'Ishsiz',
            'self_employed' => 'Tadbirkor',
            'studying' => 'O\'qimoqda',
            'unknown' => 'Noma\'lum',
            default => $this->employment_status
        };
    }

    public function getFullDiplomaNumberAttribute(): string
    {
        if ($this->diploma_series) {
            return $this->diploma_series . ' ' . $this->diploma_number;
        }
        
        return $this->diploma_number;
    }

    public function getYearsSinceGraduationAttribute(): int
    {
        return date('Y') - $this->graduation_year;
    }

    public function isEmployed(): bool
    {
        return in_array($this->employment_status, ['employed', 'self_employed']);
    }

    public function isUnemployed(): bool
    {
        return $this->employment_status === 'unemployed';
    }

    public function isStudying(): bool
    {
        return $this->employment_status === 'studying';
    }

    public function hasExcellentGpa(): bool
    {
        return $this->final_gpa >= 4.5;
    }

    public function hasGoodGpa(): bool
    {
        return $this->final_gpa >= 4.0 && $this->final_gpa < 4.5;
    }

    public function hasRedDiploma(): bool
    {
        return $this->final_gpa >= 4.5;
    }

    public function updateEmployment(array $data): void
    {
        $this->update([
            'employment_status' => $data['employment_status'] ?? $this->employment_status,
            'company_name' => $data['company_name'] ?? $this->company_name,
            'position' => $data['position'] ?? $this->position,
            'work_phone' => $data['work_phone'] ?? $this->work_phone,
            'linkedin_profile' => $data['linkedin_profile'] ?? $this->linkedin_profile,
        ]);
    }
}