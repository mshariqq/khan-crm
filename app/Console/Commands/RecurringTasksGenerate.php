<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\RecurringTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecurringTasksGenerate extends Command
{
    protected $signature = 'recurring-tasks:generate';
    protected $description = 'Generate new tasks from recurring task templates';

    public function handle(): int
    {
        $this->warn('Recurring tasks generation not yet implemented — requires RecurringTask model.');
        return Command::SUCCESS;
    }
}
