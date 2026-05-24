<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimpleStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->join('faculties', 'students.faculty_id', '=', 'faculties.id')
            ->join('specialties', 'students.specialty_id', '=', 'specialties.id')
            ->select(
                'students.*',
                'users.name as user_name',
                'users.email as user_email',
                'faculties.name_uz as faculty_name',
                'specialties.name_uz as specialty_name'
            );

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('students.student_id', 'like', "%{$search}%")
                  ->orWhere('students.first_name', 'like', "%{$search}%")
                  ->orWhere('students.last_name', 'like', "%{$search}%")
                  ->orWhere('students.phone', 'like', "%{$search}%")
                  ->orWhere('students.email', 'like', "%{$search}%");
            });
        }

        // Faculty filter
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->where('students.faculty_id', $request->faculty_id);
        }

        // Specialty filter
        if ($request->has('specialty_id') && $request->specialty_id) {
            $query->where('students.specialty_id', $request->specialty_id);
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('students.status', $request->status);
        }

        // Course filter
        if ($request->has('course') && $request->course) {
            $query->where('students.course', $request->course);
        }

        // Education form filter
        if ($request->has('education_form') && $request->education_form) {
            $query->where('students.education_form', $request->education_form);
        }

        // Education type filter
        if ($request->has('education_type') && $request->education_type) {
            $query->where('students.education_type', $request->education_type);
        }

        $students = $query->orderBy('students.created_at', 'desc')->paginate(20);

        // Get faculties and specialties for filters
        $faculties = DB::table('faculties')->select('id', 'name_uz as name')->get();
        $specialties = DB::table('specialties')->select('id', 'name_uz as name')->get();
        $groups = DB::table('academic_groups')->select('id', 'name')->get();

        // Get statistics
        $statistics = [
            'total' => DB::table('students')->count(),
            'active' => DB::table('students')->where('status', 'active')->count(),
            'male' => DB::table('students')->where('gender', 'erkak')->count(),
            'female' => DB::table('students')->where('gender', 'ayol')->count(),
        ];

        return view('student-contingent.students.index', compact(
            'students',
            'faculties',
            'specialties',
            'groups',
            'statistics'
        ));
    }

    public function create()
    {
        $faculties = DB::table('faculties')->select('id', 'name_uz as name')->get();
        $specialties = DB::table('specialties')->select('id', 'name_uz as name')->get();
        $groups = DB::table('academic_groups')->select('id', 'name')->get();
        
        return view('student-contingent.students.create', compact('faculties', 'specialties', 'groups'));
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'student_id' => 'required|unique:students',
            'first_name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'gender' => 'required|in:erkak,ayol',
            'phone' => 'required',
            'faculty_id' => 'required|exists:faculties,id',
            'specialty_id' => 'required|exists:specialties,id',
            'course' => 'required|integer|between:1,6',
            'education_form' => 'required|in:kunduzgi,sirtqi,kechki,masofaviy',
            'education_type' => 'required|in:byudjet,shartnoma',
            'admission_year' => 'required|integer',
            'admission_date' => 'required|date',
        ]);

        // Create user account
        $user = \App\Models\User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email ?? $request->student_id . '@student.uz',
            'password' => bcrypt($request->password ?? 'password'),
            'phone' => $request->phone,
        ]);
        $user->assignRole('Student');

        // Create student record
        DB::table('students')->insert([
            'user_id' => $user->id,
            'student_id' => $request->student_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'nationality' => $request->nationality ?? 'uzbek',
            'citizenship' => $request->citizenship ?? 'uzbekistan',
            'passport_series' => $request->passport_series,
            'passport_number' => $request->passport_number,
            'pinfl' => $request->pinfl,
            'phone' => $request->phone,
            'parent_phone' => $request->parent_phone,
            'email' => $request->email,
            'permanent_address' => $request->permanent_address,
            'temporary_address' => $request->temporary_address,
            'faculty_id' => $request->faculty_id,
            'specialty_id' => $request->specialty_id,
            'course' => $request->course,
            'semester' => $request->course * 2 - 1,
            'education_form' => $request->education_form,
            'education_type' => $request->education_type,
            'education_language' => $request->education_language ?? 'uz',
            'admission_year' => $request->admission_year,
            'admission_date' => $request->admission_date,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('students.index')->with('success', 'Talaba muvaffaqiyatli qo\'shildi!');
    }

    public function show($id)
    {
        $student = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->join('faculties', 'students.faculty_id', '=', 'faculties.id')
            ->join('specialties', 'students.specialty_id', '=', 'specialties.id')
            ->select(
                'students.*',
                'users.name as user_name',
                'users.email as user_email',
                'faculties.name_uz as faculty_name',
                'specialties.name_uz as specialty_name'
            )
            ->where('students.id', $id)
            ->first();

        if (!$student) {
            abort(404);
        }

        return view('student-contingent.students.show', compact('student'));
    }

    public function edit($id)
    {
        $student = DB::table('students')->where('id', $id)->first();
        
        if (!$student) {
            abort(404);
        }

        $faculties = DB::table('faculties')->select('id', 'name_uz as name')->get();
        $specialties = DB::table('specialties')->select('id', 'name_uz as name')->get();
        $groups = DB::table('academic_groups')->select('id', 'name')->get();
        
        return view('student-contingent.students.edit', compact('student', 'faculties', 'specialties', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $student = DB::table('students')->where('id', $id)->first();
        
        if (!$student) {
            abort(404);
        }

        // Validation
        $request->validate([
            'student_id' => 'required|unique:students,student_id,' . $id,
            'first_name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'gender' => 'required|in:erkak,ayol',
            'phone' => 'required',
            'faculty_id' => 'required|exists:faculties,id',
            'specialty_id' => 'required|exists:specialties,id',
            'course' => 'required|integer|between:1,6',
            'education_form' => 'required|in:kunduzgi,sirtqi,kechki,masofaviy',
            'education_type' => 'required|in:byudjet,shartnoma',
        ]);

        // Update student record
        DB::table('students')->where('id', $id)->update([
            'student_id' => $request->student_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'nationality' => $request->nationality ?? 'uzbek',
            'citizenship' => $request->citizenship ?? 'uzbekistan',
            'passport_series' => $request->passport_series,
            'passport_number' => $request->passport_number,
            'pinfl' => $request->pinfl,
            'phone' => $request->phone,
            'parent_phone' => $request->parent_phone,
            'email' => $request->email,
            'permanent_address' => $request->permanent_address,
            'temporary_address' => $request->temporary_address,
            'faculty_id' => $request->faculty_id,
            'specialty_id' => $request->specialty_id,
            'course' => $request->course,
            'semester' => $request->course * 2 - 1,
            'education_form' => $request->education_form,
            'education_type' => $request->education_type,
            'education_language' => $request->education_language ?? 'uz',
            'status' => $request->status ?? 'active',
            'updated_at' => now(),
        ]);

        // Update user if needed
        if ($student->user_id) {
            \App\Models\User::where('id', $student->user_id)->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'phone' => $request->phone,
            ]);
        }

        return redirect()->route('students.index')->with('success', 'Talaba ma\'lumotlari yangilandi!');
    }

    public function destroy($id)
    {
        $student = DB::table('students')->where('id', $id)->first();
        
        if (!$student) {
            abort(404);
        }

        // Delete student
        DB::table('students')->where('id', $id)->delete();

        // Optionally delete user account
        if ($student->user_id) {
            \App\Models\User::where('id', $student->user_id)->delete();
        }

        return redirect()->route('students.index')->with('success', 'Talaba o\'chirildi!');
    }
}