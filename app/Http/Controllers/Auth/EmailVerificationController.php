<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Contact;
use App\Services\EmailService;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function __construct(private EmailService $emailService) {}

    public function sendStaffVerification(Request $request)
    {
        $request->validate(['staff_id' => 'required|exists:tblstaff,staffid']);

        $staff = Staff::findOrFail($request->staff_id);

        if ($staff->email_verified_at) {
            return back()->with('info', 'Email already verified.');
        }

        $key = md5($staff->staffid . $staff->email . random_int(100000, 999999));
        $staff->update([
            'email_verification_key' => $key,
            'email_verification_sent_at' => now(),
        ]);

        $url = route('staff.email.verify', ['key' => $key, 'id' => $staff->staffid]);
        $body = "Hi {$staff->firstname},<br><br>"
              . "Please click the button below to verify your email address:<br><br>"
              . "<a href='{$url}' style='display:inline-block;padding:12px 24px;background-color:#2563eb;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:bold;'>Verify Email Address</a><br><br>"
              . "If you did not create an account, no further action is required.<br><br>"
              . "{email_signature}";

        $this->emailService->sendCustom($staff->email, 'Verify Your Email Address', $body);

        return back()->with('success', 'Verification email sent successfully.');
    }

    public function verifyStaff($id, $key)
    {
        $staff = Staff::findOrFail($id);

        if ($staff->email_verified_at) {
            return redirect()->route('login.staff')->with('info', 'Email already verified.');
        }

        if ($staff->email_verification_key !== $key) {
            return redirect()->route('login.staff')->withErrors(['email' => 'Invalid verification link.']);
        }

        $sentAt = $staff->email_verification_sent_at;
        if (!$sentAt || now()->diffInHours($sentAt) > 24) {
            return redirect()->route('login.staff')->withErrors(['email' => 'Verification link has expired. Request a new one.']);
        }

        $staff->update([
            'email_verified_at' => now(),
            'email_verification_key' => null,
            'email_verification_sent_at' => null,
        ]);

        return redirect()->route('login.staff')->with('success', 'Email verified successfully. You can now log in.');
    }

    public function sendContactVerification(Contact $contact)
    {
        if ($contact->email_verified_at) {
            return back()->with('info', 'Email already verified.');
        }

        $key = md5($contact->id . $contact->email . random_int(100000, 999999));
        $contact->update([
            'email_verification_key' => $key,
            'email_verification_sent_at' => now(),
        ]);

        $url = route('client.email.verify', ['key' => $key, 'id' => $contact->id]);

        $body = "Hello {$contact->firstname},<br><br>"
              . "Please click the button below to verify your email address:<br><br>"
              . "<a href='{$url}' style='display:inline-block;padding:12px 24px;background-color:#2563eb;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:bold;'>Verify Email Address</a><br><br>"
              . "If you did not create an account, no further action is required.<br><br>"
              . "{email_signature}";

        $this->emailService->sendCustom($contact->email, 'Verify Your Email Address', $body);

        return back()->with('success', 'Verification email sent successfully.');
    }

    public function verifyContact($id, $key)
    {
        $contact = Contact::findOrFail($id);

        if ($contact->email_verified_at) {
            return redirect()->route('login.client')->with('info', 'Email already verified.');
        }

        if ($contact->email_verification_key !== $key) {
            return redirect()->route('login.client')->withErrors(['email' => 'Invalid verification link.']);
        }

        $sentAt = $contact->email_verification_sent_at;
        if (!$sentAt || now()->diffInHours($sentAt) > 24) {
            return redirect()->route('login.client')->withErrors(['email' => 'Verification link has expired. Request a new one.']);
        }

        $contact->update([
            'email_verified_at' => now(),
            'email_verification_key' => null,
            'email_verification_sent_at' => null,
        ]);

        return redirect()->route('login.client')->with('success', 'Email verified successfully. You can now log in.');
    }

    public function resendStaff(Request $request)
    {
        $staffId = session('email_verify_staff_id');
        if (!$staffId) {
            return redirect()->route('login.staff')->withErrors(['email' => 'Session expired.']);
        }
        $request->merge(['staff_id' => $staffId]);
        return $this->sendStaffVerification($request);
    }
}
