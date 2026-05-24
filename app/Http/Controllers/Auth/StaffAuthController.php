<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StaffAuthController extends Controller
{
    public function __construct(
        private \App\Services\TwoFactorAuthService $twoFactorAuth
    ) {}

    public function showLogin()
    {
        if (Auth::guard('staff')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.staff.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $lockKey = 'staff_login_lockout_' . $request->ip();
        $attemptsKey = 'staff_login_attempts_' . $request->ip();

        if (Cache::has($lockKey)) {
            $seconds = Cache::get($lockKey) - time();
            return back()->withErrors(['email' => 'Too many login attempts. Try again in ' . max(1, ceil($seconds / 60)) . ' minutes.']);
        }

        if (Auth::guard('staff')->attempt($credentials)) {
            $staff = Auth::guard('staff')->user();

            $verificationRequired = \App\Helpers\SettingsHelper::get('staff_email_verification_required', '0') === '1';
            if ($verificationRequired && !$staff->email_verified_at) {
                Auth::guard('staff')->logout();
                session()->put('email_verify_staff_id', $staff->staffid);
                $key = md5($staff->staffid . $staff->email . random_int(100000, 999999));
                $staff->update(['email_verification_key' => $key, 'email_verification_sent_at' => now()]);
                $mailer = app(\App\Services\EmailService::class);
                $url = route('staff.email.verify', ['key' => $key, 'id' => $staff->staffid]);
                $body = "Hi {$staff->firstname},<br><br>Please click the button below to verify your email:<br><br>"
                      . "<a href='{$url}' style='display:inline-block;padding:12px 24px;background-color:#2563eb;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;'>Verify Email Address</a><br><br>"
                      . "If you did not create an account, no further action is required.";
                $mailer->sendCustom($staff->email, 'Verify Your Email Address', $body);
                return redirect()->route('login.staff')->with('info', 'Please check your email to verify your account before logging in.');
            }

            if ($this->twoFactorAuth->isEnabledForStaff($staff)) {
                Auth::guard('staff')->logout();
                $request->session()->put('2fa_staff_id', $staff->staffid);
                $this->twoFactorAuth->generateAndSendCode($staff);
                return redirect()->route('staff.2fa.challenge');
            }

            Cache::forget($attemptsKey);
            Cache::forget($lockKey);
            $request->session()->regenerate();

            $staff->update(['last_login' => now(), 'last_ip' => $request->ip()]);
            app(\App\Services\AuditService::class)->login('staff', $staff->staffid, $request);

            return redirect()->intended('/admin/dashboard');
        }

        $attempts = (int) Cache::get($attemptsKey, 0) + 1;
        Cache::put($attemptsKey, $attempts, 3600);

        if ($attempts >= 5) {
            Cache::put($lockKey, time() + 900, 900);
            Cache::forget($attemptsKey);
            return back()->withErrors(['email' => 'Account locked for 15 minutes due to too many failed attempts.']);
        }

        return back()->withErrors(['email' => 'Invalid staff credentials.']);
    }

    public function logout(Request $request)
    {
        $staffId = Auth::guard('staff')->id();
        app(\App\Services\AuditService::class)->logout('staff', $staffId, $request);
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logged out successfully');
    }
}