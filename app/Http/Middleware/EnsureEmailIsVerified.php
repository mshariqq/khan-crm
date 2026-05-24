<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next, string $guard = 'staff')
    {
        $user = Auth::guard($guard)->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->email_verified_at) {
            return $next($request);
        }

        $setting = \App\Helpers\SettingsHelper::get($guard === 'staff' ? 'staff_email_verification_required' : 'client_email_verification_required', '0');

        if ($setting !== '1') {
            return $next($request);
        }

        if ($guard === 'staff') {
            session()->put('email_verify_staff_id', $user->staffid);
        }

        Auth::guard($guard)->logout();

        $route = $guard === 'staff' ? 'login.staff' : 'login.client';
        return redirect()->route($route)->withErrors(['email' => 'Please verify your email address before accessing the system.']);
    }
}
