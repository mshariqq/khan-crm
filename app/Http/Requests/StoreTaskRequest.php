<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tasks_create') ?? false;
    }

    protected function prepareForValidation()
    {
        if ($this->has('assigned_staff') && is_string($this->assigned_staff)) {
            $this->merge([
                'assigned_staff' => explode(',', $this->assigned_staff)
            ]);
        }
        if ($this->has('tags') && is_string($this->tags)) {
            $tagsArr = array_filter(array_map('trim', explode(',', $this->tags)), fn($v) => $v !== '');
            $this->merge([
                'tags' => $tagsArr
            ]);
        }
        if ($this->has('followers') && is_string($this->followers)) {
            $this->merge([
                'followers' => explode(',', $this->followers)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'priority' => 'required|integer|in:1,2,3,4',
            'status' => 'required|integer|in:0,1,2,3',
            'description' => 'nullable|string',
            'startdate' => 'nullable|date',
            'duedate' => 'nullable|date|after_or_equal:startdate',
            'milestone' => 'nullable|integer',
            'billable' => 'nullable|boolean',
            'hourly_rate' => 'nullable|numeric',
            'rel_id' => 'nullable|integer|required_with:rel_type',
            'rel_type' => 'nullable|string|max:30|required_with:rel_id',
            'assigned_staff' => 'nullable|array',
            'assigned_staff.*' => 'integer|exists:tblstaff,staffid',
            'followers' => 'nullable|array',
            'followers.*' => 'integer|exists:tblstaff,staffid',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'visible_to_client' => 'nullable|boolean',
        ];
    }
}
