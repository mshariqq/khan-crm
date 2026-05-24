<?php

namespace App\Services;

use App\Models\Project;
use App\Models\EmailTemplate;
use App\Models\Staff;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProjectNotificationService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public static function types(): array
    {
        return [
            'project-created' => [
                'name' => 'Project Created',
                'recipients' => 'both',
                'description' => 'Triggered when a new project is created',
            ],
            'project-completed' => [
                'name' => 'Project Completed',
                'recipients' => 'both',
                'description' => 'Triggered when project status changes to Completed',
            ],
            'project-cancelled' => [
                'name' => 'Project Cancelled',
                'recipients' => 'both',
                'description' => 'Triggered when project status changes to Cancelled',
            ],
            'project-on-hold' => [
                'name' => 'Project On Hold',
                'recipients' => 'both',
                'description' => 'Triggered when project status changes to On Hold',
            ],
            'project-resumed' => [
                'name' => 'Project Resumed',
                'recipients' => 'both',
                'description' => 'Triggered when project comes off hold back to Active',
            ],
            'project-updated' => [
                'name' => 'Project Details Updated',
                'recipients' => 'staff',
                'description' => 'Triggered when project details are modified',
            ],
            'project-deadline-changed' => [
                'name' => 'Project Deadline Changed',
                'recipients' => 'both',
                'description' => 'Triggered when project deadline is modified',
            ],
            'project-member-added' => [
                'name' => 'Member Added to Project',
                'recipients' => 'staff',
                'description' => 'Triggered when a staff member is added to the project team',
            ],
            'project-member-removed' => [
                'name' => 'Member Removed from Project',
                'recipients' => 'staff',
                'description' => 'Triggered when a staff member is removed from the project team',
            ],
            'project-kickoff' => [
                'name' => 'Project Kickoff',
                'recipients' => 'both',
                'description' => 'Formal kickoff notification to client and team',
            ],
            'task-assigned' => [
                'name' => 'Task Assigned to Member',
                'recipients' => 'staff',
                'description' => 'Triggered when a task is assigned to a staff member',
            ],
            'task-completed' => [
                'name' => 'Task Completed',
                'recipients' => 'staff',
                'description' => 'Triggered when a task status changes to Done',
            ],
            'task-commented' => [
                'name' => 'Task Comment Added',
                'recipients' => 'staff',
                'description' => 'Triggered when a comment is added to a task',
            ],
            'milestone-created' => [
                'name' => 'Milestone Created',
                'recipients' => 'staff',
                'description' => 'Triggered when a new milestone is added',
            ],
            'milestone-completed' => [
                'name' => 'Milestone Completed',
                'recipients' => 'both',
                'description' => 'Triggered when a milestone is completed',
            ],
            'budget-threshold-reached' => [
                'name' => 'Budget Threshold Reached',
                'recipients' => 'staff',
                'description' => 'Triggered when project cost reaches threshold percentage',
            ],
            'project-inactivity-alert' => [
                'name' => 'Project Inactivity Alert',
                'recipients' => 'staff',
                'description' => 'Triggered when no activity logged for X days',
            ],
        ];
    }

    public function send(string $slug, int $projectId, array $options = []): array
    {
        $types = static::types();
        if (!isset($types[$slug])) {
            return ['success' => false, 'message' => "Unknown notification slug: {$slug}"];
        }

        $project = Project::with([
            'client.primaryContact', 'staff', 'creator', 'tasks', 'milestones'
        ])->find($projectId);

        if (!$project) {
            return ['success' => false, 'message' => "Project #{$projectId} not found"];
        }

        $template = EmailTemplate::where('slug', $slug)->where('active', 1)->first();
        if (!$template) {
            return ['success' => false, 'message' => "Email template '{$slug}' is inactive or not found"];
        }

        $data = $this->buildData($project, $options);
        if ($project->client) {
            $data['_rate_limit_type'] = 'client';
            $data['_rate_limit_id'] = $project->client->userid;
            $data['_rate_recipient_type'] = 'client';
            $data['_rate_max_per_hour'] = (int)\App\Helpers\SettingsHelper::get('email_rate_limit_per_client', 50);
        }
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
            $clientResult = $this->sendToClient($project, $template, $globalSubject, $globalMessage, $data, $options);
            if ($clientResult['success']) {
                $sent[] = $clientResult;
            } else {
                $errors[] = $clientResult;
            }
        }

        if ($recipients === 'staff' || $recipients === 'both') {
            $staffResult = $this->sendToStaff($project, $template, $globalSubject, $globalMessage, $data, $options);
            if ($staffResult['success']) {
                $sent[] = $staffResult;
            } else {
                $errors[] = $staffResult;
            }
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
        Project $project,
        EmailTemplate $template,
        string $subject,
        string $message,
        array $data,
        array $options = []
    ): array {
        $client = $project->client;
        if (!$client) {
            return ['success' => false, 'message' => 'No client associated with project', 'recipient' => 'client'];
        }

        if (!$project->contact_notification) {
            return ['success' => false, 'message' => 'Contact notifications disabled for this project', 'recipient' => 'client'];
        }

        $contacts = $client->contacts;
        if ($contacts->isEmpty()) {
            return ['success' => false, 'message' => 'No client contacts found', 'recipient' => 'client'];
        }

        $sent = [];
        foreach ($contacts as $contact) {
            if (!$contact->email) continue;

            if (!empty($project->notify_contacts) && is_array($project->notify_contacts)) {
                if (!in_array($contact->id, $project->notify_contacts)) continue;
            }

            try {
                $viewData = [
                    'subject' => $subject,
                    'body' => $message,
                    'logo' => $this->getLogo(),
                    'company_name' => SettingsHelper::get('companyname', ''),
                    'company_address' => SettingsHelper::get('companyaddress', ''),
                ];

                Mail::send('emails.base', $viewData, function ($mail) use ($contact, $template, $subject, $options) {
                    $mail->to($contact->email, $contact->firstname . ' ' . $contact->lastname);
                    $mail->subject($subject);
                    $mail->from(
                        $template->fromemail ?: SettingsHelper::get('smtp_email', config('mail.from.address')),
                        $template->fromname ? str_replace('{companyname}', SettingsHelper::get('companyname', ''), $template->fromname) : SettingsHelper::get('companyname', config('mail.from.name'))
                    );
                    if (!empty($options['reply_to'])) {
                        $mail->replyTo($options['reply_to']);
                    }
                });

                $this->logActivity($project->id, 'Email sent to <b>' . $contact->email . '</b> — ' . $template->name);
                $sent[] = $contact->email;
            } catch (\Exception $e) {
                Log::error("Project client notification failed for {$contact->email}: " . $e->getMessage());
            }
        }

        return [
            'success' => !empty($sent),
            'recipient' => 'client',
            'emails' => $sent,
        ];
    }

    protected function sendToStaff(
        Project $project,
        EmailTemplate $template,
        string $subject,
        string $message,
        array $data,
        array $options = []
    ): array {
        $staffMembers = collect();

        foreach ($project->staff as $member) {
            if ($member->email) {
                $staffMembers->push($member);
            }
        }

        if ($project->creator && $project->creator->email) {
            if (!$staffMembers->contains('staffid', $project->creator->staffid)) {
                $staffMembers->push($project->creator);
            }
        }

        $admins = Staff::where('admin', 1)->get();
        foreach ($admins as $admin) {
            if (!$staffMembers->contains('staffid', $admin->staffid)) {
                $staffMembers->push($admin);
            }
        }

        $staffMembers = $staffMembers->unique('staffid');

        if ($staffMembers->isEmpty()) {
            return ['success' => false, 'message' => 'No staff recipients found', 'recipient' => 'staff'];
        }

        $sent = [];
        $errors = [];
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
                Log::error("Project staff notification failed for {$staff->email}: " . $e->getMessage());
                $errors[] = $staff->email;
            }
        }

        if (!empty($sent)) {
            $this->logActivity($project->id, 'Staff notification sent to ' . implode(', ', $sent) . ' — ' . $template->name);
        }

        return [
            'success' => !empty($sent),
            'recipient' => 'staff',
            'emails' => $sent,
            'errors' => $errors,
        ];
    }

    public function sendToOneStaff(string $slug, int $projectId, Staff $staffMember, array $options = []): array
    {
        $project = Project::with(['client.primaryContact', 'staff', 'creator'])->find($projectId);
        if (!$project) {
            return ['success' => false, 'message' => "Project #{$projectId} not found"];
        }

        if (!$staffMember->email) {
            return ['success' => false, 'message' => 'Staff member has no email'];
        }

        $types = static::types();
        if (!isset($types[$slug])) {
            return ['success' => false, 'message' => "Unknown notification slug: {$slug}"];
        }

        $template = EmailTemplate::where('slug', $slug)->where('active', 1)->first();
        if (!$template) {
            return ['success' => false, 'message' => "Email template '{$slug}' is inactive or not found"];
        }

        $data = $this->buildData($project, $options);
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

        try {
            $viewData = [
                'subject' => $globalSubject,
                'body' => $globalMessage,
                'logo' => $this->getLogo(),
                'company_name' => SettingsHelper::get('companyname', ''),
                'company_address' => SettingsHelper::get('companyaddress', ''),
            ];

            Mail::send('emails.base', $viewData, function ($mail) use ($staffMember, $template, $globalSubject) {
                $mail->to($staffMember->email, $staffMember->firstname . ' ' . $staffMember->lastname);
                $mail->subject($globalSubject);
                $mail->from(
                    $template->fromemail ?: SettingsHelper::get('smtp_email', config('mail.from.address')),
                    $template->fromname ? str_replace('{companyname}', SettingsHelper::get('companyname', ''), $template->fromname) : SettingsHelper::get('companyname', config('mail.from.name'))
                );
            });

            return ['success' => true, 'recipient' => $staffMember->email];
        } catch (\Exception $e) {
            Log::error("Project one-to-one notification failed for {$staffMember->email}: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'recipient' => $staffMember->email];
        }
    }

    protected function buildData(Project $project, array $options = []): array
    {
        $creator = $project->creator;
        $creatorName = $creator ? trim(($creator->firstname ?? '') . ' ' . ($creator->lastname ?? '')) : 'System';
        $companyName = SettingsHelper::get('companyname', config('app.name'));

        return array_merge([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'project_description' => $project->description ?? '',
            'project_status' => $project->status,
            'project_status_text' => Project::statusLabels()[$project->status] ?? 'Unknown',
            'project_start_date' => $project->start_date ? $project->start_date->format('d M, Y') : 'Not set',
            'project_deadline' => $project->deadline ? $project->deadline->format('d M, Y') : 'Not set',
            'project_progress' => $project->progress . '%',
            'project_cost' => $project->project_cost ? number_format($project->project_cost, 2) : '0.00',
            'billing_type' => $project->billing_type,
            'estimated_hours' => $project->estimated_hours ?? '0',
            'client_company' => $project->client->company ?? 'N/A',
            'client_email' => $project->client->email ?? '',
            'client_phonenumber' => $project->client->phonenumber ?? '',
            'project_link' => route('admin.projects.show', $project->id),
            'client_portal_link' => $project->client ? route('client.projects.show', $project->id) : '',
            'creator_name' => $creatorName,
            'creator_email' => $creator->email ?? '',
            'company_name' => $companyName,
            'crm_url' => config('app.url'),
        ], $options);
    }

    protected function getCreatorData(Project $project): array
    {
        $creator = $project->creator;
        $firstName = $creator->firstname ?? '';
        $lastName = $creator->lastname ?? '';
        return [
            'full_name' => trim($firstName . ' ' . $lastName) ?: 'System',
            'email' => $creator->email ?? '',
        ];
    }

    protected function getLogo(): string
    {
        $logoPath = SettingsHelper::get('company_logo', '');
        if ($logoPath && file_exists(public_path('uploads/' . $logoPath))) {
            return asset('uploads/' . $logoPath);
        }
        return '';
    }

    protected function logActivity(int $projectId, string $message): void
    {
        try {
            \App\Models\ProjectActivity::create([
                'project_id' => $projectId,
                'staff_id' => 0,
                'description_key' => 'project_activity_email_sent',
                'additional_data' => json_encode(['message' => $message]),
                'dateadded' => now(),
                'visible_to_customer' => 0,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log notification activity: ' . $e->getMessage());
        }
    }
}
