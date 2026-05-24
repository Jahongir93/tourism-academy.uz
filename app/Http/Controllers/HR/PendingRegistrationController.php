<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\PendingRegistration;
use Illuminate\Http\Request;

class PendingRegistrationController extends Controller
{
    /**
     * Pending registratsiyalarni ko'rsatish
     */
    public function index(Request $request)
    {
        $query = PendingRegistration::with('reviewer')
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            // By default, show only pending
            $query->where('status', 'pending');
        }

        $registrations = $query->paginate(20);

        return view('hr.pending-registrations.index', compact('registrations'));
    }

    /**
     * Show details of a pending registration
     */
    public function show($id)
    {
        $registration = PendingRegistration::with('reviewer')->findOrFail($id);
        return view('hr.pending-registrations.show', compact('registration'));
    }

    /**
     * Approve a pending registration
     */
    public function approve(Request $request, $id)
    {
        $registration = PendingRegistration::findOrFail($id);

        if (!$registration->isPending()) {
            return back()->withErrors(['error' => 'Bu registratsiya allaqachon ko\'rib chiqilgan']);
        }

        // Get password from additional_info
        $additionalInfo = json_decode($registration->additional_info, true);
        $password = $additionalInfo['password'] ?? 'default123';

        try {
            // Approve using model method
            $success = $registration->approve(auth()->id(), $password);

            if ($success) {
                return redirect()->route('hr.pending-registrations.index')
                    ->with('success', 'Registratsiya muvaffaqiyatli tasdiqlandi! Agar foydalanuvchi avval ro\'yxatdan o\'tgan bo\'lsa, ma\'lumotlari yangilandi.');
            } else {
                return back()->withErrors(['error' => 'Registratsiyani tasdiqlashda xatolik yuz berdi. Loglarni tekshiring.']);
            }
        } catch (\Exception $e) {
            \Log::error('Approve registration error in controller: ' . $e->getMessage(), [
                'registration_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            // Display user-friendly error message
            $errorMessage = $e->getMessage();

            // If it's our custom user-friendly message, show it directly
            if (strpos($errorMessage, 'Bu telefon raqam') !== false ||
                strpos($errorMessage, 'Bu email') !== false ||
                strpos($errorMessage, 'Bu ma\'lumotlar') !== false) {
                return back()->withErrors(['error' => $errorMessage]);
            }

            // Otherwise show generic error
            return back()->withErrors(['error' => 'Xatolik: ' . $errorMessage]);
        }
    }

    /**
     * Reject a pending registration
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'rejection_reason.required' => 'Rad etish sababini kiriting',
        ]);

        $registration = PendingRegistration::findOrFail($id);

        if (!$registration->isPending()) {
            return back()->withErrors(['error' => 'Bu registratsiya allaqachon ko\'rib chiqilgan']);
        }

        $success = $registration->reject(auth()->id(), $request->rejection_reason);

        if ($success) {
            return redirect()->route('hr.pending-registrations.index')
                ->with('success', 'Registratsiya rad etildi');
        } else {
            return back()->withErrors(['error' => 'Registratsiyani rad etishda xatolik yuz berdi']);
        }
    }

    /**
     * Delete a pending registration
     */
    public function destroy($id)
    {
        $registration = PendingRegistration::findOrFail($id);
        $registration->delete();

        return redirect()->route('hr.pending-registrations.index')
            ->with('success', 'Registratsiya o\'chirildi');
    }
}
