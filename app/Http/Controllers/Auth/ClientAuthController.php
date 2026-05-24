<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ClientAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('client')->check()) {
            return redirect()->route('client.dashboard');
        }
        return view('auth.client.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $lockKey = 'client_login_lockout_' . $request->ip();
        $attemptsKey = 'client_login_attempts_' . $request->ip();

        if (Cache::has($lockKey)) {
            $seconds = Cache::get($lockKey) - time();
            return back()->withErrors(['email' => 'Too many login attempts. Try again in ' . max(1, ceil($seconds / 60)) . ' minutes.']);
        }

        if (Auth::guard('client')->attempt($credentials)) {
            $contact = Auth::guard('client')->user();

            $verificationRequired = \App\Helpers\SettingsHelper::get('client_email_verification_required', '0') === '1';
            if ($verificationRequired && !$contact->email_verified_at) {
                Auth::guard('client')->logout();
                $key = md5($contact->id . $contact->email . random_int(100000, 999999));
                $contact->update(['email_verification_key' => $key, 'email_verification_sent_at' => now()]);
                $mailer = app(\App\Services\EmailService::class);
                $url = route('client.email.verify', ['key' => $key, 'id' => $contact->id]);
                $body = "Hello {$contact->firstname},<br><br>Please click the button below to verify your email:<br><br>"
                      . "<a href='{$url}' style='display:inline-block;padding:12px 24px;background-color:#2563eb;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;'>Verify Email Address</a><br><br>"
                      . "If you did not create an account, no further action is required.";
                $mailer->sendCustom($contact->email, 'Verify Your Email Address', $body);
                return redirect()->route('login.client')->with('info', 'Please check your email to verify your account before logging in.');
            }

            Cache::forget($attemptsKey);
            Cache::forget($lockKey);
            $request->session()->regenerate();
            app(\App\Services\AuditService::class)->login('client', $contact->id, $request);
            return redirect()->intended('/client/dashboard');
        }

        $attempts = (int) Cache::get($attemptsKey, 0) + 1;
        Cache::put($attemptsKey, $attempts, 3600);

        if ($attempts >= 5) {
            Cache::put($lockKey, time() + 900, 900);
            Cache::forget($attemptsKey);
            return back()->withErrors(['email' => 'Account locked for 15 minutes due to too many failed attempts.']);
        }

        return back()->withErrors(['email' => 'Invalid client credentials.']);
    }

    public function logout(Request $request)
    {
        $contactId = Auth::guard('client')->id();
        app(\App\Services\AuditService::class)->logout('client', $contactId, $request);
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logged out successfully');
    }
}