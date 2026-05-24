<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tasks_create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'priority' => 'required|integer',
            'status' => 'required|integer',
            'description' => 'nullable|string',
            'startdate' => 'nullable|date',
            'duedate' => 'nullable|date',
            'milestone' => 'nullable|integer',
            'billable' => 'nullable|boolean',
            'hourly_rate' => 'nullable|numeric',
            'rel_id' => 'nullable|integer',
            'rel_type' => 'nullable|string|max:30',
            'assigned_staff' => 'nullable|array',
            'assigned_staff.*' => 'integer|exists:tblstaff,staffid',
            'followers' => 'nullable|array',
            'followers.*' => 'integer|exists:tblstaff,staffid',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
        ];
    }
}
