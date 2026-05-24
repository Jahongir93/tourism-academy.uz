<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialty_id',
        'academic_year_id',
        'course_year'
    ];

    protected $casts = [
        'course_year' => 'integer',
        'academic_year_id' => 'integer',
        'specialty_id' => 'integer'
    ];

    // Dummy faculty relationship to prevent errors
    // The faculty_id field doesn't exist in the database
    public function faculty()
    {
        // Return null relationship to prevent errors
        return $this->belongsTo(Faculty::class, 'faculty_id')->whereRaw('1=0');
    }

    // public function department(): BelongsTo
    // {
    //     return $this->belongsTo(Department::class);
    // }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'group_id');
    }

    public function groupSubjects(): HasMany
    {
        return $this->hasMany(GroupSubject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Get subjects for current semester
    public function getCurrentSemesterSubjects()
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $currentSemester = now()->month >= 9 ? 1 : 2; // 1-semestr: sentyabr-yanvar, 2-semestr: fevral-iyun

        return $this->groupSubjects()
            ->with(['subject', 'teacher'])
            ->where('academic_year_id', $currentYear->id ?? 1)
            ->where('semester', $currentSemester)
            ->where('is_active', true)
            ->get();
    }

    // These fields don't exist in the database
    // public function curator(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'curator_id');
    // }

    // public function monitor(): BelongsTo
    // {
    //     return $this->belongsTo(Student::class, 'monitor_student_id');
    // }

    // public function createdBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    public function getCurrentStudentCountAttribute(): int
    {
        return $this->students()->where('status', 'active')->count();
    }

    // Max students field doesn't exist
    // public function getAvailableSlotsAttribute(): int
    // {
    //     return max(0, $this->max_students - $this->current_student_count);
    // }

    // public function hasAvailableSlots(): bool
    // {
    //     return $this->available_slots > 0;
    // }

    public function getFullNameAttribute(): string
    {
        return $this->name . ' (' . $this->course_year . '-kurs)';
    }

    public function scopeActive($query)
    {
        // Since is_active doesn't exist in database, return all non-deleted groups
        return $query->whereNull('deleted_at');
    }

    // Faculty ID doesn't exist in database
    // public function scopeByFaculty($query, $facultyId)
    // {
    //     return $query->where('faculty_id', $facultyId);
    // }

    public function scopeByCourse($query, $course)
    {
        return $query->where('course_year', $course);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year_id', $year);
    }
}