<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscussionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) return false;
        if ($user->can('projects_create')) return true;
        if ($this->isMethod('PUT') && $user->can('projects_edit')) return true;
        return false;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'show_to_customer' => 'nullable|boolean',
        ];
    }
}
