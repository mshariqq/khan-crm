<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscussionCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('projects_view') ?? false;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip|max:10240',
            'parent_id' => 'nullable|integer|exists:tblprojectdiscussioncomments,id',
        ];
    }
}
