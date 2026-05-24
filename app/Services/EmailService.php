<?php

namespace App\Services;

use App\Helpers\SettingsHelper;
use App\Jobs\SendEmailJob;
use App\Models\EmailRateLimit;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function send($to, $slug, $data = [], $headers = []): bool
    {
        $template = EmailTemplate::where('slug', $slug)->where('active', 1)->first();

        if (!$template) {
            Log::warning("Email template with slug '{$slug}' not found or inactive.");
            return false;
        }

        // Apply rate limiting if limitable context is provided
        if (!empty($data['_rate_limit_type']) && !empty($data['_rate_limit_id'])) {
            $rateType = $data['_rate_limit_type'];
            $rateId = $data['_rate_limit_id'];
            $recipientType = $data['_rate_recipient_type'] ?? 'client';
            $maxPerHour = $data['_rate_max_per_hour'] ?? 50;

            if (!$this->checkRateLimit($rateType, $rateId, $recipientType, $maxPerHour)) {
                Log::warning("Rate limit hit for {$rateType}#{$rateId} sending {$slug} to {$to}");
                return false;
            }

            $this->incrementRateLimit($rateType, $rateId, $recipientType, $maxPerHour);
        }

        $globalData = [
            'companyname' => SettingsHelper::get('companyname', 'CRM System'),
            'crm_url' => config('app.url'),
            'email_signature' => SettingsHelper::get('email_signature', 'Regards, <br>' . SettingsHelper::get('companyname')),
        ];

        $mergedData = array_merge($globalData, $data);
        $parsed = $template->parse($mergedData);

        $viewData = [
            'subject' => $parsed['subject'],
            'body' => $parsed['message'],
            'logo' => SettingsHelper::get('company_logo'),
            'company_name' => SettingsHelper::get('companyname'),
            'company_address' => nl2br(e(SettingsHelper::get('companyaddress'))),
        ];

        $fromName = $template->fromname ?: SettingsHelper::get('companyname', 'CRM System');
        $fromEmail = $template->fromemail ?: SettingsHelper::get('smtp_email');

        dispatch(new SendEmailJob(
            to: $to,
            subject: $parsed['subject'],
            body: $viewData['body'],
            fromEmail: $fromEmail,
            fromName: $fromName,
            slug: $slug,
            trackableType: 'email_template',
            trackableId: $template->emailtemplateid,
        ));

        return true;
    }

    public function sendCustom($to, $subject, $messageBody): bool
    {
        $fromName = SettingsHelper::get('companyname', 'CRM System');
        $fromEmail = SettingsHelper::get('smtp_email');

        dispatch(new SendEmailJob(
            to: $to,
            subject: $subject,
            body: $messageBody,
            fromEmail: $fromEmail,
            fromName: $fromName,
        ));

        return true;
    }

    public function sendSync($to, $slug, $data = [], $headers = []): bool
    {
        $template = EmailTemplate::where('slug', $slug)->where('active', 1)->first();

        if (!$template) {
            Log::warning("Email template with slug '{$slug}' not found or inactive.");
            return false;
        }

        $globalData = [
            'companyname' => SettingsHelper::get('companyname', 'CRM System'),
            'crm_url' => config('app.url'),
            'email_signature' => SettingsHelper::get('email_signature', 'Regards, <br>' . SettingsHelper::get('companyname')),
        ];

        $mergedData = array_merge($globalData, $data);
        $parsed = $template->parse($mergedData);

        $viewData = [
            'subject' => $parsed['subject'],
            'body' => $parsed['message'],
            'logo' => SettingsHelper::get('company_logo'),
            'company_name' => SettingsHelper::get('companyname'),
            'company_address' => nl2br(e(SettingsHelper::get('companyaddress'))),
        ];

        try {
            Mail::send('emails.base', $viewData, function ($message) use ($to, $parsed, $template, $headers) {
                $fromName = $template->fromname ?: SettingsHelper::get('companyname', 'CRM System');
                $fromEmail = $template->fromemail ?: SettingsHelper::get('smtp_email');

                $message->to($to)->subject($parsed['subject']);

                if ($fromEmail) {
                    $message->from($fromEmail, $fromName);
                }

                if (!empty($headers)) {
                    $symfonyMessage = $message->getSymfonyMessage();
                    foreach ($headers as $key => $value) {
                        if (in_array($key, ['Message-ID', 'In-Reply-To', 'References'])) {
                            $ids = array_map(fn($id) => trim($id, ' <>'), preg_split('/\s+/', $value));
                            $symfonyMessage->getHeaders()->addIdHeader($key, $ids);
                        } else {
                            $symfonyMessage->getHeaders()->addTextHeader($key, $value);
                        }
                    }
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email to {$to} using slug '{$slug}': " . $e->getMessage());
            return false;
        }
    }

    public function checkRateLimit($limitableType, $limitableId, string $recipientType = 'client', int $maxPerHour = 50): bool
    {
        return EmailRateLimit::canSend($limitableType, $limitableId, $recipientType, $maxPerHour);
    }

    public function incrementRateLimit($limitableType, $limitableId, string $recipientType = 'client', int $maxPerHour = 50): void
    {
        EmailRateLimit::incrementCount($limitableType, $limitableId, $recipientType, $maxPerHour);
    }

    public function getPreviewData(string $slug, array $data = []): array
    {
        $template = EmailTemplate::where('slug', $slug)->first();

        if (!$template) {
            return ['error' => "Template '{$slug}' not found"];
        }

        $globalData = [
            'companyname' => SettingsHelper::get('companyname', 'CRM System'),
            'crm_url' => config('app.url'),
            'email_signature' => SettingsHelper::get('email_signature', 'Regards, <br>' . SettingsHelper::get('companyname')),
        ];

        $mergedData = array_merge($globalData, $data);
        $parsed = $template->parse($mergedData);

        return [
            'subject' => $parsed['subject'],
            'body' => $parsed['message'],
            'template' => $template,
            'from_name' => $template->fromname ?: SettingsHelper::get('companyname'),
            'from_email' => $template->fromemail ?: SettingsHelper::get('smtp_email'),
        ];
    }
}
