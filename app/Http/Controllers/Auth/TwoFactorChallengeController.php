<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorChallengeController extends Controller
{
    public function __construct(
        private \App\Services\TwoFactorAuthService $twoFactorAuth
    ) {}

    public function showChallenge()
    {
        if (Auth::guard('staff')->check()) {
            return redirect()->route('admin.dashboard');
        }

        $staffId = session('2fa_staff_id');
        if (!$staffId) {
            return redirect()->route('login.staff')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $staff = Staff::find($staffId);
        if (!$staff || !$staff->two_factor_auth_code) {
            return redirect()->route('login.staff')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        return view('auth.staff.two-factor-challenge', [
            'staff' => $staff,
        ]);
    }

    public function verify(Request $request)
    {
        $staffId = session('2fa_staff_id');
        if (!$staffId) {
            return redirect()->route('login.staff')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $staff = Staff::find($staffId);
        if (!$staff) {
            return redirect()->route('login.staff')->withErrors(['email' => 'Invalid session.']);
        }

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        if ($this->twoFactorAuth->validateCode($staff, $request->code)) {
            session()->forget('2fa_staff_id');
            Auth::guard('staff')->login($staff);
            $request->session()->regenerate();
            $staff->update(['last_login' => now(), 'last_ip' => $request->ip()]);
            app(\App\Services\AuditService::class)->login('staff_2fa', $staff->staffid, $request);
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors(['code' => 'Invalid or expired verification code.']);
    }

    public function resend()
    {
        $staffId = session('2fa_staff_id');
        if (!$staffId) {
            return redirect()->route('login.staff')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $staff = Staff::find($staffId);
        if (!$staff) {
            return redirect()->route('login.staff')->withErrors(['email' => 'Invalid session.']);
        }

        $this->twoFactorAuth->generateAndSendCode($staff);
        return back()->with('success', 'A new verification code has been sent to your email.');
    }
}
