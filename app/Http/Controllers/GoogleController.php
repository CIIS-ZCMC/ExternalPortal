<?php

namespace App\Http\Controllers;

use App\Models\ExternalEmployees;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')?->user();

        if (!$googleUser) {
            return redirect()->route('portal.login');
        }

        $user = ExternalEmployees::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            return redirect()->route('google.notFound', ['email' => $googleUser->getEmail()]);
        }

        if (is_null($user->email_verified_at)) {
            $user->update(['email_verified_at' => \Carbon\Carbon::now()]);
        }

        $googleProfile = [
            'email' => $googleUser->getEmail(),
            'name' => $googleUser->getName(),
            'avatar' => $googleUser->getAvatar(),
            'id' => $googleUser->getId()
        ];

        // Check if 2FA is active
        if ($user->is_2fa_enabled) {
            session([
                'auth.2fa.user_id' => $user->id,
                'auth.2fa.guard' => 'external',
                'auth.2fa.remember' => true,
                'auth.2fa.google_token' => $googleProfile,
            ]);

            return redirect()->route('2fa.challenge');
        }

        Auth::guard('external')->login($user);
        session()->put("userToken_", $googleProfile);
        return redirect('/portal');
    }

    public function notFound(Request $request)
    {

        return view('notFound', ['email' => $request->email]);
    }
}
