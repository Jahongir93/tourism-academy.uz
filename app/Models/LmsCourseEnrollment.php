<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsCourseEnrollment extends Model
{
    protected $table = 'lms_course_enrollments';

    protected $fillable = [
        'course_id',
        'user_id',
        'enrolled_at',
        'status',
        'progress_percentage',
        'completed_resources',
        'total_resources',
        'last_accessed_at',
        'completed_at',
        'login_count',
        'grade',
        'certificate_issued'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'login_count' => 'integer'
    ];

    /**
     * Get the course
     */
    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
