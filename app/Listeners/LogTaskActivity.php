<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Events\TaskDeleted;
use App\Services\AuditService;

class LogTaskActivity
{
    public function __construct(private AuditService $audit) {}

    public function handle(TaskCreated|TaskUpdated|TaskDeleted $event): void
    {
        match (true) {
            $event instanceof TaskCreated => $this->audit->log(
                'task_created',
                "Task created: {$event->task->name}",
                'Task',
                $event->task->id,
                null,
                $event->task->toArray(),
            ),
            $event instanceof TaskUpdated => $this->audit->log(
                'task_updated',
                "Task updated: {$event->task->name}",
                'Task',
                $event->task->id,
                ['status' => $event->oldStatus],
                ['status' => $event->newStatus],
            ),
            $event instanceof TaskDeleted => $this->audit->log(
                'task_deleted',
                "Task deleted: {$event->taskName}",
                'Task',
                $event->taskId,
                null,
                ['name' => $event->taskName],
            ),
        };
    }
}
