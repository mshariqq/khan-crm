<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;

class TaskReopened
{
    use Dispatchable;

    public Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
