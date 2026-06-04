<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmploymentDetail;
use App\Models\Position;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Nationality;
use App\Models\Citizenship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['employmentDetail.position', 'employmentDetail.department', 'employmentDetail.faculty']);

        // Filter by employee type
        if ($request->has('type')) {
            $query->where('employee_type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by faculty
        if ($request->has('faculty_id')) {
            $query->whereHas('employmentDetail', function($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        // Filter by department
        if ($request->has('department_id')) {
            $query->whereHas('employmentDetail', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('jshshir', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $faculties = Faculty::all();
        $departments = Department::all();

        return view('employees.index', compact('employees', 'faculties', 'departments'));
    }

    public function teachers(Request $request)
    {
        $request->merge(['type' => 'teacher']);
        return $this->index($request);
    }

    public function administrative(Request $request)
    {
        $request->merge(['type' => 'admin']);
        return $this->index($request);
    }

    public function create()
    {
        $positions = Position::all();
        $departments = Department::all();
        $faculties = Faculty::all();
        $nationalities = Nationality::active()->get();
        $citizenships = Citizenship::active()->get();

        return view('employees.create', compact(
            'positions', 
            'departments', 
            'faculties',
            'nationalities',
            'citizenships'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Personal information
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',

            // SECURITY FIX: JSHSHIR with regex validation (BUG #19)
            'jshshir' => [
                'nullable',
                'string',
                'size:14',
                'regex:/^[0-9]{14}$/',
                'unique:employees,jshshir'
            ],

            // SECURITY FIX: Birth date range validation (BUG #26)
            'birth_date' => [
                'required',
                'date',
                'before:today',
                'after:' . now()->subYears(100)->format('Y-m-d'),
                'before:' . now()->subYears(18)->format('Y-m-d')
            ],
            'gender' => 'required|in:male,female',
            'nationality_id' => 'nullable|exists:nationalities,id',
            'citizenship_id' => 'nullable|exists:citizenships,id',

            // SECURITY FIX: Passport with uniqueness (BUG #21)
            'passport_series' => [
                'nullable',
                'string',
                'size:2',
                'required_with:passport_number'
            ],
            'passport_number' => [
                'nullable',
                'string',
                'size:7',
                'required_with:passport_series',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->passport_series) {
                        $exists = Employee::where('passport_series', $request->passport_series)
                            ->where('passport_number', $value)
                            ->exists();
                        if ($exists) {
                            $fail('Bu passport raqami allaqachon ro\'yxatdan o\'tgan.');
                        }
                    }
                }
            ],
            'passport_issued_date' => 'nullable|date',
            'passport_issued_by' => 'nullable|string|max:255',

            // SECURITY FIX: Phone with uniqueness (BUG #20)
            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:employees,phone',
                'unique:users,phone'
            ],
            'email' => 'nullable|email|max:255',
            'telegram' => 'nullable|string|max:255',
            'address_permanent' => 'required|string',
            'address_current' => 'nullable|string',
            
            // Employment
            'employee_type' => 'required|in:teacher,admin,support',
            'position_id' => 'required|exists:positions,id',
            'department_id' => 'nullable|exists:departments,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'employment_type' => 'required|in:asosiy,qoshimcha',
            'contract_type' => 'required|in:muddatli,muddatsiz',
            'stavka' => 'required|numeric|min:0.25|max:1.5',
            'hire_date' => 'required|date',
            'contract_end_date' => 'nullable|date|after:hire_date',
            'probation_end_date' => 'nullable|date|after:hire_date',
            'salary_grade' => 'nullable|string|max:50',
            'base_salary' => 'nullable|numeric|min:0',
            
            // Photo
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // SECURITY FIX: Thread-safe employee code generation (BUG #22)
            $employeeCode = null;
            $maxRetries = 5;
            for ($i = 0; $i < $maxRetries; $i++) {
                $lastEmployee = Employee::lockForUpdate()->orderBy('id', 'desc')->first();
                $nextNumber = $lastEmployee ? ($lastEmployee->id + 1) : 1;
                $candidateCode = 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                if (!Employee::where('employee_code', $candidateCode)->exists()) {
                    $employeeCode = $candidateCode;
                    break;
                }

                $employeeCode = 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT) . '-' . rand(10, 99);
                if (!Employee::where('employee_code', $employeeCode)->exists()) {
                    break;
                }
            }

            if (!$employeeCode) {
                throw new \Exception('Employee code yaratishda xatolik yuz berdi. Qaytadan urinib ko\'ring.');
            }

            $validated['employee_code'] = $employeeCode;

            // SECURITY FIX: Upload photo AFTER transaction starts but track path (BUG #24)
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                if ($photo->isValid()) {
                    // Generate unique filename: timestamp_employeecode_originalname.ext
                    $filename = time() . '_' . ($validated['employee_code'] ?? 'emp') . '_' . Str::slug(pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $photo->getClientOriginalExtension();
                    // Move to public/images/employees/
                    $photo->move(public_path('images/employees'), $filename);
                    $photoPath = 'images/employees/' . $filename;
                    $validated['photo_url'] = $photoPath;
                } else {
                    throw new \Exception('Rasm yuklashda xatolik: ' . $photo->getErrorMessage());
                }
            }

            // Create employee
            $employeeData = collect($validated)->except([
                'position_id', 'department_id', 'faculty_id',
                'employment_type', 'contract_type', 'stavka',
                'hire_date', 'contract_end_date', 'probation_end_date',
                'salary_grade', 'base_salary', 'photo'
            ])->toArray();

            $employee = Employee::create($employeeData);
            
            // Create employment detail
            $employmentData = collect($validated)->only([
                'position_id', 'department_id', 'faculty_id',
                'employment_type', 'contract_type', 'stavka',
                'hire_date', 'contract_end_date', 'probation_end_date',
                'salary_grade', 'base_salary'
            ])->toArray();
            $employmentData['employee_id'] = $employee->id;
            
            EmploymentDetail::create($employmentData);
            
            DB::commit();
            
            return redirect()->route('employees.show', $employee)
                ->with('success', 'Xodim muvaffaqiyatli qo\'shildi!');
                
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded photo if exists
            if (isset($photoPath) && file_exists(public_path($photoPath))) {
                unlink(public_path($photoPath));
            }

            return back()->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'employmentDetail.position',
            'employmentDetail.department',
            'employmentDetail.faculty',
            'educations',
            'degrees',
            'documents',
            'orders',
            'leaves'
        ]);

        // Get additional data for teachers
        if ($employee->is_teacher) {
            try {
                $employee->load([
                    'subjects',
                    'groups'
                ]);
                // Only load workloads if table exists
                if (\Schema::hasTable('teacher_workloads')) {
                    $employee->load('workloads');
                }
            } catch (\Exception $e) {
                // Silently handle missing tables
            }
        }

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        // Load employee relationships so form fields are pre-populated
        $employee->load([
            'employmentDetail.position',
            'employmentDetail.department',
            'employmentDetail.faculty',
            'user',
            'nationality',
            'citizenship'
        ]);

        $positions = Position::all();
        $departments = Department::all();
        $faculties = Faculty::all();
        $nationalities = Nationality::active()->get();
        $citizenships = Citizenship::active()->get();

        return view('employees.edit', compact(
            'employee',
            'positions',
            'departments',
            'faculties',
            'nationalities',
            'citizenships'
        ));
    }

    public function update(Request $request, Employee $employee)
    {
        // Build email validation rule - nullable but unique if provided
        $emailRule = 'nullable|email|max:255|unique:employees,email,' . $employee->id;
        if ($employee->user_id) {
            $emailRule .= '|unique:users,email,' . $employee->user_id;
        }

        $validated = $request->validate([
            // Personal information
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',

            // JSHSHIR - nullable but validate format and uniqueness if provided
            'jshshir' => [
                'nullable',
                'string',
                'size:14',
                'regex:/^[0-9]{14}$/',
                'unique:employees,jshshir,' . $employee->id
            ],
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'nationality_id' => 'nullable|exists:nationalities,id',
            'citizenship_id' => 'nullable|exists:citizenships,id',

            // Passport - nullable but validate format if provided
            'passport_series' => [
                'nullable',
                'string',
                'size:2',
                'required_with:passport_number'
            ],
            'passport_number' => [
                'nullable',
                'string',
                'size:7',
                'required_with:passport_series',
                function ($attribute, $value, $fail) use ($request, $employee) {
                    if ($value && $request->passport_series) {
                        $exists = Employee::where('passport_series', $request->passport_series)
                            ->where('passport_number', $value)
                            ->where('id', '!=', $employee->id)
                            ->exists();
                        if ($exists) {
                            $fail('Bu passport raqami allaqachon ro\'yxatdan o\'tgan.');
                        }
                    }
                }
            ],
            'passport_issued_date' => 'nullable|date',
            'passport_issued_by' => 'nullable|string|max:255',

            // Contact
            'phone' => 'required|string|max:20|unique:employees,phone,' . $employee->id,
            'email' => $emailRule,
            'telegram' => 'nullable|string|max:255',
            'address_permanent' => 'nullable|string',
            'address_current' => 'nullable|string',

            // Employee type and status
            'employee_type' => 'required|in:teacher,admin,support',
            'status' => 'required|in:active,inactive,leave,terminated',

            // Employment details
            'position_id' => 'nullable|exists:positions,id',
            'department_id' => 'nullable|exists:departments,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'stavka' => 'nullable|numeric|min:0.25|max:1.5',
            'contract_type' => 'nullable|in:muddatli,muddatsiz',
            'hire_date' => 'nullable|date',
            'employment_type' => 'nullable|in:asosiy,qoshimcha',

            // Education
            'education_level' => 'nullable|in:secondary,vocational,bachelor,master,phd,dsc',
            'academic_degree' => 'nullable|in:phd,dsc,docent,professor',
            'specialization' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',

            // Password
            'password' => 'nullable|string|min:8|confirmed',

            // Photo
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Public teacher profile (shown on /teachers, /cms/teachers)
            'bio_uz' => 'nullable|string',
            'bio_ru' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'show_on_site' => 'nullable|boolean',
            'public_order' => 'nullable|integer|min:0',
        ]);

        // Checkbox: explicit boolean so unchecking persists
        $validated['show_on_site'] = $request->boolean('show_on_site');

        DB::beginTransaction();
        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');

                // Delete old photo from public folder
                if ($employee->photo_url && file_exists(public_path($employee->photo_url))) {
                    unlink(public_path($employee->photo_url));
                }

                // Generate unique filename: timestamp_employeecode_originalname.ext
                $filename = time() . '_' . $employee->employee_code . '_' . Str::slug(pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $photo->getClientOriginalExtension();
                // Move to public/images/employees/
                $photo->move(public_path('images/employees'), $filename);
                $validated['photo_url'] = 'images/employees/' . $filename;
            }

            // Update full name field
            $validated['name'] = $validated['last_name'] . ' ' . $validated['first_name'] . ' ' . ($validated['middle_name'] ?? '');

            // Handle password update - update or create linked User
            if (!empty($validated['password'])) {
                if ($employee->user_id && $employee->user) {
                    // Update existing user's password
                    $employee->user->update([
                        'password' => bcrypt($validated['password'])
                    ]);
                    $user = $employee->user;
                } else {
                    // Check if user with this email already exists
                    $existingUser = \App\Models\User::where('email', $validated['email'])->first();

                    if ($existingUser) {
                        // Link to existing user and update password
                        $existingUser->update([
                            'name' => $validated['name'],
                            'password' => bcrypt($validated['password']),
                        ]);
                        $user = $existingUser;
                    } else {
                        // Create new User account for this employee
                        $user = \App\Models\User::create([
                            'name' => $validated['name'],
                            'email' => $validated['email'],
                            'password' => bcrypt($validated['password']),
                        ]);
                    }

                    // Assign Teacher role if employee is a teacher
                    if ($validated['employee_type'] === 'teacher') {
                        if (!$user->hasRole('Teacher')) {
                            $user->assignRole('Teacher');
                        }

                        // SECURITY FIX: Thread-safe Teacher record creation (BUG #25)
                        $existingTeacher = \App\Models\Teacher::lockForUpdate()
                            ->where('user_id', $user->id)
                            ->first();

                        if (!$existingTeacher) {
                            \App\Models\Teacher::create([
                                'user_id' => $user->id,
                                'first_name' => $validated['first_name'],
                                'last_name' => $validated['last_name'],
                                'middle_name' => $validated['middle_name'] ?? null,
                                'email' => $validated['email'],
                                'phone' => $validated['phone'] ?? null,
                                'position' => 'O\'qituvchi',
                            ]);
                        }
                    } elseif ($validated['employee_type'] === 'admin') {
                        if (!$user->hasRole('Admin')) {
                            $user->assignRole('Admin');
                        }
                    }

                    // Link user to employee
                    $employee->user_id = $user->id;
                }
            }

            // Track if new user was created
            $newUserCreated = isset($user);
            $newUserId = $newUserCreated ? $user->id : null;

            // Remove password fields from employee data (password is stored in users table)
            unset($validated['password']);
            unset($validated['password_confirmation']);

            // Separate employee data from employment detail data
            $employmentFields = ['position_id', 'department_id', 'faculty_id', 'stavka', 'contract_type', 'hire_date', 'employment_type'];

            $employeeData = collect($validated)
                ->except(array_merge(['photo', 'password', 'password_confirmation'], $employmentFields))
                ->toArray();

            // Include user_id if a new user was created
            if ($newUserId) {
                $employeeData['user_id'] = $newUserId;
            }

            $employee->update($employeeData);

            // Update or create employment detail
            $employmentData = collect($validated)->only($employmentFields)->toArray();
            if (!empty(array_filter($employmentData))) {
                if ($employee->employmentDetail) {
                    $employee->employmentDetail->update($employmentData);
                } else {
                    $employmentData['employee_id'] = $employee->id;
                    EmploymentDetail::create($employmentData);
                }
            }

            // Refresh employee to get latest relationships
            $employee->refresh();

            // SECURITY FIX: Update user BEFORE commit (BUG #27)
            if ($employee->user && !$newUserCreated) {
                $employee->user->update([
                    'name' => $employeeData['name'] ?? $employee->name,
                    'email' => $employeeData['email'] ?? $employee->email,
                ]);
            }

            DB::commit();

            // Determine success message
            $successMessage = 'Xodim ma\'lumotlari muvaffaqiyatli yangilandi!';
            if ($newUserCreated) {
                $successMessage = 'Xodim ma\'lumotlari yangilandi va yangi tizim akkaunti yaratildi! Login: ' . $employee->email;
            }

            return redirect()->route('employees.show', $employee)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete newly uploaded photo if exists
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return back()->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function destroy(Employee $employee)
    {
        // SECURITY FIX: Check dependencies before termination (BUG #28)
        try {
            // Check if employee has active LMS exams
            if (\Schema::hasTable('lms_exams')) {
                $activeExams = \DB::table('lms_exams')
                    ->where('teacher_id', $employee->id)
                    ->where('status', 'active')
                    ->count();

                if ($activeExams > 0) {
                    return back()->with('error', 'Bu xodimning faol imtihonlari mavjud. Avval ularni tugatib qo\'ying!');
                }
            }

            // Check if employee has active groups as teacher
            if (\Schema::hasTable('groups')) {
                $activeGroups = \DB::table('groups')
                    ->where('teacher_id', $employee->id)
                    ->where('is_active', true)
                    ->count();

                if ($activeGroups > 0) {
                    return back()->with('error', 'Bu xodimning faol guruhlari mavjud. Avval guruhlar tayinini o\'zgartiring!');
                }
            }

            // Check if employee has active journal entries
            if (\Schema::hasTable('journal_attendances')) {
                $recentAttendances = \DB::table('journal_attendances')
                    ->where('teacher_id', $employee->id)
                    ->where('created_at', '>=', now()->subMonths(3))
                    ->count();

                if ($recentAttendances > 0) {
                    return back()->with('error', 'Bu xodimning oxirgi 3 oyda jurnaldagi yozuvlari mavjud. O\'chirishga ruxsat berilmadi!');
                }
            }

            // Soft delete by changing status
            $employee->update(['status' => 'terminated']);

            \Log::info('Employee terminated', [
                'employee_id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'terminated_by' => auth()->id()
            ]);

            return redirect()->route('employees.index')
                ->with('success', 'Xodim muvaffaqiyatli tugatildi!');

        } catch (\Exception $e) {
            \Log::error('Error terminating employee: ' . $e->getMessage());
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}