<?php

namespace App\Services;

use App\Models\Staff;
use App\Helpers\SettingsHelper;

class TwoFactorAuthService
{
    public function __construct(private EmailService $emailService) {}

    public function generateAndSendCode(Staff $staff): void
    {
        $code = (string) random_int(100000, 999999);

        $staff->update([
            'two_factor_auth_code' => $code,
            'two_factor_auth_code_requested' => now(),
        ]);

        $body = "Hi {$staff->firstname},<br><br>"
              . "Your login verification code is:<br><br>"
              . "<strong style='font-size: 24px; letter-spacing: 4px;'>{$code}</strong><br><br>"
              . "This code expires in 10 minutes.<br><br>"
              . "If you did not attempt to log in, please ignore this email.";

        $this->emailService->sendCustom(
            $staff->email,
            'Your Two-Factor Authentication Code',
            $body
        );
    }

    public function validateCode(Staff $staff, string $code): bool
    {
        if ($staff->two_factor_auth_code !== $code) {
            return false;
        }

        $requestedAt = $staff->two_factor_auth_code_requested;
        if (!$requestedAt || now()->diffInMinutes($requestedAt) > 10) {
            return false;
        }

        $staff->update([
            'two_factor_auth_code' => null,
            'two_factor_auth_code_requested' => null,
        ]);

        return true;
    }

    public function isGloballyEnabled(): bool
    {
        return SettingsHelper::get('two_factor_enabled', '0') === '1';
    }

    public function isEnabledForStaff(Staff $staff): bool
    {
        if (!$this->isGloballyEnabled()) {
            return false;
        }
        return (bool) $staff->two_factor_auth_enabled;
    }
}
