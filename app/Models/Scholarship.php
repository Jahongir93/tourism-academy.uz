<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholarship extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'scholarships';

    protected $fillable = [
        'name',
        'type',
        'description',
        'amount',
        'amount_type',
        'min_gpa',
        'min_attendance',
        'requirements',
        'status',
        'available_slots',
        'awarded_count',
        'start_date',
        'end_date',
        'application_deadline',
        'sponsor',
        'eligible_programs',
        'eligible_courses',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'min_gpa' => 'decimal:2',
        'requirements' => 'array',
        'eligible_programs' => 'array',
        'eligible_courses' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'application_deadline' => 'date',
    ];

    /**
     * Students who have this scholarship
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_scholarship')
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
     * Active students with this scholarship
     */
    public function activeStudents()
    {
        return $this->belongsToMany(Student::class, 'student_scholarship')
            ->wherePivot('status', 'active');
    }

    /**
     * Active recipients (alias for activeStudents)
     * Using explicit table name to avoid Laravel's automatic pluralization
     */
    public function activeRecipients()
    {
        return $this->belongsToMany(
            Student::class,
            'student_scholarship',  // Explicit table name
            'scholarship_id',       // Foreign key on pivot table
            'student_id'            // Related key on pivot table
        )->wherePivot('status', 'active');
    }

    /**
     * Check if scholarship is active
     */
    public function isActive()
    {
        return $this->status === 'active'
            && (!$this->end_date || $this->end_date->isFuture());
    }

    /**
     * Check if scholarship has available slots
     */
    public function hasAvailableSlots()
    {
        if (!$this->available_slots) {
            return true; // Unlimited slots
        }

        return $this->awarded_count < $this->available_slots;
    }

    /**
     * Get active scholarships
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Get scholarships by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
