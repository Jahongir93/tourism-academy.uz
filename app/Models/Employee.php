<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// Employee is a separate model with its own table
class Employee extends Model
{
    use HasFactory;

    // Use employees table
    protected $table = 'employees';

    protected $fillable = [
        'employee_code',
        'jshshir',
        'first_name',
        'last_name',
        'middle_name',
        'full_name',
        'birth_date',
        'gender',
        'nationality_id',
        'citizenship_id',
        'passport_series',
        'passport_number',
        'passport_issued_date',
        'passport_issued_by',
        'photo_url',
        'phone',
        'email',
        'telegram',
        'address_permanent',
        'address_current',
        'address',
        'employee_type',
        'department_id',
        'position',
        'hire_date',
        'status',
        'user_id'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'passport_issued_date' => 'date',
        'hire_date' => 'date',
    ];

    // Generate employee code
    public static function generateEmployeeCode()
    {
        $year = date('Y');
        $lastEmployee = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastNumber = intval(substr($lastEmployee->employee_code, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "EMP-{$year}-{$newNumber}";
    }

    // Full name
    public function getFullNameAttribute()
    {
        return "{$this->last_name} {$this->first_name} {$this->middle_name}";
    }

    // Short name
    public function getShortNameAttribute()
    {
        $middle = $this->middle_name ? mb_substr($this->middle_name, 0, 1) . '.' : '';
        return "{$this->last_name} " . mb_substr($this->first_name, 0, 1) . ".{$middle}";
    }

    // Age
    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    // Is teacher
    public function getIsTeacherAttribute()
    {
        return $this->employee_type === 'teacher';
    }

    // Is admin
    public function getIsAdminAttribute()
    {
        return $this->employee_type === 'admin';
    }

    // Scope for teachers
    public function scopeTeachers($query)
    {
        return $query->where('employee_type', 'teacher');
    }

    // Scope for admins
    public function scopeAdmins($query)
    {
        return $query->where('employee_type', 'admin');
    }

    // Scope for active employees
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for employees on leave
    public function scopeOnLeave($query)
    {
        return $query->where('status', 'leave');
    }

    // Scope for inactive employees
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Teacher relationships

    /**
     * Get the teacher assignments for this employee
     * Using group_subjects table for assignments
     */
    public function teacherAssignments()
    {
        return $this->hasMany(GroupSubject::class, 'teacher_id');
    }

    /**
     * Get the subjects assigned to this teacher
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'group_subjects', 'teacher_id', 'subject_id')
            ->withPivot(['is_active'])
            ->withTimestamps()
            ->distinct();
    }

    /**
     * Get the groups assigned to this teacher
     */
    public function groups()
    {
        return $this->belongsToMany(StudentGroup::class, 'group_subjects', 'teacher_id', 'student_group_id')
            ->withTimestamps();
    }

    /**
     * Get the journals for this teacher
     */
    public function journals()
    {
        if (!class_exists(\App\Models\Journal::class)) {
            return $this->hasMany(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasMany(\App\Models\Journal::class, 'teacher_id');
    }

    /**
     * Get the schedules for this teacher
     */
    public function schedules()
    {
        if (!class_exists(\App\Models\Schedule::class)) {
            return $this->hasMany(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasMany(\App\Models\Schedule::class, 'teacher_id');
    }

    /**
     * Get employment detail
     */
    public function employmentDetail()
    {
        if (!class_exists(EmploymentDetail::class)) {
            return $this->hasOne(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasOne(EmploymentDetail::class, 'employee_id');
    }

    /**
     * Get all education records for the employee
     */
    public function educations()
    {
        if (!class_exists(EmployeeEducation::class)) {
            return $this->hasMany(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasMany(EmployeeEducation::class, 'employee_id');
    }

    /**
     * Get the highest education record (relationship)
     */
    public function education()
    {
        if (!class_exists(EmployeeEducation::class)) {
            return $this->hasOne(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasOne(EmployeeEducation::class, 'employee_id')
            ->orderByRaw("FIELD(education_level, 'dsc', 'phd', 'doctor', 'candidate', 'master', 'bachelor', 'secondary_special', 'secondary') ASC")
            ->latest();
    }

    /**
     * Get degrees (scientific degrees)
     */
    public function degrees()
    {
        if (!class_exists(EmployeeDegree::class)) {
            return $this->hasMany(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasMany(EmployeeDegree::class, 'employee_id');
    }

    /**
     * Get documents
     */
    public function documents()
    {
        if (!class_exists(EmployeeDocument::class)) {
            return $this->hasMany(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasMany(EmployeeDocument::class, 'employee_id');
    }

    /**
     * Get orders
     */
    public function orders()
    {
        if (!class_exists(EmployeeOrder::class)) {
            return $this->hasMany(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasMany(EmployeeOrder::class, 'employee_id');
    }

    /**
     * Get leaves
     */
    public function leaves()
    {
        if (!class_exists(EmployeeLeave::class)) {
            return $this->hasMany(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasMany(EmployeeLeave::class, 'employee_id');
    }

    /**
     * Get workloads (for teachers)
     */
    public function workloads()
    {
        if (!class_exists(TeacherWorkload::class)) {
            return $this->hasMany(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasMany(TeacherWorkload::class, 'teacher_id');
    }

    /**
     * Get employment contracts
     */
    public function employmentContracts()
    {
        if (!class_exists(EmploymentContract::class)) {
            return $this->hasMany(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasMany(EmploymentContract::class, 'employee_id');
    }

    /**
     * Get current employment contract
     */
    public function currentContract()
    {
        if (!class_exists(EmploymentContract::class)) {
            return $this->hasOne(self::class, 'id')->whereRaw('1 = 0');
        }
        return $this->hasOne(EmploymentContract::class, 'employee_id')
            ->where('status', 'active')
            ->latest();
    }

    /**
     * Department relationship
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Position relationship
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Calculate workload for teacher
     */
    public function calculateWorkload($academicYear = null, $semester = null)
    {
        try {
            $query = $this->teacherAssignments()
                ->where('is_active', true);

            if ($academicYear) {
                $query->where('academic_year_id', $academicYear);
            }

            if ($semester) {
                $query->where('semester', $semester);
            }

            // Count active assignments as a simple workload measure
            return $query->count() * 2; // 2 hours per assignment as estimate
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Check if employee is currently teaching
     */
    public function isCurrentlyTeaching()
    {
        try {
            return $this->teacherAssignments()
                ->where('is_active', true)
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the user associated with this employee
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the teacher record if this employee is a teacher
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'user_id', 'user_id');
    }

    /**
     * Get the nationality of the employee
     */
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    /**
     * Get the citizenship of the employee
     */
    public function citizenship(): BelongsTo
    {
        return $this->belongsTo(Citizenship::class);
    }
}