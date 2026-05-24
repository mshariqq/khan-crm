<?php

namespace App\Events;

use App\Models\Task;
use App\Models\Staff;
use Illuminate\Foundation\Events\Dispatchable;

class TaskUnassigned
{
    use Dispatchable;

    public Task $task;
    public Staff $staff;

    public function __construct(Task $task, Staff $staff)
    {
        $this->task = $task;
        $this->staff = $staff;
    }
}
