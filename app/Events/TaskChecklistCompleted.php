<?php

namespace App\Events;

use App\Models\TaskChecklistItem;
use Illuminate\Foundation\Events\Dispatchable;

class TaskChecklistCompleted
{
    use Dispatchable;

    public TaskChecklistItem $item;

    public function __construct(TaskChecklistItem $item)
    {
        $this->item = $item;
    }
}
