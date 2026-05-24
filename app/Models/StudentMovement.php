<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'movement_type',
        'from_group_id',
        'to_group_id',
        'from_faculty_id',
        'to_faculty_id',
        'from_specialty_id',
        'to_specialty_id',
        'from_education_form',
        'to_education_form',
        'from_education_type',
        'to_education_type',
        'reason',
        'order_id',
        'movement_date'
    ];

    protected $casts = [
        'movement_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(StudentOrder::class, 'order_id');
    }

    public function fromGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class, 'from_group_id');
    }

    public function toGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class, 'to_group_id');
    }

    public function fromFaculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'from_faculty_id');
    }

    public function toFaculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'to_faculty_id');
    }

    public function fromSpecialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class, 'from_specialty_id');
    }

    public function toSpecialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class, 'to_specialty_id');
    }

    public function getMovementTypeLabelAttribute(): string
    {
        return match($this->movement_type) {
            'guruh_otkazish' => 'Guruhga o\'tkazish',
            'yonalish_otkazish' => 'Yo\'nalishga o\'tkazish',
            'fakultet_otkazish' => 'Fakultetga o\'tkazish',
            'talim_shakli_otkazish' => 'Ta\'lim shakliga o\'tkazish',
            'grant_contract' => 'Grantdan kontraktga',
            'contract_grant' => 'Kontraktdan grantga',
            'boshqa_otm_dan' => 'Boshqa OTMdan',
            'boshqa_otm_ga' => 'Boshqa OTMga',
            default => $this->movement_type
        };
    }

    public function isGroupTransfer(): bool
    {
        return $this->movement_type === 'guruh_otkazish';
    }

    public function isSpecialtyTransfer(): bool
    {
        return $this->movement_type === 'yonalish_otkazish';
    }

    public function isFacultyTransfer(): bool
    {
        return $this->movement_type === 'fakultet_otkazish';
    }

    public function isEducationFormTransfer(): bool
    {
        return $this->movement_type === 'talim_shakli_otkazish';
    }

    public function isEducationTypeTransfer(): bool
    {
        return in_array($this->movement_type, ['grant_contract', 'contract_grant']);
    }

    public function isExternalTransfer(): bool
    {
        return in_array($this->movement_type, ['boshqa_otm_dan', 'boshqa_otm_ga']);
    }

    public function getFromEducationFormLabelAttribute(): string
    {
        return match($this->from_education_form) {
            'kunduzgi' => 'Kunduzgi',
            'kechki' => 'Kechki',
            'sirtqi' => 'Sirtqi',
            default => $this->from_education_form ?? ''
        };
    }

    public function getToEducationFormLabelAttribute(): string
    {
        return match($this->to_education_form) {
            'kunduzgi' => 'Kunduzgi',
            'kechki' => 'Kechki',
            'sirtqi' => 'Sirtqi',
            default => $this->to_education_form ?? ''
        };
    }

    public function getFromEducationTypeLabelAttribute(): string
    {
        return match($this->from_education_type) {
            'grant' => 'Grant',
            'contract' => 'Kontrakt',
            'super_contract' => 'Super kontrakt',
            'foreign_contract' => 'Xorijiy kontrakt',
            default => $this->from_education_type ?? ''
        };
    }

    public function getToEducationTypeLabelAttribute(): string
    {
        return match($this->to_education_type) {
            'grant' => 'Grant',
            'contract' => 'Kontrakt',
            'super_contract' => 'Super kontrakt',
            'foreign_contract' => 'Xorijiy kontrakt',
            default => $this->to_education_type ?? ''
        };
    }
}