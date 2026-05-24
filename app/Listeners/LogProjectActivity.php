<?php

namespace App\Listeners;

use App\Events\ProjectCreated;
use App\Events\ProjectUpdated;
use App\Events\ProjectDeleted;
use App\Events\ProjectStatusChanged;
use App\Events\ProjectCompleted;
use App\Events\ProjectMemberAdded;
use App\Events\ProjectMemberRemoved;
use App\Events\ProjectFileUploaded;
use App\Events\ProjectFileDeleted;
use App\Events\DiscussionCreated;
use App\Events\DiscussionCommented;
use App\Events\MilestoneCreated;
use App\Events\MilestoneCompleted;
use App\Models\Project;
use App\Models\ProjectActivity;
use Illuminate\Support\Facades\Auth;

class LogProjectActivity
{
    public function handleProjectCreated(ProjectCreated $event): void
    {
        $this->log($event->project->id, 'project_activity_project_created', [
            'project_name' => $event->project->name,
        ]);
    }

    public function handleProjectUpdated(ProjectUpdated $event): void
    {
        $changes = $event->project->getChanges();
        unset($changes['updated_at']);

        if (!empty($changes)) {
            $this->log($event->project->id, 'project_activity_project_updated', [
                'project_name' => $event->project->name,
                'changes' => array_map(fn($key) => [
                    'old' => $event->project->getOriginal($key),
                    'new' => $event->project->{$key}
                ], array_keys($changes)),
            ]);
        }
    }

    public function handleProjectDeleted(ProjectDeleted $event): void
    {
        $this->log(null, 'project_activity_project_deleted', [
            'project_id' => $event->projectId,
            'project_name' => $event->projectName,
        ]);
    }

    public function handleProjectStatusChanged(ProjectStatusChanged $event): void
    {
        $labels = Project::statusLabels();
        $this->log($event->project->id, 'project_activity_status_updated', [
            'old_status' => $labels[$event->oldStatus] ?? $event->oldStatus,
            'new_status' => $labels[$event->newStatus] ?? $event->newStatus,
        ]);
    }

    public function handleProjectCompleted(ProjectCompleted $event): void
    {
        $this->log($event->project->id, 'project_activity_completed', [
            'project_name' => $event->project->name,
        ]);
    }

    public function handleProjectMemberAdded(ProjectMemberAdded $event): void
    {
        $this->log($event->project->id, 'project_activity_member_added', [
            'member_name' => $event->staff->firstname . ' ' . $event->staff->lastname,
        ]);
    }

    public function handleProjectMemberRemoved(ProjectMemberRemoved $event): void
    {
        $this->log($event->project->id, 'project_activity_member_removed', [
            'member_name' => $event->staff->firstname . ' ' . $event->staff->lastname,
        ]);
    }

    public function handleProjectFileUploaded(ProjectFileUploaded $event): void
    {
        $this->log($event->file->project_id, 'project_activity_file_uploaded', [
            'file_name' => $event->file->file_name,
        ]);
    }

    public function handleProjectFileDeleted(ProjectFileDeleted $event): void
    {
        $this->log($event->file->project_id, 'project_activity_file_deleted', [
            'file_name' => $event->file->file_name,
        ]);
    }

    public function handleDiscussionCreated(DiscussionCreated $event): void
    {
        $this->log($event->discussion->project_id, 'project_activity_discussion_created', [
            'discussion_subject' => $event->discussion->subject,
        ]);
    }

    public function handleDiscussionCommented(DiscussionCommented $event): void
    {
        $this->log($event->comment->discussion->project_id ?? null, 'project_activity_discussion_commented', [
            'discussion_id' => $event->comment->discussion_id,
            'comment_content' => substr($event->comment->content, 0, 100),
        ]);
    }

    public function handleMilestoneCreated(MilestoneCreated $event): void
    {
        $this->log($event->milestone->project_id, 'project_activity_milestone_created', [
            'milestone_name' => $event->milestone->name,
        ]);
    }

    public function handleMilestoneCompleted(MilestoneCompleted $event): void
    {
        $this->log($event->milestone->project_id, 'project_activity_milestone_completed', [
            'milestone_name' => $event->milestone->name,
        ]);
    }

    private function log(?int $projectId, string $descriptionKey, array $additionalData = []): void
    {
        try {
            ProjectActivity::create([
                'project_id' => $projectId,
                'staff_id' => Auth::id() ?? 0,
                'description_key' => $descriptionKey,
                'additional_data' => json_encode($additionalData),
                'dateadded' => now(),
                'visible_to_customer' => 1,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to log project activity: ' . $e->getMessage());
        }
    }
}
