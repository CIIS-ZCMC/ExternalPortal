<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GAuthClientService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.gauth.base_url', env('GAUTH_BASE_URL', 'http://127.0.0.1:8001')), '/');
        $this->apiKey = config('services.gauth.api_key', env('GAUTH_API_KEY', ''));
    }

    /**
     * Step 1: Initialize 2FA enrollment.
     * Returns QR Code Data URI, manual setup key, and backup recovery codes.
     */
    public function enrollUser(string $userId, ?string $email = null, ?string $name = null): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(8)
                ->post("{$this->baseUrl}/api/v1/2fa/enroll", [
                    'external_user_id' => $userId,
                    'external_user_email' => $email,
                    'external_user_name' => $name,
                ]);

            if ($response->status() === 409) {
                return [
                    'success' => false,
                    'already_active' => true,
                    'message' => 'Two-factor authentication is already active.',
                ];
            }

            if (! $response->successful()) {
                Log::error('GAuth enrollment failed: ' . $response->body());
                return [
                    'success' => false,
                    'message' => $response->json('message', 'Failed to enroll 2FA.'),
                ];
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error('GAuth enrollment exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Cannot connect to 2FA authentication server. Please try again later.',
            ];
        }
    }

    /**
     * Step 2: Confirm first code to activate 2FA for the user.
     */
    public function activateUser(string $userId, string $code): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(6)
                ->post("{$this->baseUrl}/api/v1/2fa/activate", [
                    'external_user_id' => $userId,
                    'code' => trim($code),
                ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'message' => $response->json('message', $response->successful() ? '2FA Activated' : 'Invalid code entered.'),
            ];
        } catch (\Throwable $e) {
            Log::error('GAuth activation exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Network error during 2FA activation.',
            ];
        }
    }

    /**
     * Step 3: Verify login TOTP code or emergency backup recovery code.
     */
    public function verifyCode(string $userId, string $code, bool $isRecoveryCode = false, ?string $clientIp = null): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(6)
                ->post("{$this->baseUrl}/api/v1/2fa/verify", [
                    'external_user_id' => $userId,
                    'code' => trim($code),
                    'is_recovery_code' => $isRecoveryCode,
                    'ip_address' => $clientIp ?? request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

            $json = $response->json() ?? [];

            return [
                'verified' => $response->successful() && ($json['verified'] ?? false) === true,
                'status' => $response->status(),
                'data' => $json,
                'is_locked' => ($response->status() === 423) || ($json['is_locked'] ?? false),
                'locked_until' => $json['locked_until'] ?? null,
                'remaining_attempts' => $json['remaining_attempts'] ?? null,
                'remaining_recovery_codes' => $json['remaining_recovery_codes'] ?? null,
                'message' => $json['message'] ?? ($response->successful() ? 'Verified' : 'Invalid code'),
            ];
        } catch (\Throwable $e) {
            Log::error('GAuth verify exception: ' . $e->getMessage());
            return [
                'verified' => false,
                'status' => 500,
                'is_locked' => false,
                'message' => 'Unable to verify code at this time. Please try again.',
            ];
        }
    }

    /**
     * Revoke or reset 2FA when an employee disables 2FA or loses their device.
     */
    public function resetUser(string $userId, ?string $reason = null): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(6)
                ->post("{$this->baseUrl}/api/v1/2fa/reset", [
                    'external_user_id' => $userId,
                    'reason' => $reason ?? 'User disabled 2FA',
                ]);

            return [
                'success' => $response->successful(),
                'message' => $response->json('message', '2FA reset successfully.'),
            ];
        } catch (\Throwable $e) {
            Log::error('GAuth reset exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Unable to reach 2FA server to reset.',
            ];
        }
    }

    /**
     * Check user's 2FA enrollment status.
     */
    public function checkStatus(string $userId): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(5)
                ->get("{$this->baseUrl}/api/v1/2fa/status/{$userId}");

            return $response->json() ?? ['status' => 'none', 'is_enrolled' => false];
        } catch (\Throwable $e) {
            return ['status' => 'unknown', 'is_enrolled' => false];
        }
    }
}
