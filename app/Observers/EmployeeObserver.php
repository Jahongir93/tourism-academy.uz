<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\Teacher;
use App\Models\User;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        // Check if employee should have a teacher record
        $this->syncTeacherRecord($employee);
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        // Check if employee should have a teacher record
        $this->syncTeacherRecord($employee);
    }

    /**
     * Sync teacher record based on employee type or user role
     */
    protected function syncTeacherRecord(Employee $employee): void
    {
        if (!$employee->user_id) {
            return;
        }

        $user = User::find($employee->user_id);
        if (!$user) {
            return;
        }

        // Check if user has Teacher role or employee_type is 'teacher'
        $shouldBeTeacher = $employee->employee_type === 'teacher'
                        || $user->hasRole('Teacher');

        $existingTeacher = Teacher::where('user_id', $employee->user_id)->first();

        if ($shouldBeTeacher && !$existingTeacher) {
            // Create teacher record
            Teacher::create([
                'user_id' => $employee->user_id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'middle_name' => $employee->middle_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'department_id' => null, // Can be set later
                'degree' => null,
                'position' => null,
            ]);
        }
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        // Optionally delete teacher record when employee is deleted
        if ($employee->user_id) {
            Teacher::where('user_id', $employee->user_id)->delete();
        }
    }
}
