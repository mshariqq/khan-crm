<?php

namespace App\Http\Requests;

class UpdateTaskRequest extends StoreTaskRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tasks_edit') ?? false;
    }
}
