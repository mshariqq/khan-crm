<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class NotificationsCleanup extends Command
{
    protected $signature = 'notifications:cleanup {--days=90 : Delete notifications older than this many days}';
    protected $description = 'Clean up old in-app notifications';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deleted = Notification::where('date', '<', $cutoff)->delete();

        $this->info("Deleted {$deleted} notifications older than {$days} days.");

        return Command::SUCCESS;
    }
}
