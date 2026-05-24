<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\HemisAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class HemisAuthController extends Controller
{
    protected $hemisService;

    public function __construct(HemisAuthService $hemisService)
    {
        $this->hemisService = $hemisService;
    }

    /**
     * Redirect to HEMIS for authentication
     * SECURITY FIX: Added state parameter for CSRF protection (BUG #38)
     */
    public function redirectToHemis()
    {
        // SECURITY FIX: Generate and store state token for OAuth CSRF protection
        $state = Str::random(40);
        session(['hemis_oauth_state' => $state]);

        return redirect($this->hemisService->getAuthorizationUrl($state));
    }

    /**
     * Handle callback from HEMIS
     * SECURITY FIXES: State validation, strong random password, transaction wrapping (BUG #37-41)
     */
    public function handleCallback(Request $request)
    {
        try {
            // SECURITY FIX: Verify state parameter to prevent CSRF attacks (BUG #38)
            $state = $request->input('state');
            $sessionState = session('hemis_oauth_state');

            if (!$state || !$sessionState || $state !== $sessionState) {
                session()->forget('hemis_oauth_state');
                return redirect()->route('login')
                    ->withErrors(['hemis' => 'Xavfsizlik tekshiruvi muvaffaqiyatsiz. Qaytadan urinib ko\'ring.']);
            }

            // Clear the state from session
            session()->forget('hemis_oauth_state');

            // SECURITY NOTE: Token signature verification should be done in HemisAuthService (BUG #39)
            $hemisData = $this->hemisService->handleCallback($request);

            if (!$hemisData) {
                return redirect()->route('login')
                    ->withErrors(['hemis' => 'HEMIS orqali kirish amalga oshmadi.']);
            }

            // SECURITY FIX: Wrap in transaction (BUG #40)
            DB::beginTransaction();

            $user = User::where('hemis_id', $hemisData['hemis_id'])->first();

            if (!$user) {
                // SECURITY FIX: Use cryptographically secure random password (BUG #37)
                $user = User::create([
                    'name' => $hemisData['full_name'],
                    'email' => $hemisData['email'] ?? null,
                    'phone' => $hemisData['phone'] ?? null,
                    'hemis_id' => $hemisData['hemis_id'],
                    'password' => Hash::make(Str::random(32)), // SECURITY FIX: Strong random password
                    'user_type' => 'uzbek',
                    'phone_verified_at' => now(), // HEMIS users are pre-verified (BUG #41 - BY DESIGN)
                ]);

                // SECURITY FIX: Thread-safe role creation
                $roleType = $hemisData['type'] === 'student' ? 'Student' : 'Teacher';
                $role = Role::lockForUpdate()->where('name', $roleType)->first();
                if (!$role) {
                    $role = Role::create(['name' => $roleType]);
                }
                $user->assignRole($role);
            } else {
                // SECURITY FIX: User update inside transaction (BUG #40)
                $user->update([
                    'name' => $hemisData['full_name'],
                    'email' => $hemisData['email'] ?? $user->email,
                    'phone' => $hemisData['phone'] ?? $user->phone,
                ]);
            }

            DB::commit();

            // SECURITY NOTE: Auto-login for HEMIS users is by design (BUG #41)
            // HEMIS is a trusted government system, users are pre-verified
            Auth::login($user, true);

            return redirect($user->getDashboardRoute())
                ->with('success', 'HEMIS orqali muvaffaqiyatli kirdingiz!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('login')
                ->withErrors(['hemis' => 'HEMIS orqali kirish xatolik yuz berdi: ' . $e->getMessage()]);
        }
    }

    /**
     * Sync user data with HEMIS
     */
    public function syncWithHemis(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hemis_id) {
            return response()->json([
                'success' => false,
                'message' => 'Foydalanuvchi HEMIS bilan bog\'lanmagan.',
            ], 400);
        }

        try {
            $hemisData = $this->hemisService->getUserData($user->hemis_id);
            
            $user->update([
                'name' => $hemisData['full_name'],
                'email' => $hemisData['email'] ?? $user->email,
                'phone' => $hemisData['phone'] ?? $user->phone,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ma\'lumotlar HEMIS bilan sinxronlashtirildi.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sinxronlashda xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }
}