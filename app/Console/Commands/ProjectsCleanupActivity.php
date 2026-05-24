<?php

namespace App\Console\Commands;

use App\Models\ProjectActivity;
use Illuminate\Console\Command;

class ProjectsCleanupActivity extends Command
{
    protected $signature = 'projects:cleanup-activity {--days=90 : Delete activity older than N days}';
    protected $description = 'Clean up old project activity logs';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deleted = ProjectActivity::where('dateadded', '<', $cutoff)->delete();

        $this->info("Deleted {$deleted} activity records older than {$days} days.");

        return Command::SUCCESS;
    }
}
