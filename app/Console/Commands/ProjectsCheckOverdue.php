<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\ProjectNotificationService;
use Illuminate\Console\Command;

class ProjectsCheckOverdue extends Command
{
    protected $signature = 'projects:check-overdue';
    protected $description = 'Check for overdue projects and send alerts';

    public function handle(ProjectNotificationService $notificationService): int
    {
        $overdueProjects = Project::whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->whereNotIn('status', [Project::STATUS_COMPLETED, Project::STATUS_CANCELLED])
            ->get();

        $count = 0;
        foreach ($overdueProjects as $project) {
            $notificationService->send('project-inactivity-alert', $project->id);
            $count++;
        }

        $this->info("Sent overdue alerts for {$count} projects.");

        return Command::SUCCESS;
    }
}
