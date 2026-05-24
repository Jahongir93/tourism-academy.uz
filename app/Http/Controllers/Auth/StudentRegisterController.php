<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class StudentRegisterController extends Controller
{
    /**
     * Talabalar uchun soddalashtirilgan ro'yxatga olish formasi
     */
    public function showRegistrationForm()
    {
        return view('auth.student-register');
    }

    /**
     * Talabani ro'yxatdan o'tkazish
     * SECURITY FIX: Rate limiting applied via middleware in routes (BUG #49)
     */
    public function register(Request $request)
    {
        // SECURITY FIX: Enhanced validation (BUG #48, #50)
        $request->validate([
            'full_name' => 'required|string|max:255|min:3',
            // SECURITY FIX: Phone unique validation (BUG #48)
            'phone' => [
                'nullable',
                'string',
                'regex:/^[+]998[0-9]{9}$/',
                'unique:users,phone',
                'unique:students,phone',
            ],
            // SECURITY FIX: Stronger password requirement (BUG #50)
            'password' => 'required|string|min:8|confirmed',
        ], [
            'full_name.required' => 'Ism va familiyangizni kiriting',
            'full_name.min' => 'Ism va familiya kamida 3 ta harfdan iborat bo\'lishi kerak',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
            'phone.regex' => 'Telefon raqam formati noto\'g\'ri (+998XXXXXXXXX)',
            'phone.unique' => 'Bu telefon raqam allaqachon ro\'yxatdan o\'tgan',
        ]);

        DB::beginTransaction();

        try {
            // SECURITY FIX: Thread-safe student ID generation (BUG #47)
            $studentId = $this->generateStudentId();

            // Full name dan first_name va last_name ajratish
            $nameParts = explode(' ', trim($request->full_name), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            // Foydalanuvchi yaratish
            $userData = [
                'name' => $request->full_name,
                'email' => $studentId . '@student.uz',
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'is_profile_complete' => false,
            ];
            if (\Schema::hasColumn('users', 'user_type')) {
                $userData['user_type'] = 'uzbek';
            }
            if (\Schema::hasColumn('users', 'status')) {
                $userData['status'] = 'active';
            }
            $user = User::create($userData);

            // SECURITY FIX: Thread-safe role creation (BUG #51)
            $studentRole = Role::lockForUpdate()->where('name', 'Student')->first();
            if (!$studentRole) {
                $studentRole = Role::create(['name' => 'Student']);
            }
            $user->assignRole($studentRole);

            // Student jadvliga yozish
            Student::create([
                'user_id' => $user->id,
                'student_id' => $studentId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $request->full_name,
                'email' => $studentId . '@student.uz',
                'phone' => $request->phone,
                'group_id' => null,
                'faculty_id' => null,
                'specialty_id' => null,
                'registration_date' => now(),
                'status' => 'active',
                'profile_completed' => false,
            ]);

            DB::commit();

            // Avtomatik tizimga kirish
            Auth::login($user);

            // Profilni to'ldirish sahifasiga yo'naltirish
            return redirect()->route('student.complete-profile')
                ->with('success', 'Ro\'yxatdan muvaffaqiyatli o\'tdingiz! Endi profilingizni to\'ldiring.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Student registration error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except('password', 'password_confirmation')
            ]);
            return back()->withErrors(['error' => 'Ro\'yxatdan o\'tishda xatolik yuz berdi: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Talaba ID generatsiya qilish
     * SECURITY FIX: Thread-safe with database locking (BUG #47)
     */
    private function generateStudentId()
    {
        $year = date('Y');

        // SECURITY FIX: Use lockForUpdate() to prevent race conditions
        $lastStudent = Student::where('student_id', 'like', $year . '%')
                              ->orderBy('student_id', 'desc')
                              ->lockForUpdate()
                              ->first();

        if ($lastStudent) {
            $lastNumber = intval(substr($lastStudent->student_id, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $studentId = $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Double check uniqueness
        $maxRetries = 5;
        for ($i = 0; $i < $maxRetries; $i++) {
            if (!Student::where('student_id', $studentId)->exists()) {
                return $studentId;
            }
            // If duplicate, increment and try again
            $newNumber++;
            $studentId = $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }

        throw new \Exception('Student ID generatsiya qilishda xatolik yuz berdi. Qaytadan urinib ko\'ring.');
    }

    /**
     * Profilni to'ldirish sahifasi
     */
    public function showCompleteProfile()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        return view('student.complete-profile', compact('student'));
    }

    /**
     * Profilni yangilash
     * SECURITY FIXES: Passport uniqueness, birth date age validation, email uniqueness (BUG #52, #53, #54)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // SECURITY FIX: Enhanced validation (BUG #52, #53, #54)
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            // SECURITY FIX: Age range validation (BUG #53)
            'birth_date' => [
                'required',
                'date',
                'before:today',
                'after:' . now()->subYears(100)->format('Y-m-d'),
                'before:' . now()->subYears(16)->format('Y-m-d'),
            ],
            'gender' => 'required|in:male,female',
            // SECURITY FIX: Passport uniqueness validation (BUG #52)
            'passport_series' => 'nullable|string|max:10',
            'passport_number' => [
                'nullable',
                'string',
                'max:20',
                'required_with:passport_series',
                function ($attribute, $value, $fail) use ($request, $student) {
                    if ($value && $request->passport_series) {
                        $exists = Student::where('passport_series', $request->passport_series)
                            ->where('passport_number', $value)
                            ->where('id', '!=', $student->id)
                            ->exists();
                        if ($exists) {
                            $fail('Bu passport raqami allaqachon ro\'yxatdan o\'tgan.');
                        }
                    }
                }
            ],
            'address' => 'nullable|string|max:500',
            // SECURITY FIX: Email unique in both tables (BUG #54)
            'email' => [
                'nullable',
                'email',
                'unique:users,email,' . Auth::id(),
                'unique:students,email,' . $student->id,
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[+]998[0-9]{9}$/',
                'unique:users,phone,' . Auth::id(),
                'unique:students,phone,' . $student->id,
            ],
            'faculty_id' => 'nullable|exists:faculties,id',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        DB::beginTransaction();

        try {
            // User jadvalini yangilash
            $user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email ?: $student->student_id . '@student.uz',
                'phone' => $request->phone,
                'is_profile_complete' => true,
            ]);

            // Student jadvalini yangilash
            $student->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'full_name' => $request->first_name . ' ' . $request->last_name . ' ' . $request->middle_name,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'passport_series' => $request->passport_series,
                'passport_number' => $request->passport_number,
                'address' => $request->address,
                'email' => $request->email,
                'phone' => $request->phone,
                'faculty_id' => $request->faculty_id,
                'group_id' => $request->group_id,
                'profile_completed' => true,
            ]);

            DB::commit();

            return redirect()->route('student.dashboard')
                ->with('success', 'Profilingiz muvaffaqiyatli yangilandi!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Student profile update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except('password', 'password_confirmation')
            ]);
            return back()->withErrors(['error' => 'Ma\'lumotlarni saqlashda xatolik yuz berdi: ' . $e->getMessage()])
                        ->withInput();
        }
    }
}