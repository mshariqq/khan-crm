<?php

namespace App\Console\Commands;

use App\Models\ProjectRecurring;
use App\Services\ProjectService;
use Illuminate\Console\Command;

class GenerateRecurringProjects extends Command
{
    protected $signature = 'projects:generate-recurring';
    protected $description = 'Generate recurring projects that are due for creation';

    public function handle(ProjectService $projectService)
    {
        $configs = ProjectRecurring::active()
            ->where('next_generation_date', '<=', now())
            ->get();

        $generated = 0;
        foreach ($configs as $config) {
            try {
                $project = $projectService->generateRecurringProject($config);
                if ($project) {
                    $this->info("Generated project: {$project->name}");
                    $generated++;
                }
            } catch (\Exception $e) {
                $this->error("Failed: {$e->getMessage()}");
            }
        }

        $this->info("Generated {$generated} recurring project(s).");
        return Command::SUCCESS;
    }
}
