<?php

namespace App\Events;

use App\Models\TaskTimer;
use Illuminate\Foundation\Events\Dispatchable;

class TaskTimerStopped
{
    use Dispatchable;

    public TaskTimer $timer;

    public function __construct(TaskTimer $timer)
    {
        $this->timer = $timer;
    }
}
