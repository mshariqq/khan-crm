<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectEmailTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'type' => 'project',
                'slug' => 'project-created',
                'language' => 'english',
                'name' => 'Project Created',
                'subject' => 'New Project: {project_name}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>We are pleased to inform you that a new project has been created.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Status</td><td>{project_status}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Start Date</td><td>{start_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Deadline</td><td>{deadline}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Billing Type</td><td>{billing_type}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Budget</td><td>${project_cost}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{project_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Project</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'project-completed',
                'language' => 'english',
                'name' => 'Project Completed',
                'subject' => 'Project Completed: {project_name}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>We are pleased to inform you that the project <strong>{project_name}</strong> has been completed.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Tasks Completed</td><td>{completed_tasks} / {task_count}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Progress</td><td>{progress}</td></tr>
</table>
<p>Thank you for your collaboration throughout this project.</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'project-cancelled',
                'language' => 'english',
                'name' => 'Project Cancelled',
                'subject' => 'Project Cancelled: {project_name}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>This is to inform you that the project <strong>{project_name}</strong> has been cancelled.</p>
<p>If you have any questions regarding this decision, please contact the project team.</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'project-on-hold',
                'language' => 'english',
                'name' => 'Project On Hold',
                'subject' => 'Project On Hold: {project_name}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>This is to inform you that the project <strong>{project_name}</strong> has been placed on hold.</p>
<p>We will notify you when the project resumes. If you have any questions, please contact us.</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'project-resumed',
                'language' => 'english',
                'name' => 'Project Resumed',
                'subject' => 'Project Resumed: {project_name}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>We are pleased to inform you that the project <strong>{project_name}</strong> has resumed and is now active again.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Deadline</td><td>{deadline}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Progress</td><td>{progress}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{project_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Project</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'project-updated',
                'language' => 'english',
                'name' => 'Project Details Updated',
                'subject' => 'Project Updated: {project_name}',
                'message' => '<p>Hi Team,</p>
<p>The project <strong>{project_name}</strong> has been updated.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Status</td><td>{project_status}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Deadline</td><td>{deadline}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Progress</td><td>{progress}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{project_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View in CRM</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'project-deadline-changed',
                'language' => 'english',
                'name' => 'Project Deadline Changed',
                'subject' => 'Deadline Updated: {project_name}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>The deadline for project <strong>{project_name}</strong> has been updated.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Previous Deadline</td><td>{old_deadline}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">New Deadline</td><td>{new_deadline}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{project_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Project</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'project-member-added',
                'language' => 'english',
                'name' => 'Member Added to Project',
                'subject' => 'You have been added to project {project_name}',
                'message' => '<p>Hi {member_name},</p>
<p>You have been added as a team member to the project <strong>{project_name}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Client</td><td>{client_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Status</td><td>{project_status}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Deadline</td><td>{deadline}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{project_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">Open Project</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'project-member-removed',
                'language' => 'english',
                'name' => 'Member Removed from Project',
                'subject' => 'Removed from project {project_name}',
                'message' => '<p>Hi {member_name},</p>
<p>You have been removed from the project <strong>{project_name}</strong>.</p>
<p>If you believe this is an error, please contact the project administrator.</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'project-kickoff',
                'language' => 'english',
                'name' => 'Project Kickoff',
                'subject' => 'Project Kickoff: {project_name}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>We are excited to announce the kickoff of project <strong>{project_name}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Start Date</td><td>{start_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Deadline</td><td>{deadline}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Team Members</td><td>{staff_names}</td></tr>
</table>
<p>{project_description}</p>
<p style="text-align:center;margin:24px 0;">
<a href="{project_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Project</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'task-assigned',
                'language' => 'english',
                'name' => 'Task Assigned to Member',
                'subject' => 'New Task Assigned: {task_name}',
                'message' => '<p>Hi {member_name},</p>
<p>A new task has been assigned to you in project <strong>{project_name}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Task</td><td>{task_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Priority</td><td>{task_priority}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Due Date</td><td>{task_duedate}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{task_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Task</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'task-completed',
                'language' => 'english',
                'name' => 'Task Completed',
                'subject' => 'Task Completed: {task_name}',
                'message' => '<p>Hi Team,</p>
<p>Task <strong>{task_name}</strong> has been marked as completed in project <strong>{project_name}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Task</td><td>{task_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Completed Tasks</td><td>{completed_tasks} / {task_count}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{task_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Task</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'task-commented',
                'language' => 'english',
                'name' => 'Task Comment Added',
                'subject' => 'New Comment on Task: {task_name}',
                'message' => '<p>Hi Team,</p>
<p>{comment_author} has added a comment to task <strong>{task_name}</strong> in project <strong>{project_name}</strong>.</p>
<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px;margin:16px 0;">
<p style="margin:0;font-style:italic;">{comment_content}</p>
</div>
<p style="text-align:center;margin:24px 0;">
<a href="{task_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Discussion</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'milestone-created',
                'language' => 'english',
                'name' => 'Milestone Created',
                'subject' => 'New Milestone: {milestone_name}',
                'message' => '<p>Hi Team,</p>
<p>A new milestone has been added to project <strong>{project_name}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Milestone</td><td>{milestone_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Due Date</td><td>{milestone_duedate}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{project_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Project</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'milestone-completed',
                'language' => 'english',
                'name' => 'Milestone Completed',
                'subject' => 'Milestone Completed: {milestone_name}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>We are pleased to inform you that milestone <strong>{milestone_name}</strong> has been completed in project <strong>{project_name}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Milestone</td><td>{milestone_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Progress</td><td>{progress}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{project_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Project</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'project',
                'slug' => 'budget-threshold-reached',
                'language' => 'english',
                'name' => 'Budget Threshold Reached',
                'subject' => 'Budget Alert: {project_name}',
                'message' => '<p>Hi Team,</p>
<p>Project <strong>{project_name}</strong> has reached <strong>{budget_percent}%</strong> of its budget.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Project</td><td>{project_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Budget Used</td><td>${budget_spent}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Total Budget</td><td>${budget_total}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Percentage</td><td>{budget_percent}%</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{project_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View in CRM</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
        ];

        foreach ($templates as $template) {
            $existing = DB::table('tblemailtemplates')->where('slug', $template['slug'])->first();
            if (!$existing) {
                DB::table('tblemailtemplates')->insert($template);
            }
        }

        $this->command->info('Project email templates seeded successfully.');
    }
}
