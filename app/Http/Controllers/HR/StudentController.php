<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'group'])->paginate(20);
        return view('hr.students.index', compact('students'));
    }

    public function create()
    {
        return view('hr.students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|unique:users,phone',
            'student_id' => 'required|string|unique:students',
        ]);

        try {
            // Create user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make('password123'),
                'user_type' => 'student',
            ]);

            // Assign student role
            $user->assignRole('Student');

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'student_id' => $validated['student_id'],
            ]);

            return redirect()->route('hr.students.index')
                ->with('success', 'Talaba muvaffaqiyatli qo\'shildi');

        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a duplicate entry error
            if ($e->getCode() == 23000) {
                $errorMessage = $e->getMessage();

                if (strpos($errorMessage, 'users_phone_unique') !== false) {
                    return back()->withInput()
                        ->withErrors(['phone' => 'Bu telefon raqam allaqachon ro\'yxatdan o\'tgan.']);
                } elseif (strpos($errorMessage, 'users_email_unique') !== false) {
                    return back()->withInput()
                        ->withErrors(['email' => 'Bu email manzil allaqachon ro\'yxatdan o\'tgan.']);
                }
            }

            return back()->withInput()
                ->withErrors(['error' => 'Xatolik yuz berdi: ' . $e->getMessage()]);
        }
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|unique:users,phone',
            'student_id' => 'required|string|unique:students',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make('password123'),
                'user_type' => 'student',
            ]);

            $user->assignRole('Student');

            $student = Student::create([
                'user_id' => $user->id,
                'student_id' => $validated['student_id'],
            ]);

            return response()->json([
                'success' => true,
                'student' => $student,
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a duplicate entry error
            if ($e->getCode() == 23000) {
                $errorMessage = $e->getMessage();

                if (strpos($errorMessage, 'users_phone_unique') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bu telefon raqam allaqachon ro\'yxatdan o\'tgan.'
                    ], 422);
                } elseif (strpos($errorMessage, 'users_email_unique') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bu email manzil allaqachon ro\'yxatdan o\'tgan.'
                    ], 422);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $student = Student::with(['user', 'group'])->findOrFail($id);
        return view('hr.students.show', compact('student'));
    }

    public function edit($id)
    {
        $student = Student::with(['user'])->findOrFail($id);
        return view('hr.students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'phone' => 'nullable|string|unique:users,phone,' . $student->user_id,
            'student_id' => 'required|string|unique:students,student_id,' . $id,
        ]);

        try {
            $student->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);

            $student->update([
                'student_id' => $validated['student_id'],
            ]);

            return redirect()->route('hr.students.index')
                ->with('success', 'Talaba ma\'lumotlari yangilandi');

        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a duplicate entry error
            if ($e->getCode() == 23000) {
                $errorMessage = $e->getMessage();

                if (strpos($errorMessage, 'users_phone_unique') !== false) {
                    return back()->withInput()
                        ->withErrors(['phone' => 'Bu telefon raqam allaqachon ro\'yxatdan o\'tgan.']);
                } elseif (strpos($errorMessage, 'users_email_unique') !== false) {
                    return back()->withInput()
                        ->withErrors(['email' => 'Bu email manzil allaqachon ro\'yxatdan o\'tgan.']);
                }
            }

            return back()->withInput()
                ->withErrors(['error' => 'Xatolik yuz berdi: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->user->delete();
        $student->delete();

        return redirect()->route('hr.students.index')
            ->with('success', 'Talaba o\'chirildi');
    }

    public function exportExcel()
    {
        // TODO: Implement Excel export functionality
        return response()->download(storage_path('app/students.xlsx'));
    }

    public function import(Request $request)
    {
        // TODO: Implement import functionality
        return redirect()->route('hr.students.index')
            ->with('success', 'Import muvaffaqiyatli amalga oshirildi');
    }
}
