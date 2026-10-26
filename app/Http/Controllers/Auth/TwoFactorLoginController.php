<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ExternalEmployees;
use App\Models\Administrator;
use App\Models\User;
use App\Services\GAuthClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorLoginController extends Controller
{
    public function __construct(
        protected GAuthClientService $gauth
    ) {}

    protected function findUserByGuard(string $guard, mixed $userId)
    {
        if ($guard === 'administrator') {
            return Administrator::findOrFail($userId);
        }

        if ($guard === 'external') {
            return ExternalEmployees::findOrFail($userId);
        }

        return ExternalEmployees::find($userId) ?? User::find($userId) ?? Administrator::find($userId);
    }

    /**
     * Show the 6-digit verification page.
     */
    public function showChallenge(Request $request)
    {
        $userId = $request->session()->get('auth.2fa.user_id');
        $guard = $request->session()->get('auth.2fa.guard', 'external');

        if (! $userId) {
            return redirect()->route('portal.login');
        }

        $user = $this->findUserByGuard($guard, $userId);

        return view('auth.two-factor-challenge', [
            'user' => $user,
        ]);
    }

    /**
     * Verify submitted 6-digit code or emergency recovery code.
     */
    public function verifyChallenge(Request $request)
    {
        $userId = $request->session()->get('auth.2fa.user_id');
        $guard = $request->session()->get('auth.2fa.guard', 'external');
        $remember = (bool) $request->session()->get('auth.2fa.remember', false);
        $googleToken = $request->session()->get('auth.2fa.google_token');

        if (! $userId) {
            return redirect()->route('portal.login');
        }

        $user = $this->findUserByGuard($guard, $userId);

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = trim($request->input('code'));
        $isRecovery = $request->boolean('is_recovery_code', false);

        // 1. Delegate verification to central GAuthServices microservice
        $result = $this->gauth->verifyCode(
            userId: (string) $user->id,
            code: $code,
            isRecoveryCode: $isRecovery,
            clientIp: $request->ip()
        );

        // 2. Handle Lockout (5 failed attempts -> 15 min lock)
        if ($result['is_locked'] ?? false) {
            $lockMsg = 'Your account is temporarily locked due to excessive failed attempts.';
            if (!empty($result['locked_until'])) {
                $lockMsg .= " Please try again after {$result['locked_until']}.";
            } else {
                $lockMsg .= ' Please try again in 15 minutes.';
            }

            throw ValidationException::withMessages([
                'code' => [$lockMsg],
            ]);
        }

        // 3. Handle Invalid Code
        if (! $result['verified']) {
            $left = $result['remaining_attempts'] ?? null;
            $msg = $result['message'] ?? 'Invalid verification code.';
            if ($left !== null) {
                $msg .= " ({$left} attempts remaining)";
            }

            throw ValidationException::withMessages([
                'code' => [$msg],
            ]);
        }

        // 4. Verification Successful: Complete Login in Target System
        $request->session()->forget([
            'auth.2fa.user_id',
            'auth.2fa.guard',
            'auth.2fa.remember',
            'auth.2fa.google_token',
            'url.intended',
        ]);

        Auth::guard($guard)->login($user, $remember);
        $request->session()->regenerate();

        if ($googleToken) {
            $request->session()->put('userToken_', $googleToken);
        }

        if ($guard === 'administrator') {
            return redirect('/administratorPanel');
        }

        return redirect('/portal');
    }
}
