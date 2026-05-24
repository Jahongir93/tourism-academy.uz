<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Teacher profile sahifasini ko'rsatish
     */
    public function index()
    {
        $user = Auth::user();

        // Rol tekshiruvi
        if (!$user->hasAnyRole(['teacher', 'Teacher', 'superadmin'])) {
            abort(403, 'O\'qituvchi profili topilmadi. Iltimos administrator bilan bog\'laning.');
        }

        return view('teacher.profile', compact('user'));
    }

    /**
     * Teacher profilini yangilash
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'bio' => $request->bio,
        ]);

        return redirect()->route('teacher.profile')
            ->with('success', 'Profil muvaffaqiyatli yangilandi!');
    }

    /**
     * Parolni o'zgartirish
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Joriy parol noto\'g\'ri']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('teacher.profile')
            ->with('success', 'Parol muvaffaqiyatli o\'zgartirildi!');
    }
}