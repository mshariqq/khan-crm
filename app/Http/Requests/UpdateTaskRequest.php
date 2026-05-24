<?php

namespace App\Http\Requests;

class UpdateTaskRequest extends StoreTaskRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tasks_edit') ?? false;
    }

    public function rules(): array
    {
        $rules = parent::rules();
        $rules['name'] = 'sometimes|required|string|max:255';
        $rules['priority'] = 'sometimes|required|integer|in:1,2,3,4';
        $rules['status'] = 'sometimes|required|integer|in:0,1,2,3';
        return $rules;
    }
}
