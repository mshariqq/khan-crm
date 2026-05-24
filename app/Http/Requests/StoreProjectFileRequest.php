<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('projects_edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|max:51200|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt,zip,rar,mp4,avi,mov,mp3,wav',
            'subject' => 'nullable|string|max:255',
            'visible_to_customer' => 'nullable|boolean',
            'folder_id' => 'nullable|integer|exists:tblproject_file_folders,id',
            'document_status' => 'nullable|string|in:draft,reviewed,approved,rejected',
        ];
    }

    public function messages(): array
    {
        return [
            'file.max' => 'The file must not exceed 50MB.',
        ];
    }
}
