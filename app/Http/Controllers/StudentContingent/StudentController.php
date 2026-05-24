<?php

namespace App\Http\Controllers\StudentContingent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Group;
use App\Models\Faculty;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class StudentController extends Controller
{
    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'group', 'faculty', 'specialty']);

        // Default: Show only active students if no status filter applied
        if (!$request->has('status') || $request->status === '') {
            $query->where('status', 'active');
        } elseif ($request->status && $request->status !== 'all') {
            // Filter by specific status (active, graduated, expelled)
            $query->where('status', $request->status);
        }
        // If status is 'all', don't filter by status (show all students)

        // Filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('group_id') && $request->group_id) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->has('course') && $request->course) {
            $query->where('course', $request->course);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->query());

        // Get face registration status for each student
        $studentIds = $students->pluck('id')->toArray();
        $faceRegistrations = DB::table('face_encodings')
            ->whereIn('student_id', $studentIds)
            ->where('is_active', true)
            ->pluck('student_id')
            ->toArray();

        // Add face registration status to each student
        foreach ($students as $student) {
            $student->has_face_registered = in_array($student->id, $faceRegistrations);
        }

        // Get filter options
        $groups = Group::where('is_active', true)->where('name', 'NOT LIKE', 'Test%')->orderBy('name')->get();
        $faculties = Faculty::where('is_active', true)->get();
        $specialties = Specialty::where('is_active', true)->get();

        return view('student-contingent.students.index', compact(
            'students',
            'groups',
            'faculties',
            'specialties'
        ));
    }

    /**
     * Show the form for creating a new student (Registration Wizard)
     */
    public function create()
    {
        $faculties = Faculty::with('specialties')->where('is_active', true)->get();
        $groups = Group::with(['specialty.faculty'])
            ->where('is_active', true)
            ->where('name', 'NOT LIKE', 'Test%')
            ->orderBy('name')
            ->get();

        return view('student-contingent.students.create', compact('faculties', 'groups'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic Information - faqat ism va familiya majburiy
            'first_name_latin' => 'required|string|max:50',
            'last_name_latin' => 'required|string|max:50',
            'middle_name_latin' => 'nullable|string|max:50',
            'group_id' => 'required|exists:groups,id',

            // Boshqa maydonlar ixtiyoriy - IMPROVED VALIDATION
            'jshshir' => [
                'nullable',
                'string',
                'size:14',
                'regex:/^[0-9]{14}$/',
                'unique:students,jshshir'
            ],
            'birth_date' => [
                'nullable',
                'date',
                'before:today',
                'after:' . now()->subYears(100)->format('Y-m-d')
            ],
            'gender' => 'nullable|in:male,female,erkak,ayol',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,phone',
                'unique:students,phone'
            ],
            'email' => [
                'nullable',
                'email',
                'unique:students,email',
                'unique:users,email'
            ],
            'passport_series' => 'nullable|string|max:2',
            'passport_number' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->passport_series) {
                        $exists = Student::where('passport_series', $request->passport_series)
                            ->where('passport_number', $value)
                            ->exists();
                        if ($exists) {
                            $fail('Bu passport raqami allaqachon ro\'yxatdan o\'tgan.');
                        }
                    }
                }
            ],
            'permanent_address' => 'nullable|string|max:500',
            'temporary_address' => 'nullable|string|max:500',
            'faculty_id' => 'nullable|exists:faculties,id',
            'specialty_id' => 'nullable|exists:specialties,id',
            'course' => 'nullable|integer|min:1|max:6',
            'education_form' => 'nullable|in:kunduzgi,kechki,sirtqi',
            'education_type' => 'nullable|in:grant,contract,super_contract,foreign_contract',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Get group data to fill faculty and specialty
            $group = Group::with(['specialty.faculty'])->find($validated['group_id']);

            // Transform Uzbek gender values to English for database
            if (isset($validated['gender'])) {
                $genderMap = ['erkak' => 'male', 'ayol' => 'female'];
                if (array_key_exists($validated['gender'], $genderMap)) {
                    $validated['gender'] = $genderMap[$validated['gender']];
                }
            }

            // FIXED: Thread-safe student ID generation with retry mechanism
            $studentId = null;
            $maxRetries = 5;
            for ($i = 0; $i < $maxRetries; $i++) {
                // Use lockForUpdate to prevent race conditions
                $lastStudent = Student::lockForUpdate()->orderBy('id', 'desc')->first();
                $nextNumber = $lastStudent ? ($lastStudent->id + 1) : 1;
                $candidateId = 'EXA-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // Check if ID already exists
                if (!Student::where('student_id', $candidateId)->exists()) {
                    $studentId = $candidateId;
                    break;
                }

                // If exists, try with random suffix
                $studentId = 'EXA-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT) . '-' . rand(10, 99);
                if (!Student::where('student_id', $studentId)->exists()) {
                    break;
                }
            }

            if (!$studentId) {
                throw new \Exception('Student ID yaratishda xatolik yuz berdi. Qaytadan urinib ko\'ring.');
            }

            // Create full name
            $fullName = trim($validated['last_name_latin'] . ' ' . $validated['first_name_latin'] . ' ' . ($validated['middle_name_latin'] ?? ''));

            // FIXED: Upload photo BEFORE creating student
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                if ($photo->isValid()) {
                    $photoPath = $photo->store('students/photos', 'public');
                } else {
                    throw new \Exception('Rasm yuklashda xatolik: ' . $photo->getErrorMessage());
                }
            }

            // Create student with photo
            $studentData = [
                'student_id' => $studentId,
                'first_name' => $validated['first_name_latin'],
                'last_name' => $validated['last_name_latin'],
                'middle_name' => $validated['middle_name_latin'] ?? null,
                'full_name' => $fullName,
                'group_id' => $validated['group_id'],
                'faculty_id' => $validated['faculty_id'] ?? ($group->specialty->faculty->id ?? null),
                'specialty_id' => $validated['specialty_id'] ?? ($group->specialty_id ?? null),
                'course' => $validated['course'] ?? ($group->course ?? 1),
                'status' => 'active',
                'admission_date' => now(),
                'birth_date' => $validated['birth_date'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'passport_series' => $validated['passport_series'] ?? null,
                'passport_number' => $validated['passport_number'] ?? null,
                'address' => $validated['permanent_address'] ?? null,
                'temporary_address' => $validated['temporary_address'] ?? null,
                'education_form' => $validated['education_form'] ?? null,
                'education_type' => $validated['education_type'] ?? null,
                'photo_url' => $photoPath,
                'profile_completed' => false,
            ];

            $student = Student::create($studentData);

            // Create user account for student
            $loginEmail = $request->input('login_email');
            if (empty($loginEmail)) {
                $loginEmail = $studentId . '@tourism.uz';
            }

            // FIXED: Generate strong random password
            $password = $request->input('password');
            if (empty($password)) {
                $password = \Illuminate\Support\Str::random(12);
            }

            // FIXED: Proper email uniqueness handling with retry
            $finalEmail = $loginEmail;
            $emailRetries = 10;
            for ($j = 0; $j < $emailRetries; $j++) {
                if (!\App\Models\User::where('email', $finalEmail)->exists()) {
                    break;
                }
                // Add timestamp-based suffix for uniqueness
                $parts = explode('@', $loginEmail);
                $finalEmail = $parts[0] . '-' . time() . rand(100, 999) . '@' . ($parts[1] ?? 'tourism.uz');
            }

            // Double-check email is unique before creating
            if (\App\Models\User::where('email', $finalEmail)->exists()) {
                throw new \Exception('Login email yaratishda xatolik. Qaytadan urinib ko\'ring.');
            }

            $user = \App\Models\User::create([
                'name' => $fullName,
                'email' => $finalEmail,
                'password' => $password,
                'phone' => $validated['phone'] ?? null,
            ]);

            $user->assignRole('Student');
            $student->update(['user_id' => $user->id]);

            DB::commit();

            // Log the generated credentials for admin
            \Log::info('New student created', [
                'student_id' => $studentId,
                'email' => $finalEmail,
                'password' => $password
            ]);

            return redirect()->route('students.index')
                ->with('success', 'Talaba muvaffaqiyatli qo\'shildi! Login: ' . $finalEmail);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Check if it's a duplicate entry error
            if ($e->getCode() == 23000) {
                $errorMessage = $e->getMessage();

                // Parse which field is duplicated
                if (strpos($errorMessage, 'users_phone_unique') !== false) {
                    return back()->withInput()
                        ->withErrors(['phone' => 'Bu telefon raqam allaqachon ro\'yxatdan o\'tgan.']);
                } elseif (strpos($errorMessage, 'users_email_unique') !== false || strpos($errorMessage, 'email') !== false) {
                    return back()->withInput()
                        ->withErrors(['email' => 'Bu email manzil allaqachon ro\'yxatdan o\'tgan.']);
                } else {
                    return back()->withInput()
                        ->withErrors(['error' => 'Bu ma\'lumotlar allaqachon ishlatilgan.']);
                }
            }

            return back()->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified student
     */
    public function show(Student $student)
    {
        $student->load([
            'user',
            'group',
            'faculty',
            'specialty'
        ]);

        return view('student-contingent.students.show', compact('student'));
    }

    /**
     * Show the form for editing the student
     */
    public function edit(Student $student)
    {
        $student->load([
            'user',
            'group',
            'faculty',
            'specialty'
        ]);

        $faculties = Faculty::with('specialties')->where('is_active', true)->get();
        $specialties = Specialty::where('is_active', true)->get();
        $groups = Group::where('is_active', true)->where('name', 'NOT LIKE', 'Test%')->orderBy('name')->get();

        return view('student-contingent.students.edit', compact('student', 'faculties', 'specialties', 'groups'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_id' => 'nullable|string|max:50',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,erkak,ayol',
            'passport_series' => 'nullable|string|max:2',
            'passport_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'parent_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:students,email,' . $student->id,
            'permanent_address' => 'nullable|string|max:255',
            'temporary_address' => 'nullable|string|max:255',
            'faculty_id' => 'nullable|exists:faculties,id',
            'specialty_id' => 'nullable|exists:specialties,id',
            'group_id' => 'nullable|exists:groups,id',
            'course' => 'nullable|integer|min:1|max:6',
            'education_form' => 'nullable|in:kunduzgi,sirtqi,kechki,masofaviy',
            'education_type' => 'nullable|in:byudjet,shartnoma',
            'status' => 'nullable|in:active,inactive,graduated,expelled,academic_leave',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Transform Uzbek gender values to English for database
        if (isset($validated['gender'])) {
            $genderMap = ['erkak' => 'male', 'ayol' => 'female'];
            if (array_key_exists($validated['gender'], $genderMap)) {
                $validated['gender'] = $genderMap[$validated['gender']];
            }
        }

        // Map permanent_address to address field
        if (isset($validated['permanent_address'])) {
            $validated['address'] = $validated['permanent_address'];
            unset($validated['permanent_address']);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');

            if ($photo->isValid()) {
                // Delete old photo if exists
                if ($student->photo_url && Storage::disk('public')->exists($student->photo_url)) {
                    Storage::disk('public')->delete($student->photo_url);
                }

                // Store new photo
                $path = $photo->store('students/photos', 'public');
                $validated['photo_url'] = $path;
            } else {
                \Log::warning('Photo upload failed during update', [
                    'student_id' => $student->id,
                    'error' => $photo->getErrorMessage()
                ]);
            }
        }

        // Update full_name if name parts changed
        if (isset($validated['first_name']) || isset($validated['last_name']) || isset($validated['middle_name'])) {
            $lastName = $validated['last_name'] ?? $student->last_name;
            $firstName = $validated['first_name'] ?? $student->first_name;
            $middleName = $validated['middle_name'] ?? $student->middle_name;
            $validated['full_name'] = trim("$lastName $firstName $middleName");
        }

        $student->update($validated);

        // Handle user account updates (login/password)
        if ($student->user) {
            $userUpdates = [];

            // Update login (email) if provided
            if ($request->filled('new_login')) {
                $newLogin = $request->input('new_login');
                // Check if email is unique
                $existingUser = \App\Models\User::where('email', $newLogin)
                    ->where('id', '!=', $student->user->id)
                    ->first();
                if (!$existingUser) {
                    $userUpdates['email'] = $newLogin;
                }
            }

            // Update password if provided
            if ($request->filled('new_password')) {
                $userUpdates['password'] = $request->input('new_password'); // Laravel auto-hashes via 'hashed' cast
            }

            if (!empty($userUpdates)) {
                $student->user->update($userUpdates);
            }
        } else {
            // Create new user if requested
            if ($request->filled('create_user_email')) {
                $email = $request->input('create_user_email');
                $password = $request->input('create_user_password', 'password123');

                // Check if email is unique
                $existingUser = \App\Models\User::where('email', $email)->first();
                if (!$existingUser) {
                    $user = \App\Models\User::create([
                        'name' => $student->full_name,
                        'email' => $email,
                        'password' => $password, // Laravel auto-hashes via 'hashed' cast
                        'phone' => $student->phone,
                    ]);
                    $user->assignRole('Student');
                    $student->update(['user_id' => $user->id]);
                }
            }
        }

        return redirect()->route('students.show', $student)
            ->with('success', 'Talaba ma\'lumotlari yangilandi!');
    }

    /**
     * Remove the specified student (soft delete)
     */
    public function destroy(Student $student)
    {
        $student->update(['status' => 'expelled']);

        return redirect()->route('students.index')
            ->with('success', 'Talaba chetlashtirildi!');
    }

    /**
     * Permanently delete the specified student from database
     */
    public function forceDelete($id)
    {
        $student = Student::findOrFail($id);

        // Faqat chetlashtirilgan talabalarni o'chirish mumkin
        if ($student->status !== 'expelled') {
            return redirect()->back()
                ->with('error', 'Faqat chetlashtirilgan talabalarni butunlay o\'chirish mumkin!');
        }

        // FIXED: Wrap in transaction for data integrity
        DB::beginTransaction();

        try {
            // Delete associated user account first
            if ($student->user_id) {
                $user = $student->user;
                if ($user) {
                    // Delete user's related data
                    $user->tokens()->delete(); // Delete API tokens if any
                    $user->notifications()->delete(); // Delete notifications
                    $user->delete();
                }
            }

            // Delete student's related data
            $student->faceEncoding()->delete(); // Delete face recognition data if exists

            // Delete the student record
            $student->delete();

            DB::commit();

            \Log::info('Student permanently deleted', [
                'student_id' => $student->student_id,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('students.index')
                ->with('success', 'Talaba tizimdan butunlay o\'chirildi!');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Failed to delete student', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Talabani o\'chirishda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Export students to Excel
     */
    public function export(Request $request)
    {
        // Implementation will use Maatwebsite\Excel package
        return Excel::download(new StudentsExport($request->all()), 'talabalar_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Import students from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);
        
        try {
            Excel::import(new StudentsImport, $request->file('file'));
            
            return redirect()->route('students.index')
                ->with('success', 'Talabalar muvaffaqiyatli import qilindi!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import xatoligi: ' . $e->getMessage());
        }
    }

    /**
     * Generate student ID card
     */
    public function generateIdCard(Student $student)
    {
        // Load necessary relationships
        $student->load([
            'user',
            'group',
            'faculty',
            'specialty'
        ]);

        // If PDF download is requested
        if (request()->has('download')) {
            $pdf = \PDF::loadView('student-contingent.students.id-card', compact('student'));
            $pdf->setPaper([0, 0, 350, 220], 'landscape'); // Custom size for ID card
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);
            return $pdf->download('id-card-' . $student->student_id . '.pdf');
        }

        return view('student-contingent.students.id-card', compact('student'));
    }

    /**
     * Show student statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Student::count(),
            'active' => Student::where('status', 'active')->count(),
            'graduated' => Student::where('status', 'graduated')->count(),
            'expelled' => Student::where('status', 'expelled')->count(),
            'academic_leave' => Student::where('status', 'academic_leave')->count(),
            
            'by_gender' => Student::select('gender', DB::raw('count(*) as count'))
                ->groupBy('gender')->get(),
                
            'by_education_form' => DB::table('student_enrollments')
                ->select('education_form', DB::raw('count(*) as count'))
                ->where('is_active', true)
                ->groupBy('education_form')->get(),
                
            'by_education_type' => DB::table('student_enrollments')
                ->select('education_type', DB::raw('count(*) as count'))
                ->where('is_active', true)
                ->groupBy('education_type')->get(),
                
            'by_course' => DB::table('student_enrollments')
                ->select('current_course', DB::raw('count(*) as count'))
                ->where('is_active', true)
                ->groupBy('current_course')->get(),
        ];
        
        return view('student-contingent.students.statistics', compact('stats'));
    }
}