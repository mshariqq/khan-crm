<?php

namespace App\Http\Requests;

use App\Models\Project;
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
            'name' => 'required|string|min:2|max:255',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:1,2,3,4',
            'clientid' => 'required|integer|exists:tblclients,userid',
            'billing_type' => 'nullable|integer|in:1,2,3',
            'start_date' => 'nullable|date|before_or_equal:deadline',
            'deadline' => 'nullable|date|after_or_equal:start_date',
            'project_cost' => 'nullable|numeric|min:0',
            'project_rate_per_hour' => 'nullable|numeric|min:0',
            'estimated_hours' => 'nullable|numeric|min:0',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'category_id' => 'nullable|integer|exists:tblproject_categories,id',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'integer|exists:tblstaff,staffid',
            'contact_notification' => 'nullable|boolean',
            'notify_contacts' => 'nullable|array',
            'notify_contacts.*' => 'integer|exists:tblcontacts,id',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status must be one of: Active (1), Completed (2), On Hold (3), or Cancelled (4)',
            'billing_type.in' => 'Billing type must be one of: Fixed Rate (1), Hourly (2), or Mixed (3)',
            'start_date.before_or_equal' => 'Start date must be before or equal to the deadline.',
            'deadline.after_or_equal' => 'Deadline must be after or equal to the start date.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('staff_ids') && is_string($this->staff_ids)) {
            $this->merge([
                'staff_ids' => explode(',', $this->staff_ids),
            ]);
        }
    }
}
