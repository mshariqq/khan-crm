<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('projects:check-overdue')->daily();
        $schedule->command('projects:update-progress')->daily();
        $schedule->command('projects:budget-check')->weekly();
        $schedule->command('notifications:cleanup')->daily();
        $schedule->command('projects:cleanup-activity', ['--days' => 90])->monthly();
        $schedule->command('projects:archive-completed', ['--days' => 90])->monthly();
        $schedule->command('invoices:check-overdue')->daily();
        $schedule->command('invoices:generate-recurring')->daily();
        $schedule->command('recurring-tasks:generate')->daily();
        $schedule->command('projects:generate-recurring')->daily();
        $schedule->command('discussions:fetch-emails')->everyFiveMinutes();
        $schedule->command('files:cleanup-expired')->daily();
        $schedule->command('files:scan-virus')->hourly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
