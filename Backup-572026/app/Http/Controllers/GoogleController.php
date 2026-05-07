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
        Auth::guard('external')->login($user);
        session()->put("userToken_", [
            'email' => $googleUser->getEmail(),
            'name' => $googleUser->getName(),
            'avatar' => $googleUser->getAvatar(),
            'id' => $googleUser->getId()
        ]);
        return redirect('/portal');
    }

    public function notFound(Request $request)
    {

        return view('notFound', ['email' => $request->email]);
    }
}
