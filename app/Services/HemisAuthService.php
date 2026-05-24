<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class HemisAuthService
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $hemisUrl;
    protected $tokenUrl;
    protected $userInfoUrl;

    public function __construct()
    {
        $this->clientId = config('services.hemis.client_id');
        $this->clientSecret = config('services.hemis.client_secret');
        $this->redirectUri = config('services.hemis.redirect_uri');
        $this->hemisUrl = config('services.hemis.base_url', 'https://hemis.uz');
        $this->tokenUrl = $this->hemisUrl . '/oauth/token';
        $this->userInfoUrl = $this->hemisUrl . '/api/user/info';
    }

    /**
     * Get HEMIS authorization URL
     * SECURITY FIX: Accept state parameter from controller (BUG #38)
     */
    public function getAuthorizationUrl(?string $state = null): string
    {
        // Use provided state or generate new one
        $stateParam = $state ?? $this->generateState();

        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'read:user',
            'state' => $stateParam,
        ]);

        return $this->hemisUrl . '/oauth/authorize?' . $params;
    }

    /**
     * Handle callback from HEMIS
     */
    public function handleCallback(Request $request): ?array
    {
        if (!$request->has('code')) {
            Log::error('HEMIS callback missing authorization code');
            return null;
        }

        if (!$this->verifyState($request->get('state'))) {
            Log::error('HEMIS callback state verification failed');
            return null;
        }

        $token = $this->getAccessToken($request->get('code'));
        
        if (!$token) {
            return null;
        }

        return $this->getUserInfo($token);
    }

    /**
     * Get access token from HEMIS
     */
    protected function getAccessToken(string $code): ?string
    {
        try {
            $response = Http::post($this->tokenUrl, [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'code' => $code,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }

            Log::error('Failed to get HEMIS access token: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('HEMIS token request error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user information from HEMIS
     */
    protected function getUserInfo(string $token): ?array
    {
        try {
            $response = Http::withToken($token)->get($this->userInfoUrl);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'hemis_id' => $data['id'] ?? null,
                    'full_name' => $data['full_name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'type' => $data['type'] ?? 'student',
                    'faculty' => $data['faculty'] ?? null,
                    'specialty' => $data['specialty'] ?? null,
                    'group' => $data['group'] ?? null,
                    'course' => $data['course'] ?? null,
                ];
            }

            Log::error('Failed to get HEMIS user info: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('HEMIS user info request error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user data from HEMIS by ID
     */
    public function getUserData(string $hemisId): ?array
    {
        try {
            $token = $this->getServiceToken();
            
            if (!$token) {
                return null;
            }

            $response = Http::withToken($token)->get($this->hemisUrl . '/api/user/' . $hemisId);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'hemis_id' => $data['id'] ?? null,
                    'full_name' => $data['full_name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'type' => $data['type'] ?? 'student',
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('HEMIS user data request error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get service token for server-to-server requests
     */
    protected function getServiceToken(): ?string
    {
        return Cache::remember('hemis_service_token', 3600, function () {
            try {
                $response = Http::post($this->tokenUrl, [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['access_token'] ?? null;
                }

                return null;

            } catch (\Exception $e) {
                Log::error('HEMIS service token error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Generate state for CSRF protection
     */
    protected function generateState(): string
    {
        $state = bin2hex(random_bytes(16));
        session(['hemis_state' => $state]);
        return $state;
    }

    /**
     * Verify state for CSRF protection
     */
    protected function verifyState(string $state): bool
    {
        $sessionState = session('hemis_state');
        session()->forget('hemis_state');
        return $sessionState === $state;
    }
}