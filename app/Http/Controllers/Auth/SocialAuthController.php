<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        $provider = strtolower($provider);

        return match ($provider) {
            'google'   => $this->redirectGoogle(),
            'facebook' => $this->redirectFacebook(),
            default    => redirect()->route('login')->withErrors(['oauth' => 'Noma\'lum provider: ' . $provider]),
        };
    }

    public function callback(Request $request, string $provider)
    {
        $provider = strtolower($provider);

        if ($request->has('error') || $request->has('error_description')) {
            return redirect()->route('login')->withErrors(['oauth' => $request->input('error_description') ?: 'Tizimga kirish bekor qilindi']);
        }

        try {
            $profile = match ($provider) {
                'google'   => $this->fetchGoogleProfile($request),
                'facebook' => $this->fetchFacebookProfile($request),
                default    => null,
            };

            if (!$profile) {
                return redirect()->route('login')->withErrors(['oauth' => 'OAuth javobi noto\'g\'ri']);
            }

            return $this->loginOrCreate($provider, $profile);
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors(['oauth' => 'OAuth xatosi: ' . $e->getMessage()]);
        }
    }

    /** Telegram Login Widget callback: /auth/telegram/callback?id=...&hash=... */
    public function telegramCallback(Request $request)
    {
        $botToken = config('services.telegram.bot_token');
        if (!$botToken) {
            return redirect()->route('login')->withErrors(['oauth' => 'Telegram bot token sozlanmagan']);
        }

        $data = $request->only(['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date', 'hash']);
        if (empty($data['id']) || empty($data['hash'])) {
            return redirect()->route('login')->withErrors(['oauth' => 'Telegram ma\'lumoti yetarli emas']);
        }

        // Verify hash per Telegram docs
        $hash = $data['hash'];
        unset($data['hash']);
        ksort($data);
        $dataCheckString = collect($data)->map(fn($v, $k) => "{$k}={$v}")->implode("\n");
        $secretKey = hash('sha256', $botToken, true);
        $computed = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (!hash_equals($computed, $hash)) {
            return redirect()->route('login')->withErrors(['oauth' => 'Telegram imzosi noto\'g\'ri']);
        }

        if (time() - (int)($data['auth_date'] ?? 0) > 86400) {
            return redirect()->route('login')->withErrors(['oauth' => 'Telegram muddati o\'tgan']);
        }

        $profile = [
            'id'     => (string)$data['id'],
            'name'   => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')) ?: ('tg_' . $data['id']),
            'email'  => null,
            'avatar' => $data['photo_url'] ?? null,
        ];

        return $this->loginOrCreate('telegram', $profile);
    }

    // ---------- Google ----------

    private function redirectGoogle()
    {
        $clientId = config('services.google.client_id');
        if (!$clientId) return redirect()->route('login')->withErrors(['oauth' => 'Google client_id sozlanmagan']);

        $params = [
            'client_id'     => $clientId,
            'redirect_uri'  => config('services.google.redirect'),
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ];
        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
    }

    private function fetchGoogleProfile(Request $request): ?array
    {
        $code = $request->input('code');
        if (!$code) return null;

        $token = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri'  => config('services.google.redirect'),
            'grant_type'    => 'authorization_code',
        ])->json();

        if (empty($token['access_token'])) return null;

        $info = Http::withToken($token['access_token'])
            ->get('https://www.googleapis.com/oauth2/v3/userinfo')
            ->json();

        if (empty($info['sub'])) return null;

        return [
            'id'     => (string)$info['sub'],
            'name'   => $info['name'] ?? ($info['email'] ?? 'Google user'),
            'email'  => $info['email'] ?? null,
            'avatar' => $info['picture'] ?? null,
        ];
    }

    // ---------- Facebook ----------

    private function redirectFacebook()
    {
        $clientId = config('services.facebook.client_id');
        if (!$clientId) return redirect()->route('login')->withErrors(['oauth' => 'Facebook client_id sozlanmagan']);

        $params = [
            'client_id'     => $clientId,
            'redirect_uri'  => config('services.facebook.redirect'),
            'response_type' => 'code',
            'scope'         => 'email,public_profile',
        ];
        return redirect('https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query($params));
    }

    private function fetchFacebookProfile(Request $request): ?array
    {
        $code = $request->input('code');
        if (!$code) return null;

        $token = Http::get('https://graph.facebook.com/v18.0/oauth/access_token', [
            'code'          => $code,
            'client_id'     => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'redirect_uri'  => config('services.facebook.redirect'),
        ])->json();

        if (empty($token['access_token'])) return null;

        $info = Http::withToken($token['access_token'])
            ->get('https://graph.facebook.com/me', ['fields' => 'id,name,email,picture'])
            ->json();

        if (empty($info['id'])) return null;

        return [
            'id'     => (string)$info['id'],
            'name'   => $info['name'] ?? 'Facebook user',
            'email'  => $info['email'] ?? null,
            'avatar' => $info['picture']['data']['url'] ?? null,
        ];
    }

    // ---------- Shared login/create ----------

    private function loginOrCreate(string $provider, array $profile)
    {
        $user = null;

        if (Schema::hasColumn('users', 'provider_id')) {
            $user = User::where('provider', $provider)
                ->where('provider_id', $profile['id'])
                ->first();
        }

        if (!$user && !empty($profile['email'])) {
            $user = User::where('email', $profile['email'])->first();
        }

        DB::transaction(function () use (&$user, $provider, $profile) {
            if (!$user) {
                $email = $profile['email'] ?: ('tg_' . $profile['id'] . '@telegram.local');

                $userData = [
                    'name'              => $profile['name'] ?? $email,
                    'email'             => $email,
                    'password'          => Hash::make(Str::random(40)),
                    'email_verified_at' => now(), // OAuth providers already verified the email
                ];
                if (Schema::hasColumn('users', 'provider'))    $userData['provider']    = $provider;
                if (Schema::hasColumn('users', 'provider_id')) $userData['provider_id'] = $profile['id'];
                if (Schema::hasColumn('users', 'avatar'))      $userData['avatar']      = $profile['avatar'] ?? null;
                if (Schema::hasColumn('users', 'is_profile_complete')) $userData['is_profile_complete'] = false;

                $user = User::create($userData);

                $role = Role::firstOrCreate(['name' => 'Student']);
                $user->assignRole($role);

                $studentId = $this->generateStudentId();
                $parts = preg_split('/\s+/', trim($profile['name']), 2);

                Student::create([
                    'user_id'           => $user->id,
                    'student_id'        => $studentId,
                    'first_name'        => $parts[0] ?? $profile['name'],
                    'last_name'         => $parts[1] ?? '',
                    'full_name'         => $profile['name'],
                    'email'             => $email,
                    'group_id'          => null,
                    'faculty_id'        => null,
                    'specialty_id'      => null,
                    'registration_date' => now(),
                    'status'            => 'active',
                    'profile_completed' => false,
                ]);
            } else {
                $changes = [];
                if (Schema::hasColumn('users', 'provider') && !$user->provider) {
                    $changes['provider'] = $provider;
                }
                if (Schema::hasColumn('users', 'provider_id') && !$user->provider_id) {
                    $changes['provider_id'] = $profile['id'];
                }
                if (Schema::hasColumn('users', 'avatar') && empty($user->avatar) && !empty($profile['avatar'])) {
                    $changes['avatar'] = $profile['avatar'];
                }
                if (!empty($changes)) $user->update($changes);
            }
        });

        Auth::login($user, true);

        if (Schema::hasColumn('users', 'is_profile_complete') && !$user->is_profile_complete) {
            return redirect()->route('student.complete-profile')
                ->with('success', 'Xush kelibsiz! Profilingizni to\'ldiring.');
        }

        return redirect()->intended('/dashboard');
    }

    private function generateStudentId(): string
    {
        $year = date('Y');
        $last = Student::where('student_id', 'like', "{$year}%")->orderByDesc('id')->first();
        $num = $last ? intval(substr($last->student_id, 4)) + 1 : 1;
        return $year . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
