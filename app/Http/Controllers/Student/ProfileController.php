<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\StudentGroup;

class ProfileController extends Controller
{
    /**
     * Display student profile
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::with(['group.specialty', 'group.faculty', 'faculty', 'specialty'])
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        // Get academic info with null safety
        $academicInfo = [
            'group' => $student->group?->name ?? 'N/A',
            'specialty' => $student->group?->specialty?->name ?? ($student->specialty?->name_uz ?? 'N/A'),
            'faculty' => $student->group?->faculty?->name ?? ($student->faculty?->name_uz ?? 'N/A'),
            'course' => $student->group?->course ?? ($student->course ?? 'N/A'),
            'student_id' => $student->student_no ?? $student->student_id ?? 'N/A',
            'admitted_on' => $student->admitted_on ?? $student->admission_date,
            'status' => $student->status ?? 'active',
        ];

        return view('student.profile.index', compact('user', 'student', 'academicInfo'));
    }

    /**
     * Show ID card
     */
    public function idCard()
    {
        $user = Auth::user();
        $student = Student::with(['group.specialty', 'group.faculty', 'faculty', 'specialty'])
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        $academicInfo = [
            'group' => $student->group?->name ?? 'N/A',
            'specialty' => $student->group?->specialty?->name ?? ($student->specialty?->name_uz ?? 'N/A'),
            'faculty' => $student->group?->faculty?->name ?? ($student->faculty?->name_uz ?? 'N/A'),
            'course' => $student->group?->course ?? ($student->course ?? 'N/A'),
            'student_id' => $student->student_no ?? $student->student_id ?? 'N/A',
            'admitted_on' => $student->admitted_on ?? $student->admission_date,
        ];

        return view('student.profile.id-card', compact('user', 'student', 'academicInfo'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update user data
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        $user->save();

        // Update student data
        if (isset($validated['address'])) {
            $student->address = $validated['address'];
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        $student->save();

        return redirect()->route('student.profile.index')->with('success', 'Profil muvaffaqiyatli yangilandi!');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->with('error', 'Joriy parol noto\'g\'ri!');
        }

        // Update password (Laravel auto-hashes via 'hashed' cast)
        $user->password = $validated['new_password'];
        $user->save();

        return redirect()->route('student.profile.index')->with('success', 'Parol muvaffaqiyatli o\'zgartirildi!');
    }

    /**
     * Help page
     */
    public function help()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // FAQ categories
        $faqCategories = [
            [
                'name' => 'Umumiy savollar',
                'icon' => 'fas fa-question-circle',
                'faqs' => [
                    [
                        'question' => 'HEMIS tizimiga qanday kirish mumkin?',
                        'answer' => 'HEMIS tizimiga kirish uchun sizga berilgan login va paroldan foydalaning. Login odatda student ID raqamingiz, parol esa ro\'yxatdan o\'tish vaqtida yaratilgan parol.'
                    ],
                    [
                        'question' => 'Parolimni unutsam nima qilishim kerak?',
                        'answer' => 'Agar parolingizni unutgan bo\'lsangiz, login sahifasida "Parolni unutdingizmi?" tugmasini bosing yoki o\'quv bo\'limi bilan bog\'laning.'
                    ],
                    [
                        'question' => 'Profilimni qanday yangilash mumkin?',
                        'answer' => '"Mening profilim" bo\'limiga o\'ting va "Profilni tahrirlash" formasida ma\'lumotlaringizni yangilang. Email, telefon, manzil va profil rasmini o\'zgartirishingiz mumkin.'
                    ],
                ]
            ],
            [
                'name' => 'Dars jadvali',
                'icon' => 'fas fa-calendar-alt',
                'faqs' => [
                    [
                        'question' => 'Dars jadvalim qayerdan ko\'raman?',
                        'answer' => 'Sidebar menyusidagi "Dars jadvali" bo\'limiga o\'ting. Bu yerda haftalik va kunlik darslaringiz jadvali ko\'rsatiladi.'
                    ],
                    [
                        'question' => 'Dars jadvali o\'zgarsa nima qilishim kerak?',
                        'answer' => 'Dars jadvali avtomatik yangilanadi. Agar o\'zgarish ko\'rmasangiz, sahifani yangilang yoki texnik yordam bilan bog\'laning.'
                    ],
                ]
            ],
            [
                'name' => 'Topshiriqlar',
                'icon' => 'fas fa-tasks',
                'faqs' => [
                    [
                        'question' => 'Topshiriqlarni qayerdan topaman?',
                        'answer' => '"Topshiriqlar" bo\'limida barcha fanlar bo\'yicha berilgan topshiriqlarni ko\'rishingiz mumkin. Har bir topshiriq uchun muddat, tavsif va baholash mezonlari ko\'rsatilgan.'
                    ],
                    [
                        'question' => 'Topshiriqni qanday topshiraman?',
                        'answer' => 'Topshiriq sahifasiga o\'ting, "Topshirish" formasida matn yoki fayllarni yuklang va "Topshirish" tugmasini bosing.'
                    ],
                    [
                        'question' => 'Muddati o\'tgan topshiriqni topshira olamanmi?',
                        'answer' => 'Ha, lekin kech topshirish uchun jarima ball qo\'llaniladi. Jarima miqdori topshiriq sozlamalarida ko\'rsatilgan.'
                    ],
                ]
            ],
            [
                'name' => 'Baholar',
                'icon' => 'fas fa-star',
                'faqs' => [
                    [
                        'question' => 'Baholarimni qayerdan ko\'raman?',
                        'answer' => '"Baholar" bo\'limida barcha fanlar bo\'yicha baholaringizni, GPA va statistikangizni ko\'rishingiz mumkin.'
                    ],
                    [
                        'question' => 'GPA qanday hisoblanadi?',
                        'answer' => 'GPA 4.0 tizimida hisoblanadi: 86-100 = 4.0, 71-85 = 3.0, 56-70 = 2.0, 56 dan past = 0. Barcha fanlar bo\'yicha o\'rtacha olinadi.'
                    ],
                ]
            ],
            [
                'name' => 'Davomat',
                'icon' => 'fas fa-calendar-check',
                'faqs' => [
                    [
                        'question' => 'Davomatim qayerda ko\'rsatiladi?',
                        'answer' => '"Davomat" bo\'limida kunlik va umumiy davomat statistikangizni ko\'rishingiz mumkin.'
                    ],
                    [
                        'question' => 'Davomatda xatolik bo\'lsa nima qilaman?',
                        'answer' => 'Agar davomatda xatolik bo\'lsa, darhol o\'qituvchi yoki o\'quv bo\'limi bilan bog\'laning.'
                    ],
                ]
            ],
        ];

        // Quick links
        $quickLinks = [
            [
                'title' => 'Dars jadvali',
                'icon' => 'fas fa-calendar-alt',
                'route' => 'student.schedule',
                'color' => 'primary'
            ],
            [
                'title' => 'Topshiriqlar',
                'icon' => 'fas fa-tasks',
                'route' => 'student.assignments.index',
                'color' => 'warning'
            ],
            [
                'title' => 'Baholar',
                'icon' => 'fas fa-star',
                'route' => 'grades.all',
                'color' => 'success'
            ],
            [
                'title' => 'Davomat',
                'icon' => 'fas fa-calendar-check',
                'route' => 'student.attendance.index',
                'color' => 'info'
            ],
        ];

        // Contact info
        $contacts = [
            [
                'title' => 'Texnik yordam',
                'icon' => 'fas fa-laptop-code',
                'value' => 'support@tourism-academy.uz',
                'type' => 'email'
            ],
            [
                'title' => 'O\'quv bo\'limi',
                'icon' => 'fas fa-building',
                'value' => '+998 66 225 12 34',
                'type' => 'phone'
            ],
            [
                'title' => 'Ish vaqti',
                'icon' => 'fas fa-clock',
                'value' => 'Dush-Juma: 09:00-18:00',
                'type' => 'text'
            ],
            [
                'title' => 'Manzil',
                'icon' => 'fas fa-map-marker-alt',
                'value' => 'Samarqand sh., Registon ko\'chasi',
                'type' => 'text'
            ],
        ];

        return view('student.help', compact('student', 'faqCategories', 'quickLinks', 'contacts'));
    }
}
