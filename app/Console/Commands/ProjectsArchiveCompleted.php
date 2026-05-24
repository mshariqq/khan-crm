<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

class ProjectsArchiveCompleted extends Command
{
    protected $signature = 'projects:archive-completed {--days=30 : Archive projects completed more than N days ago}';
    protected $description = 'Archive old completed projects by setting status to On Hold';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $count = Project::where('status', Project::STATUS_COMPLETED)
            ->where('date_finished', '<', $cutoff)
            ->update(['status' => Project::STATUS_ON_HOLD]);

        $this->info("Archived {$count} completed projects older than {$days} days.");

        return Command::SUCCESS;
    }
}
