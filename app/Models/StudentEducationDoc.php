<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEducationDoc extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'document_type',
        'document_number',
        'institution_name',
        'graduation_year',
        'gpa',
        'document_scan_url'
    ];

    protected $casts = [
        'gpa' => 'decimal:2',
        'graduation_year' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'attestat' => 'Attestat',
            'diplom' => 'Diplom',
            'certificate' => 'Sertifikat',
            'academic_lyceum' => 'Akademik litsey',
            'college' => 'Kollej',
            default => $this->document_type
        };
    }

    public function isHighSchool(): bool
    {
        return in_array($this->document_type, ['attestat', 'academic_lyceum']);
    }

    public function isCollege(): bool
    {
        return $this->document_type === 'college';
    }

    public function isDiploma(): bool
    {
        return $this->document_type === 'diplom';
    }

    public function hasExcellentGpa(): bool
    {
        return $this->gpa >= 4.5;
    }

    public function hasGoodGpa(): bool
    {
        return $this->gpa >= 4.0 && $this->gpa < 4.5;
    }
}