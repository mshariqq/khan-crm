<?php

namespace App\Observers;

use App\Models\Project;
use App\Events\ProjectCreated;
use App\Events\ProjectUpdated;
use App\Events\ProjectDeleted;
use App\Events\ProjectStatusChanged;
use App\Events\ProjectCompleted;

class ProjectObserver
{
    public function created(Project $project): void
    {
        ProjectCreated::dispatch($project);
    }

    public function updated(Project $project): void
    {
        $oldStatus = $project->getOriginal('status');
        $newStatus = $project->status;

        ProjectUpdated::dispatch($project, $oldStatus, $newStatus);

        if ($oldStatus != $newStatus) {
            ProjectStatusChanged::dispatch($project, $oldStatus, $newStatus);

            if ((int) $newStatus === Project::STATUS_COMPLETED) {
                ProjectCompleted::dispatch($project);
            }
        }
    }

    public function deleted(Project $project): void
    {
        if (!$project->isForceDeleting()) {
            ProjectDeleted::dispatch($project->id, $project->name);
        }
    }
}
