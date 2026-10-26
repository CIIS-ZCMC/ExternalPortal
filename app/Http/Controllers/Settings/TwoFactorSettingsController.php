<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\GAuthClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorSettingsController extends Controller
{
    public function __construct(
        protected GAuthClientService $gauth
    ) {}

    protected function resolveUser(Request $request)
    {
        return Auth::guard('external')->user() 
            ?? Auth::guard('administrator')->user() 
            ?? $request->user();
    }

    /**
     * Start enrollment for the authenticated user.
     * Returns the Base32 secret, QR code data URI, and recovery codes.
     */
    public function enroll(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $userId = (string) $user->id;
        $email = $user->email ?? null;
        $name = method_exists($user, 'fullName') ? $user->fullName() : ($user->name ?? $user->username ?? 'User');

        $result = $this->gauth->enrollUser(
            userId: $userId,
            email: $email,
            name: $name
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'already_active' => $result['already_active'] ?? false,
                'message' => $result['message'] ?? 'Could not start 2FA setup.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }

    /**
     * Activate 2FA by verifying the very first 6-digit code.
     */
    public function activate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $code = trim($request->input('code'));

        $result = $this->gauth->activateUser(
            userId: (string) $user->id,
            code: $code
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Invalid code entered. Please check your phone time and try again.',
            ], 422);
        }

        // Mark 2FA as active in client database
        $user->update([
            'is_2fa_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Google Authenticator 2FA activated successfully!',
        ]);
    }

    /**
     * Disable 2FA for the user.
     */
    public function disable(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $this->gauth->resetUser((string) $user->id, 'User disabled 2FA in settings');

        $user->update([
            'is_2fa_enabled' => false,
            'two_factor_confirmed_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Two-factor authentication has been disabled.',
        ]);
    }

    /**
     * Check 2FA status for the user.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'is_2fa_enabled' => (bool) ($user->is_2fa_enabled ?? false),
            'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
        ]);
    }
}
