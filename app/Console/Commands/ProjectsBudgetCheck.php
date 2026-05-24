<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Console\Command;

class ProjectsBudgetCheck extends Command
{
    protected $signature = 'projects:budget-check';
    protected $description = 'Check project budgets and send threshold alerts';

    public function handle(ProjectService $projectService): int
    {
        $projects = Project::whereNotNull('project_cost')
            ->where('project_cost', '>', 0)
            ->whereNotIn('status', [Project::STATUS_COMPLETED, Project::STATUS_CANCELLED])
            ->get();

        $count = 0;
        foreach ($projects as $project) {
            $result = $projectService->handleBudgetTracking($project);
            if ($result) {
                $count++;
            }
        }

        $this->info("Budget check complete. {$count} projects exceeded threshold.");

        return Command::SUCCESS;
    }
}
