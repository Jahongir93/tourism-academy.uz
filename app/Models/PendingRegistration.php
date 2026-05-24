<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Employee;

class PendingRegistration extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'type',
        'user_type',
        'position',
        'additional_info',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the user who reviewed this registration
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Approve this pending registration and create the actual user/student/employee
     */
    public function approve(int $userId, string $password): bool
    {
        DB::beginTransaction();
        try {
            // Parse name
            $nameParts = explode(' ', trim($this->full_name), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            // Check if user already exists with this phone or email
            $email = $this->email ?? ($this->type === 'student' ? $this->generateStudentEmail() : $this->generateEmployeeEmail());

            $existingUser = User::where('phone', $this->phone)
                ->orWhere('email', $email)
                ->first();

            if ($existingUser) {
                // User already exists - update existing user instead of creating new one
                $existingUser->update([
                    'name' => $this->full_name,
                    'email' => $email,
                    'phone' => $this->phone,
                    'user_type' => $this->user_type,
                    'status' => 'active',
                ]);
                $user = $existingUser;
            } else {
                // Create new user account
                $user = User::create([
                    'name' => $this->full_name,
                    'email' => $email,
                    'phone' => $this->phone,
                    'password' => Hash::make($password),
                    'user_type' => $this->user_type,
                    'status' => 'active',
                    'is_profile_complete' => false,
                ]);
            }

            // Assign role based on type
            if ($this->type === 'student') {
                // Assign role if not already assigned
                if (!$user->hasRole('Student')) {
                    $user->assignRole('Student');
                }

                // Check if student record already exists
                $existingStudent = Student::where('user_id', $user->id)->first();

                if (!$existingStudent) {
                    // Generate student ID
                    $year = date('Y');
                    $lastStudent = Student::where('student_id', 'like', $year . '%')
                        ->orderBy('student_id', 'desc')
                        ->first();

                    $studentId = $lastStudent
                        ? $year . str_pad((int)substr($lastStudent->student_id, 4) + 1, 4, '0', STR_PAD_LEFT)
                        : $year . '0001';

                    // Create student record
                    Student::create([
                        'user_id' => $user->id,
                        'student_id' => $studentId,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'full_name' => $this->full_name,
                        'email' => $user->email,
                        'phone' => $this->phone,
                        'group_id' => null,
                        'faculty_id' => null,
                        'specialty_id' => null,
                        'registration_date' => now(),
                        'status' => 'active',
                        'profile_completed' => false,
                    ]);
                } else {
                    // Update existing student record
                    $existingStudent->update([
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'full_name' => $this->full_name,
                        'email' => $user->email,
                        'phone' => $this->phone,
                        'status' => 'active',
                    ]);
                }
            } else {
                // Assign role if not already assigned
                if (!$user->hasRole('Employee')) {
                    $user->assignRole('Employee');
                }

                // Create employee record if employees table exists
                if (schema()->hasTable('employees')) {
                    $existingEmployee = Employee::where('user_id', $user->id)->first();

                    if (!$existingEmployee) {
                        Employee::create([
                            'user_id' => $user->id,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'full_name' => $this->full_name,
                            'email' => $user->email,
                            'phone' => $this->phone,
                            'position' => $this->position,
                            'hire_date' => now(),
                            'status' => 'active',
                        ]);
                    } else {
                        // Update existing employee record
                        $existingEmployee->update([
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'full_name' => $this->full_name,
                            'email' => $user->email,
                            'phone' => $this->phone,
                            'position' => $this->position,
                            'status' => 'active',
                        ]);
                    }
                }
            }

            // Update pending registration status
            $this->update([
                'status' => 'approved',
                'reviewed_by' => $userId,
                'reviewed_at' => now(),
            ]);

            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            // Check if it's a duplicate entry error
            if ($e->getCode() == 23000) {
                $errorMessage = $e->getMessage();

                // Log with more specific information
                if (strpos($errorMessage, 'users_phone_unique') !== false) {
                    \Log::error('Pending registration approval error: Duplicate phone number', [
                        'phone' => $this->phone,
                        'pending_registration_id' => $this->id
                    ]);
                    throw new \Exception('Bu telefon raqam allaqachon ro\'yxatdan o\'tgan.');
                } elseif (strpos($errorMessage, 'users_email_unique') !== false || strpos($errorMessage, 'email') !== false) {
                    \Log::error('Pending registration approval error: Duplicate email', [
                        'email' => $this->email,
                        'pending_registration_id' => $this->id
                    ]);
                    throw new \Exception('Bu email manzil allaqachon ro\'yxatdan o\'tgan.');
                } else {
                    \Log::error('Pending registration approval error: Duplicate entry', [
                        'error' => $errorMessage,
                        'pending_registration_id' => $this->id
                    ]);
                    throw new \Exception('Bu ma\'lumotlar allaqachon ishlatilgan.');
                }
            }

            // Re-throw the exception for other database errors
            throw $e;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Pending registration approval error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'pending_registration_id' => $this->id
            ]);
            throw $e; // Re-throw to be caught by controller
        }
    }

    /**
     * Reject this pending registration
     */
    public function reject(int $userId, string $reason): bool
    {
        try {
            $this->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_by' => $userId,
                'reviewed_at' => now(),
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error('Pending registration rejection error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if registration is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if registration is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if registration is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Generate unique student email
     */
    private function generateStudentEmail(): string
    {
        $year = date('Y');
        $lastStudent = Student::where('student_id', 'like', $year . '%')
            ->orderBy('student_id', 'desc')
            ->first();

        $number = $lastStudent
            ? (int)substr($lastStudent->student_id, 4) + 1
            : 1;

        return $year . str_pad($number, 4, '0', STR_PAD_LEFT) . '@student.uz';
    }

    /**
     * Generate unique employee email
     */
    private function generateEmployeeEmail(): string
    {
        $baseEmail = strtolower(str_replace(' ', '.', $this->full_name));
        $email = $baseEmail . '@employee.uz';

        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $baseEmail . $counter . '@employee.uz';
            $counter++;
        }

        return $email;
    }

    /**
     * Scope to get pending registrations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved registrations
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected registrations
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to get student registrations
     */
    public function scopeStudents($query)
    {
        return $query->where('type', 'student');
    }

    /**
     * Scope to get employee registrations
     */
    public function scopeEmployees($query)
    {
        return $query->where('type', 'employee');
    }
}
