<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('external')->check()) {
            $user = Auth::guard('external')->user();

            if (is_null($user->email_verified_at)) {
                Auth::guard('external')->logout();
                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                return redirect()->route('portal.login')->with('error', 'Your email address has not been verified yet. Please check your email to verify your account before logging in.');
            }
        }

        return $next($request);
    }
}
