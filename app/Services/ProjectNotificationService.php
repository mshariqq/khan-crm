<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
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

        $contact = $client->primaryContact;
        $toEmail = $contact->email ?? $client->email ?? null;
        $toName = $contact ? ($contact->firstname . ' ' . $contact->lastname) : $client->company;

        if (!$toEmail) {
            return ['success' => false, 'message' => 'No client contact email found', 'recipient' => 'client'];
        }

        try {
            $viewData = [
                'subject' => $subject,
                'body' => $message,
                'logo' => $this->getLogo(),
                'company_name' => SettingsHelper::get('companyname', ''),
                'company_address' => SettingsHelper::get('companyaddress', ''),
            ];

            Mail::send('emails.base', $viewData, function ($mail) use ($toEmail, $toName, $template, $subject, $options) {
                $mail->to($toEmail, $toName);
                $mail->subject($subject);
                $mail->from(
                    $template->fromemail ?: SettingsHelper::get('smtp_email', config('mail.from.address')),
                    $template->fromname ? str_replace('{companyname}', SettingsHelper::get('companyname', ''), $template->fromname) : SettingsHelper::get('companyname', config('mail.from.name'))
                );
                if (!empty($options['reply_to'])) {
                    $mail->replyTo($options['reply_to']);
                }
            });

            $this->logActivity($project->id, 'Email sent to <b>' . $toEmail . '</b> — ' . $template->name);

            return ['success' => true, 'recipient' => 'client', 'email' => $toEmail];
        } catch (\Exception $e) {
            Log::error("Project notification failed for {$toEmail}: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'recipient' => 'client', 'email' => $toEmail];
        }
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

        if ($staffMembers->isEmpty()) {
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
                Log::error("Project staff notification failed for {$staff->email}: " . $e->getMessage());
            }
        }

        if (!empty($sent)) {
            $this->logActivity($project->id, 'Staff notification sent to ' . implode(', ', $sent) . ' — ' . $template->name);
        }

        return ['success' => !empty($sent), 'recipient' => 'staff', 'emails' => $sent];
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

        $template = EmailTemplate::where('slug', $slug)->where('active', 1)->first();
        if (!$template) {
            return ['success' => false, 'message' => "Email template '{$slug}' is inactive or not found"];
        }

        $data = $this->buildData($project, $options);
        $parsed = $template->parse($data);

        $globalSubject = str_replace(
            ['{companyname}', '{crm_url}', '{email_signature}'],
            [SettingsHelper::get('companyname', ''), config('app.url'), SettingsHelper::get('email_signature', '')],
            $parsed['subject']
        );
        $globalMessage = str_replace(
            ['{companyname}', '{crm_url}', '{email_signature}'],
            [SettingsHelper::get('companyname', ''), config('app.url'), SettingsHelper::get('email_signature', '')],
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

            $this->logActivity($project->id, 'Email sent to <b>' . $staffMember->email . '</b> — ' . $template->name);
            return ['success' => true, 'email' => $staffMember->email];
        } catch (\Exception $e) {
            Log::error("Project staff notification failed: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function buildData(Project $project, array $options = []): array
    {
        $client = $project->client;
        $contact = $client ? $client->primaryContact : null;

        $statusMap = [Project::STATUS_ACTIVE => 'Active', Project::STATUS_COMPLETED => 'Completed', Project::STATUS_ON_HOLD => 'On Hold', Project::STATUS_CANCELLED => 'Cancelled'];
        $billingTypes = [1 => 'Fixed Rate', 2 => 'Project Hours', 3 => 'Task Hours'];

        $completedTasks = $project->tasks->where('status', Task::STATUS_COMPLETED)->count();
        $totalTasks = $project->tasks->count();

        $staffNames = $project->staff->pluck('firstname')->implode(', ');

        return array_merge([
            'project_name' => $project->name,
            'project_description' => $project->description ?? '',
            'project_status' => $statusMap[$project->status] ?? 'Unknown',
            'project_status_code' => (string)$project->status,
            'project_id' => (string)$project->id,
            'client_name' => $client->company ?? '',
            'client_company' => $client->company ?? '',
            'contact_firstname' => $contact->firstname ?? $client->company ?? '',
            'contact_lastname' => $contact->lastname ?? '',
            'start_date' => $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M, Y') : '',
            'deadline' => $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M, Y') : '',
            'old_deadline' => $options['old_deadline'] ?? '',
            'new_deadline' => $options['new_deadline'] ?? '',
            'billing_type' => $billingTypes[$project->billing_type] ?? 'N/A',
            'project_cost' => number_format($project->project_cost ?? 0, 2),
            'hourly_rate' => number_format($project->project_rate_per_hour ?? 0, 2),
            'estimated_hours' => (string)($project->estimated_hours ?? '0'),
            'progress' => (string)$project->progress . '%',
            'progress_number' => (string)($project->progress ?? 0),
            'project_link' => route('admin.projects.show', $project->id),
            'client_project_link' => $client ? (config('app.url') . '/client/projects/' . $project->id) : '',
            'staff_names' => $staffNames,
            'staff_count' => (string)$project->staff->count(),
            'task_count' => (string)$totalTasks,
            'completed_tasks' => (string)$completedTasks,
            'milestone_count' => (string)$project->milestones->count(),
            'member_name' => $options['member_name'] ?? '',
            'member_email' => $options['member_email'] ?? '',
            'added_by' => $options['added_by'] ?? '',
            'task_name' => $options['task_name'] ?? '',
            'task_priority' => $options['task_priority'] ?? '',
            'task_duedate' => $options['task_duedate'] ?? '',
            'task_status' => $options['task_status'] ?? '',
            'task_link' => $options['task_link'] ?? '',
            'milestone_name' => $options['milestone_name'] ?? '',
            'milestone_duedate' => $options['milestone_duedate'] ?? '',
            'comment_author' => $options['comment_author'] ?? '',
            'comment_content' => $options['comment_content'] ?? '',
            'budget_percent' => $options['budget_percent'] ?? '',
            'budget_spent' => $options['budget_spent'] ?? '',
            'budget_total' => $options['budget_total'] ?? '',
            'company_name' => SettingsHelper::get('companyname', ''),
            'company_logo' => $this->getLogo(),
            'company_address' => SettingsHelper::get('companyaddress', ''),
            'old_status' => $options['old_status'] ?? '',
            'new_status' => $options['new_status'] ?? '',
        ], $this->getCreatorData($project));
    }

    protected function getCreatorData(Project $project): array
    {
        return [
            'created_by_name' => $project->creator->firstname ?? '' . ' ' . $project->creator->lastname ?? '',
            'created_by_email' => $project->creator->email ?? '',
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

    protected function logActivity(int $projectId, string $description): void
    {
        try {
            \App\Models\ProjectActivity::create([
                'project_id' => $projectId,
                'staff_id' => auth()->id() ?? 0,
                'description_key' => 'notification_sent',
                'additional_data' => json_encode(['description' => $description]),
                'dateadded' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning("Failed to log project activity: " . $e->getMessage());
        }
    }
}
