<?php

namespace App\Models\Hemis;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'hemis_id',
        'first_name',
        'last_name',
        'middle_name',
        'first_name_en',
        'last_name_en',
        'birth_date',
        'gender',
        'nationality',
        'citizenship',
        'passport_series',
        'passport_number',
        'passport_given_by',
        'passport_given_date',
        'pinfl',
        'phone',
        'parent_phone',
        'email',
        'permanent_address',
        'temporary_address',
        'region',
        'district',
        'faculty_id',
        'specialty_id',
        'group_id',
        'course',
        'semester',
        'education_form',
        'education_type',
        'payment_form',
        'education_language',
        'admission_year',
        'admission_date',
        'admission_order_number',
        'admission_order_date',
        'entrance_exam_score',
        'previous_education',
        'previous_education_doc_number',
        'status',
        'status_changed_at',
        'status_order_number',
        'status_reason',
        'is_foreign',
        'has_dormitory',
        'dormitory_room',
        'gpa',
        'total_credits',
        'additional_data',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'passport_given_date' => 'date',
        'admission_date' => 'date',
        'admission_order_date' => 'date',
        'status_changed_at' => 'date',
        'is_foreign' => 'boolean',
        'has_dormitory' => 'boolean',
        'gpa' => 'decimal:2',
        'entrance_exam_score' => 'decimal:2',
        'additional_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class, 'group_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(StudentOrder::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(StudentContract::class)->latest();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function getFullName(): string
    {
        return trim("{$this->last_name} {$this->first_name} {$this->middle_name}");
    }

    public function getFullNameEn(): string
    {
        return trim("{$this->first_name_en} {$this->last_name_en}");
    }

    public function getAge(): int
    {
        return $this->birth_date->age;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOnAcademicLeave(): bool
    {
        return $this->status === 'academic_leave';
    }

    public function hasDebt(): bool
    {
        if ($this->education_type !== 'shartnoma') {
            return false;
        }

        return $this->payments()
            ->where('academic_year', date('Y'))
            ->where('status', 'pending')
            ->exists();
    }

    public function calculateGPA(): float
    {
        $grades = $this->grades()
            ->where('status', 'completed')
            ->whereNotNull('final_mark')
            ->get();

        if ($grades->isEmpty()) {
            return 0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            $credits = $grade->subject->credits;
            $points = $this->convertGradeToPoints($grade->final_mark);
            
            $totalPoints += $points * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
    }

    private function convertGradeToPoints($mark): float
    {
        return match($mark) {
            '5' => 5.0,
            '4' => 4.0,
            '3' => 3.0,
            '2' => 2.0,
            default => 0.0,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    public function scopeBySpecialty($query, $specialtyId)
    {
        return $query->where('specialty_id', $specialtyId);
    }

    public function scopeByCourse($query, $course)
    {
        return $query->where('course', $course);
    }

    public function scopeByEducationType($query, $type)
    {
        return $query->where('education_type', $type);
    }
}