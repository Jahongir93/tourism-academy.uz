<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'admission_date',
        'status',
        'student_id',
        'first_name',
        'last_name',
        'middle_name',
        'full_name',
        'birth_date',
        'gender',
        'passport_series',
        'passport_number',
        'jshshir',
        'address',
        'temporary_address',
        'email',
        'phone',
        'parent_phone',
        'faculty_id',
        'specialty_id',
        'course',
        'semester',
        'education_form',
        'education_type',
        'registration_date',
        'profile_completed',
        'photo_url'
    ];

    // SECURITY: Fields that should NEVER be mass-assigned through user input
    // Use these with caution and explicit assignment only
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'birth_date' => 'date',
        'profile_completed' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->student_id)) {
                $student->student_id = 'STD' . date('Y') . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get full name in latin (computed from first, last, middle names)
     */
    public function getFullNameLatinAttribute(): string
    {
        $parts = array_filter([
            $this->attributes['last_name'] ?? '',
            $this->attributes['first_name'] ?? '',
            $this->attributes['middle_name'] ?? ''
        ]);
        return implode(' ', $parts) ?: ($this->user ? $this->user->name : 'Unknown Student');
    }

    /**
     * BUGFIX #56: Get photo URL accessor for displaying images
     * Converts storage path to public URL
     */
    public function getPhotoAttribute()
    {
        if (empty($this->attributes['photo_url'])) {
            return asset('images/default-avatar.svg'); // Default avatar
        }

        // If already full URL, return as is
        if (filter_var($this->attributes['photo_url'], FILTER_VALIDATE_URL)) {
            return $this->attributes['photo_url'];
        }

        // Convert storage path to public URL
        return asset('storage/' . $this->attributes['photo_url']);
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Attendance records for this student
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    /**
     * Faculty relationship
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Specialty (Yo'nalish) relationship
     */
    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class, 'specialty_id');
    }

    /**
     * Scholarships for this student
     */
    public function scholarships()
    {
        return $this->belongsToMany(Scholarship::class, 'student_scholarship')
            ->withPivot([
                'applied_date',
                'awarded_date',
                'start_date',
                'end_date',
                'status',
                'rejection_reason',
                'amount',
                'amount_paid',
                'gpa_at_application',
                'attendance_at_application',
                'notes',
                'approved_by',
                'approved_at',
            ])
            ->withTimestamps();
    }

    /**
     * Active scholarships for this student
     */
    public function activeScholarships()
    {
        return $this->belongsToMany(Scholarship::class, 'student_scholarship')
            ->wherePivot('status', 'active')
            ->withPivot([
                'applied_date',
                'awarded_date',
                'start_date',
                'end_date',
                'status',
                'amount',
                'amount_paid',
            ])
            ->withTimestamps();
    }

    // Compatibility alias for old group relationship
    public function studentGroup(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function passport(): HasOne
    {
        return $this->hasOne(StudentPassport::class);
    }

    public function educationDocs(): HasMany
    {
        return $this->hasMany(StudentEducationDoc::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(StudentAddress::class);
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(StudentFamilyMember::class);
    }

    // StudentEnrollment table doesn't exist
    // public function enrollments(): HasMany
    // {
    //     return $this->hasMany(StudentEnrollment::class);
    // }

    // public function currentEnrollment(): HasOne
    // {
    //     return $this->hasOne(StudentEnrollment::class)->where('is_active', true)->latest();
    // }

    public function contracts(): HasMany
    {
        return $this->hasMany(StudentContract::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(StudentOrder::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StudentMovement::class);
    }

    /**
     * Assignment submissions for this student
     */
    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Journal grades for this student
     */
    public function journalGrades(): HasMany
    {
        return $this->hasMany(JournalGrade::class);
    }

    /**
     * Get all assignments for this student based on their group
     */
    public function assignments()
    {
        if (!$this->group_id) {
            return Assignment::whereRaw('1 = 0'); // Return empty query
        }

        return Assignment::whereJsonContains('group_ids', $this->group_id)
            ->orWhereJsonContains('group_ids', (string)$this->group_id);
    }

    /**
     * Get pending assignments (not submitted or late)
     */
    public function pendingAssignments()
    {
        $submittedIds = $this->assignmentSubmissions()->pluck('assignment_id')->toArray();

        return $this->assignments()
            ->whereNotIn('id', $submittedIds)
            ->where('deadline', '>=', now())
            ->orderBy('deadline', 'asc');
    }

    /**
     * Get upcoming assignments (deadline within next 7 days)
     */
    public function upcomingAssignments()
    {
        $submittedIds = $this->assignmentSubmissions()->pluck('assignment_id')->toArray();

        return $this->assignments()
            ->whereNotIn('id', $submittedIds)
            ->whereBetween('deadline', [now(), now()->addDays(7)])
            ->orderBy('deadline', 'asc');
    }
}