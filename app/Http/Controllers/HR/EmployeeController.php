<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'department'])->paginate(20);
        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('hr.employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // Faqat ism va familiya majburiy
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'employee_type' => 'nullable|string|in:staff,teacher,admin',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'address' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Full name yaratish
            $fullName = trim($validated['last_name'] . ' ' . $validated['first_name'] . ' ' . ($validated['middle_name'] ?? ''));

            // Auto-generate email if not provided
            $email = $validated['email'] ?? null;
            if (!$email) {
                $baseEmail = Str::slug($validated['first_name'] . '.' . $validated['last_name'], '.') . '@tas.uz';
                $email = $baseEmail;
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = Str::slug($validated['first_name'] . '.' . $validated['last_name'], '.') . $counter . '@tas.uz';
                    $counter++;
                }
            }

            // Auto-generate password
            $password = Str::random(10);

            // Create user
            $user = User::create([
                'name' => $fullName,
                'email' => $email,
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($password),
                'employee_type' => $validated['employee_type'] ?? 'staff',
            ]);

            // Assign role based on employee type
            $employeeType = $validated['employee_type'] ?? 'staff';
            if ($employeeType === 'teacher') {
                $user->assignRole('Teacher');
            } elseif ($employeeType === 'admin') {
                $user->assignRole('Admin');
            } else {
                $user->assignRole('Employee');
            }

            // Create employee record
            $employee = Employee::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'full_name' => $fullName,
                'department_id' => $validated['department_id'] ?? null,
                'position' => $validated['position'] ?? null,
                'hire_date' => $validated['hire_date'] ?? now(),
                'birth_date' => $validated['birth_date'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => 'active',
            ]);

            DB::commit();

            // Log credentials
            \Log::info('New employee created', [
                'employee_id' => $employee->id,
                'email' => $email,
                'password' => $password
            ]);

            return redirect()->route('hr.employees.index')
                ->with('success', "Xodim muvaffaqiyatli qo'shildi! Login: {$email}");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Employee creation error: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
        ]);

        $fullName = $validated['last_name'] . ' ' . $validated['first_name'];
        $email = Str::slug($validated['first_name'] . '.' . $validated['last_name'], '.') . '@tas.uz';

        // Ensure unique email
        $counter = 1;
        $baseEmail = $email;
        while (User::where('email', $email)->exists()) {
            $email = str_replace('@', $counter . '@', $baseEmail);
            $counter++;
        }

        $user = User::create([
            'name' => $fullName,
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'full_name' => $fullName,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'employee' => $employee,
            'email' => $email,
        ]);
    }

    public function show($id)
    {
        $employee = Employee::with(['user', 'department'])->findOrFail($id);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = Employee::with(['user', 'department'])->findOrFail($id);
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('hr.employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|unique:users,email,' . $employee->user_id,
            'phone' => 'nullable|string|max:20',
            'employee_type' => 'nullable|string|in:staff,teacher,admin',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'hire_date' => 'nullable|date',
            'address' => 'nullable|string|max:500',
        ]);

        $fullName = trim($validated['last_name'] . ' ' . $validated['first_name'] . ' ' . ($validated['middle_name'] ?? ''));

        // Update user
        if ($employee->user) {
            $employee->user->update([
                'name' => $fullName,
                'email' => $validated['email'] ?? $employee->user->email,
                'phone' => $validated['phone'] ?? null,
                'employee_type' => $validated['employee_type'] ?? 'staff',
            ]);
        }

        // Update employee
        $employee->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'full_name' => $fullName,
            'department_id' => $validated['department_id'] ?? null,
            'position' => $validated['position'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Xodim ma\'lumotlari yangilandi');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee->user) {
            $employee->user->delete();
        }
        $employee->delete();

        return redirect()->route('hr.employees.index')
            ->with('success', 'Xodim o\'chirildi');
    }

    public function exportExcel()
    {
        // TODO: Implement Excel export functionality
        return response()->download(storage_path('app/employees.xlsx'));
    }

    public function import(Request $request)
    {
        // TODO: Implement import functionality
        return redirect()->route('hr.employees.index')
            ->with('success', 'Import muvaffaqiyatli amalga oshirildi');
    }
}
