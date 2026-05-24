<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\ProposalComment;
use App\Models\EmailTemplate;
use App\Models\Staff;
use App\Models\Notification;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProposalNotificationService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public static function types(): array
    {
        return [
            'proposal-send-to-client' => [
                'name' => 'Send Proposal to Customer',
                'recipients' => 'client',
                'description' => 'Triggered when proposal is sent to the client',
            ],
            'proposal-client-accepted' => [
                'name' => 'Proposal Accepted by Client',
                'recipients' => 'staff',
                'description' => 'Triggered when client accepts the proposal',
            ],
            'proposal-client-declined' => [
                'name' => 'Proposal Declined by Client',
                'recipients' => 'staff',
                'description' => 'Triggered when client declines the proposal',
            ],
            'proposal-client-thank-you' => [
                'name' => 'Thank You for Proposal Acceptance',
                'recipients' => 'client',
                'description' => 'Sent to client after they accept the proposal',
            ],
            'proposal-comment-to-client' => [
                'name' => 'New Comment (to Client)',
                'recipients' => 'client',
                'description' => 'Triggered when staff adds a comment to a proposal',
            ],
            'proposal-comment-to-admin' => [
                'name' => 'New Comment (to Staff)',
                'recipients' => 'staff',
                'description' => 'Triggered when a comment is added to a proposal',
            ],
            'proposal-expiry-reminder' => [
                'name' => 'Proposal Expiry Reminder',
                'recipients' => 'staff',
                'description' => 'Reminder that a proposal is about to expire',
            ],
        ];
    }

    public function send(string $slug, int $proposalId, array $options = []): array
    {
        $types = static::types();
        if (!isset($types[$slug])) {
            return ['success' => false, 'message' => "Unknown notification slug: {$slug}"];
        }

        $proposal = Proposal::with(['currency', 'creator', 'assignedStaff', 'items'])->find($proposalId);

        if (!$proposal) {
            return ['success' => false, 'message' => "Proposal #{$proposalId} not found"];
        }

        $template = EmailTemplate::where('slug', $slug)->where('active', 1)->first();
        if (!$template) {
            return ['success' => false, 'message' => "Email template '{$slug}' is inactive or not found"];
        }

        $data = $this->buildData($proposal, $options);
        $parsed = $template->parse($data);

        $globalSubject = str_replace(
            ['{companyname}', '{crm_url}', '{email_signature}'],
            [
                SettingsHelper::get('companyname', ''),
                config('app.url'),
                SettingsHelper::get('email_signature', ''),
            ],
            $parsed['subject']
        );
        $globalMessage = str_replace(
            ['{companyname}', '{crm_url}', '{email_signature}'],
            [
                SettingsHelper::get('companyname', ''),
                config('app.url'),
                SettingsHelper::get('email_signature', ''),
            ],
            $parsed['message']
        );

        $recipientType = $types[$slug]['recipients'];
        $recipients = $options['recipients'] ?? $recipientType;

        $sent = [];
        $errors = [];

        if ($recipients === 'client' || $recipients === 'both') {
            $clientResult = $this->sendToClient($proposal, $template, $globalSubject, $globalMessage, $data, $options);
            if ($clientResult['success']) {
                $sent[] = $clientResult;
            } else {
                $errors[] = $clientResult;
            }
        }

        if ($recipients === 'staff' || $recipients === 'both') {
            $staffResult = $this->sendToStaff($proposal, $template, $globalSubject, $globalMessage, $data, $options);
            if ($staffResult['success']) {
                $sent[] = $staffResult;
            } else {
                $errors[] = $staffResult;
            }
        }

        if ($recipients === 'staff' || $recipients === 'both') {
            $this->createInAppNotifications($proposal, $slug, $options);
        }

        return [
            'success' => empty($errors),
            'sent' => $sent,
            'errors' => $errors,
            'message' => empty($errors)
                ? 'Notification sent successfully'
                : 'Some notifications failed',
        ];
    }

    protected function sendToClient(
        Proposal $proposal,
        EmailTemplate $template,
        string $subject,
        string $message,
        array $data,
        array $options = []
    ): array {
        $to = $proposal->email;
        if (!$to) {
            return ['success' => false, 'message' => 'No client email found', 'recipient' => 'client'];
        }

        try {
            $viewData = [
                'subject' => $subject,
                'body' => $message,
                'logo' => $this->getLogo(),
                'company_name' => SettingsHelper::get('companyname', ''),
                'company_address' => SettingsHelper::get('companyaddress', ''),
            ];

            Mail::send('emails.base', $viewData, function ($mail) use ($to, $template, $subject, $proposal) {
                $mail->to($to, $proposal->proposal_to);
                $mail->subject($subject);
                $mail->from(
                    $template->fromemail ?: SettingsHelper::get('smtp_email', config('mail.from.address')),
                    $template->fromname ? str_replace('{companyname}', SettingsHelper::get('companyname', ''), $template->fromname) : SettingsHelper::get('companyname', config('mail.from.name'))
                );
                if (!empty($options['reply_to'])) {
                    $mail->replyTo($options['reply_to']);
                }
            });

            return ['success' => true, 'recipient' => 'client', 'email' => $to];
        } catch (\Exception $e) {
            Log::error("Proposal notification failed for {$to}: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'recipient' => 'client', 'email' => $to];
        }
    }

    protected function sendToStaff(
        Proposal $proposal,
        EmailTemplate $template,
        string $subject,
        string $message,
        array $data,
        array $options = []
    ): array {
        $staffMembers = [];

        if ($proposal->assignedStaff && $proposal->assignedStaff->email) {
            $staffMembers[] = $proposal->assignedStaff;
        }
        if ($proposal->creator && $proposal->creator->email) {
            $staffMembers[] = $proposal->creator;
        }

        $admins = Staff::where('admin', 1)->get();
        foreach ($admins as $admin) {
            if (!collect($staffMembers)->contains('staffid', $admin->staffid)) {
                $staffMembers[] = $admin;
            }
        }

        if (empty($staffMembers)) {
            return ['success' => false, 'message' => 'No staff recipients found', 'recipient' => 'staff'];
        }

        $sent = [];
        foreach ($staffMembers as $staff) {
            try {
                $viewData = [
                    'subject' => $subject,
                    'body' => $message,
                    'logo' => $this->getLogo(),
                    'company_name' => SettingsHelper::get('companyname', ''),
                    'company_address' => SettingsHelper::get('companyaddress', ''),
                ];

                Mail::send('emails.base', $viewData, function ($mail) use ($staff, $template, $subject) {
                    $mail->to($staff->email, $staff->firstname . ' ' . $staff->lastname);
                    $mail->subject($subject);
                    $mail->from(
                        $template->fromemail ?: SettingsHelper::get('smtp_email', config('mail.from.address')),
                        $template->fromname ? str_replace('{companyname}', SettingsHelper::get('companyname', ''), $template->fromname) : SettingsHelper::get('companyname', config('mail.from.name'))
                    );
                });

                $sent[] = $staff->email;
            } catch (\Exception $e) {
                Log::error("Proposal staff notification failed for {$staff->email}: " . $e->getMessage());
            }
        }

        return ['success' => !empty($sent), 'recipient' => 'staff', 'emails' => $sent];
    }

    protected function createInAppNotifications(Proposal $proposal, string $slug, array $options = []): void
    {
        $description = $this->getNotificationDescription($proposal, $slug);
        $link = route('admin.proposals.show', $proposal->id);

        $staffIds = [];
        if ($proposal->assignedStaff) {
            $staffIds[] = $proposal->assignedStaff->staffid;
        }
        if ($proposal->creator) {
            $staffIds[] = $proposal->creator->staffid;
        }

        $admins = Staff::where('admin', 1)->pluck('staffid')->toArray();
        $staffIds = array_unique(array_merge($staffIds, $admins));
        $staffIds = array_filter($staffIds);

        if (!empty($staffIds)) {
            Notification::createForMultipleStaff($staffIds, $description, $link);
        }
    }

    protected function getNotificationDescription(Proposal $proposal, string $slug): string
    {
        return match ($slug) {
            'proposal-send-to-client' => "Proposal <b>{$proposal->subject}</b> sent to {$proposal->proposal_to}",
            'proposal-client-accepted' => "Proposal <b>{$proposal->subject}</b> was accepted by {$proposal->proposal_to}",
            'proposal-client-declined' => "Proposal <b>{$proposal->subject}</b> was declined by {$proposal->proposal_to}",
            'proposal-client-thank-you' => "Thank you for accepting proposal <b>{$proposal->subject}</b>",
            'proposal-comment-to-client' => "New comment on proposal <b>{$proposal->subject}</b>",
            'proposal-comment-to-admin' => "New comment on proposal <b>{$proposal->subject}</b>",
            'proposal-expiry-reminder' => "Proposal <b>{$proposal->subject}</b> is about to expire",
            default => "Notification for proposal <b>{$proposal->subject}</b>",
        };
    }

    protected function buildData(Proposal $proposal, array $options = []): array
    {
        $lineItemsHtml = '';
        foreach ($proposal->items as $item) {
            $lineItemsHtml .= '<tr>';
            $lineItemsHtml .= '<td style="padding:8px;border-bottom:1px solid #e2e8f0;">' . e($item->description) . '</td>';
            $lineItemsHtml .= '<td style="padding:8px;border-bottom:1px solid #e2e8f0;text-align:center;">' . (int)$item->qty . '</td>';
            $lineItemsHtml .= '<td style="padding:8px;border-bottom:1px solid #e2e8f0;text-align:right;">' . app_format_money($item->rate, $proposal->currency) . '</td>';
            $lineItemsHtml .= '<td style="padding:8px;border-bottom:1px solid #e2e8f0;text-align:right;">' . app_format_money($item->amount, $proposal->currency) . '</td>';
            $lineItemsHtml .= '</tr>';
        }

        return [
            'proposal_id' => (string)$proposal->id,
            'proposal_subject' => $proposal->subject,
            'proposal_to' => $proposal->proposal_to,
            'proposal_total' => app_format_money($proposal->total, $proposal->currency),
            'subtotal' => app_format_money($proposal->subtotal, $proposal->currency),
            'total_tax' => app_format_money($proposal->total_tax, $proposal->currency),
            'currency' => $proposal->currency->symbol ?? '$',
            'currency_code' => app_get_currency_code($proposal->currency),
            'proposal_link' => config('app.url') . '/proposals/' . $proposal->id . '/' . $proposal->hash,
            'staff_link' => route('admin.proposals.show', $proposal->id),
            'line_items' => $lineItemsHtml,
            'proposal_date' => $proposal->date ? \Carbon\Carbon::parse($proposal->date)->format('d M, Y') : '',
            'open_till' => $proposal->open_till ? \Carbon\Carbon::parse($proposal->open_till)->format('d M, Y') : '',
            'contact_firstname' => $proposal->proposal_to,
            'contact_lastname' => '',
            'company_name' => SettingsHelper::get('companyname', ''),
            'company_logo' => $this->getLogo(),
            'company_address' => SettingsHelper::get('companyaddress', ''),
            'email_signature' => SettingsHelper::get('email_signature', ''),
            'crm_url' => config('app.url'),
        ];
    }

    protected function getLogo(): string
    {
        $logo = SettingsHelper::get('company_logo', '');
        if ($logo && file_exists(public_path('uploads/' . $logo))) {
            return asset('uploads/' . $logo);
        }
        return '';
    }
}