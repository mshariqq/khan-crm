<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('projects_create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|integer',
            'clientid' => 'required|integer',
            'billing_type' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'project_cost' => 'nullable|numeric',
            'project_rate_per_hour' => 'nullable|numeric',
            'estimated_hours' => 'nullable|numeric',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'integer|exists:tblstaff,staffid',
        ];
    }
}
