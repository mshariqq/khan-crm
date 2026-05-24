<?php

namespace App\Observers;

use App\Models\Task;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Events\TaskDeleted;
use App\Events\TaskCompleted;
use App\Events\TaskReopened;

class TaskObserver
{
    public function created(Task $task): void
    {
        TaskCreated::dispatch($task);
    }

    public function updated(Task $task): void
    {
        $oldStatus = $task->getOriginal('status');
        $newStatus = $task->status;

        if ($oldStatus != $newStatus) {
            TaskUpdated::dispatch($task, $oldStatus, $newStatus);

            if ((int) $newStatus === Task::STATUS_COMPLETED) {
                TaskCompleted::dispatch($task);
            } elseif ((int) $oldStatus === Task::STATUS_COMPLETED && (int) $newStatus !== Task::STATUS_COMPLETED) {
                TaskReopened::dispatch($task);
            }
        }

        if ($oldStatus == $newStatus) {
            TaskUpdated::dispatch($task, $oldStatus, $newStatus);
        }
    }

    public function deleted(Task $task): void
    {
        TaskDeleted::dispatch($task->id, $task->name);
    }
}
