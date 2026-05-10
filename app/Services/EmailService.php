<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send an email using a template slug
     */
    public function send($to, $slug, $data = [], $headers = [])
    {
        $template = EmailTemplate::where('slug', $slug)->where('active', 1)->first();

        if (!$template) {
            Log::warning("Email template with slug '{$slug}' not found or inactive.");
            return false;
        }

        // Add global settings to data
        $globalData = [
            'companyname' => SettingsHelper::get('companyname', 'CRM System'),
            'crm_url' => config('app.url'),
            'email_signature' => SettingsHelper::get('email_signature', 'Regards, <br>' . SettingsHelper::get('companyname')),
        ];

        $mergedData = array_merge($globalData, $data);

        // Parse template
        $parsed = $template->parse($mergedData);

        // Prepare view data
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

                $message->to($to)
                        ->subject($parsed['subject']);
                
                if ($fromEmail) {
                    $message->from($fromEmail, $fromName);
                }

                // Add custom headers for threading
                if (!empty($headers)) {
                    $symfonyMessage = $message->getSymfonyMessage();
                    foreach ($headers as $key => $value) {
                        if ($key === 'Message-ID') {
                            $symfonyMessage->getHeaders()->addIdHeader($key, $value);
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

    /**
     * Send a custom email (not using template)
     */
    public function sendCustom($to, $subject, $messageBody)
    {
        $viewData = [
            'subject' => $subject,
            'body' => $messageBody,
            'logo' => SettingsHelper::get('company_logo'),
            'company_name' => SettingsHelper::get('companyname'),
            'company_address' => nl2br(e(SettingsHelper::get('companyaddress'))),
        ];

        try {
            Mail::send('emails.base', $viewData, function ($message) use ($to, $subject) {
                $fromName = SettingsHelper::get('companyname', 'CRM System');
                $fromEmail = SettingsHelper::get('smtp_email');

                $message->to($to)
                        ->subject($subject);
                
                if ($fromEmail) {
                    $message->from($fromEmail, $fromName);
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send custom email to {$to}: " . $e->getMessage());
            return false;
        }
    }
}
