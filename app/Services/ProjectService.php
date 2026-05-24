<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Staff;
use App\Models\Notification;
use App\Models\ProjectActivity;
use App\Services\ProjectNotificationService;
use Illuminate\Support\Facades\Auth;

class ProjectService extends BaseService
{
    protected ProjectNotificationService $projectNotification;

    public function __construct(ProjectNotificationService $projectNotification)
    {
        $this->projectNotification = $projectNotification;
    }

    public function syncStaffAndNotify(Project $project, array $newStaffIds): void
    {
        $oldStaff = $project->staff()->pluck('staffid')->toArray();
        $project->staff()->sync($newStaffIds);

        $addedStaff = array_diff($newStaffIds, $oldStaff);
        foreach ($addedStaff as $staffId) {
            $staffMember = Staff::find($staffId);
            if ($staffMember) {
                $this->projectNotification->sendToOneStaff('project-member-added', $project->id, $staffMember, [
                    'member_name' => $staffMember->firstname . ' ' . $staffMember->lastname,
                    'member_email' => $staffMember->email,
                    'added_by' => (Auth::user()->firstname ?? '') . ' ' . (Auth::user()->lastname ?? ''),
                ]);

                Notification::createForStaff($staffMember->staffid, Auth::user()->full_name . ' added you to project <strong>' . e($project->name) . '</strong>', route('admin.projects.show', $project->id));
            }
        }

        if ($oldStaff !== $newStaffIds) {
            ProjectActivity::create([
                'project_id' => $project->id,
                'staff_id' => Auth::id(),
                'description_key' => 'project_activity_team_updated',
                'additional_data' => json_encode([
                    'old_staff_count' => count($oldStaff),
                    'new_staff_count' => count($newStaffIds),
                ]),
                'dateadded' => now(),
            ]);
        }
    }

    public function handleStatusChange(Project $project, int $oldStatus, int $newStatus): bool
    {
        $notified = false;
        $statusLabels = [
            Project::STATUS_ACTIVE => 'Active',
            Project::STATUS_COMPLETED => 'Completed',
            Project::STATUS_ON_HOLD => 'On Hold',
            Project::STATUS_CANCELLED => 'Cancelled'
        ];
        $statusSlugs = [
            Project::STATUS_COMPLETED => 'project-completed',
            Project::STATUS_ON_HOLD => 'project-on-hold',
            Project::STATUS_CANCELLED => 'project-cancelled'
        ];

        if ($oldStatus !== $newStatus) {
            if (isset($statusSlugs[$newStatus])) {
                $this->projectNotification->send($statusSlugs[$newStatus], $project->id);
                $notified = true;
            } elseif ($newStatus === Project::STATUS_ACTIVE && in_array($oldStatus, [Project::STATUS_ON_HOLD, Project::STATUS_CANCELLED])) {
                $this->projectNotification->send('project-resumed', $project->id);
                $notified = true;
            }

            $oldLabel = $statusLabels[$oldStatus] ?? 'Unknown';
            $newLabel = $statusLabels[$newStatus] ?? 'Unknown';
            $this->notifyStaffExceptSelf($project, 'Project <strong>' . e($project->name) . '</strong> moved from ' . $oldLabel . ' to ' . $newLabel);
        }

        return $notified;
    }

    public function handleDeadlineChange(Project $project, ?string $oldDeadline, ?string $newDeadline): bool
    {
        if ($oldDeadline === $newDeadline) {
            return false;
        }

        $this->projectNotification->send('project-deadline-changed', $project->id);
        $newDate = $newDeadline ? \Carbon\Carbon::parse($newDeadline)->format('M j, Y') : 'Not set';
        $this->notifyStaffExceptSelf($project, 'Deadline for <strong>' . e($project->name) . '</strong> changed to ' . $newDate);
        return true;
    }

    public function notifyGeneralUpdate(Project $project): void
    {
        $this->projectNotification->send('project-updated', $project->id);
        $this->notifyStaffExceptSelf($project, Auth::user()->full_name . ' updated project <strong>' . e($project->name) . '</strong>');
    }

    public function logProjectActivity(Project $project): void
    {
        $changedAttributes = $project->getChanges();
        unset($changedAttributes['updated_at']);

        if (!empty($changedAttributes)) {
            ProjectActivity::create([
                'project_id' => $project->id,
                'staff_id' => Auth::id(),
                'description_key' => 'project_activity_project_updated',
                'additional_data' => json_encode([
                    'project_name' => $project->name,
                    'changes' => array_map(function($key) use ($project) {
                        return ['old' => $project->getOriginal($key), 'new' => $project->{$key}];
                    }, array_keys($changedAttributes))
                ]),
                'dateadded' => now(),
            ]);
        }
    }

    private function notifyStaffExceptSelf(Project $project, string $description): void
    {
        $staffIds = $project->staff()->pluck('staffid')->toArray();
        $exclude = [(int)Auth::id()];
        $recipients = array_values(array_diff($staffIds, $exclude));
        if (!empty($recipients)) {
            Notification::createForMultipleStaff($recipients, $description, route('admin.projects.show', $project->id));
        }
    }
}
