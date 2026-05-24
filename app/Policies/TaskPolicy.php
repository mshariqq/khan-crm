<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\Task;

class TaskPolicy
{
    public function view(Staff $user): bool
    {
        return $user->can('tasks_view');
    }

    public function create(Staff $user): bool
    {
        return $user->can('tasks_create');
    }

    public function update(Staff $user, Task $task): bool
    {
        if ($user->can('tasks_edit_all')) {
            return true;
        }

        return $user->can('tasks_edit') && $task->assignedStaff()->where('staffid', $user->staffid)->exists();
    }

    public function delete(Staff $user, Task $task): bool
    {
        if ($user->can('tasks_delete_all')) {
            return true;
        }

        return $user->can('tasks_delete') && $task->assignedStaff()->where('staffid', $user->staffid)->exists();
    }
}
