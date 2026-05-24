<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'department_id',
        'faculty_id',
        'specialty_id',
        'course',
        'academic_year',
        'semester',
        'students_count',
        'max_students',
        'current_students',
        'curator_id',
        'language',
        'is_active',
        'education_type',
    ];

    protected $casts = [
        'course' => 'integer',
        'students_count' => 'integer',
        'max_students' => 'integer',
        'current_students' => 'integer',
        'academic_year' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($group) {
            // Use service to create journal entries
            $service = app(\App\Services\GroupJournalService::class);
            $service->createJournalEntriesForGroup($group);
        });
    }

    /**
     * Get the department that owns the group
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the students in this group
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the journal entries for this group
     */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * Get the specialty through department
     */
    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * Get the faculty for this group
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the curator (user) for this group
     */
    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    /**
     * Get the curator as employee
     */
    public function curatorEmployee()
    {
        if (!$this->curator_id) {
            return null;
        }
        return Employee::where('user_id', $this->curator_id)->first();
    }

    /**
     * Manually assign a subject with teacher to this group
     */
    public function assignSubject($subjectId, $teacherId, $academicYearId = null, $semesterId = null)
    {
        $service = app(\App\Services\GroupJournalService::class);
        return $service->assignTeacherToSubject($this, $subjectId, $teacherId, $academicYearId, $semesterId);
    }

    /**
     * Get all subjects assigned to this group
     */
    public function getAssignedSubjects()
    {
        return Subject::whereIn('id', function($query) {
            $query->select('subject_id')
                ->from('journal_entries')
                ->where('group_id', $this->id);
        })->get();
    }

    /**
     * Get all teachers teaching this group
     */
    public function getTeachers()
    {
        return Teacher::whereIn('id', function($query) {
            $query->select('teacher_id')
                ->from('journal_entries')
                ->where('group_id', $this->id)
                ->distinct();
        })->get();
    }
}
