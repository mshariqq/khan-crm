<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('projects_edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'description' => 'required|string',
            'title' => 'nullable|string|max:255',
            'rel_type' => 'required|string|in:project',
            'rel_id' => 'required|integer|exists:tblprojects,id',
        ];
    }
}
