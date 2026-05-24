<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Console\Command;

class ProjectsUpdateProgress extends Command
{
    protected $signature = 'projects:update-progress';
    protected $description = 'Auto-calculate project progress from task completion';

    public function handle(): int
    {
        $projects = Project::where('progress_from_tasks', 1)->get();

        $count = 0;
        foreach ($projects as $project) {
            $total = $project->tasks()->count();
            if ($total === 0) continue;

            $completed = $project->tasks()->where('status', Task::STATUS_COMPLETED)->count();
            $progress = (int) round(($completed / $total) * 100);

            $updateData = ['progress' => $progress];
            if ($progress >= 100 && $project->status !== Project::STATUS_COMPLETED) {
                $updateData['status'] = Project::STATUS_COMPLETED;
                $updateData['date_finished'] = now();
            }

            $project->update($updateData);
            $count++;
        }

        $this->info("Updated progress for {$count} projects.");

        return Command::SUCCESS;
    }
}
