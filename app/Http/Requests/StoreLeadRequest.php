<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('leads_create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phonenumber' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:255',
            'status' => 'required|integer',
            'source' => 'required|integer',
            'country' => 'nullable|integer',
            'assigned' => 'nullable|integer',
            'client_id' => 'nullable|integer',
            'lead_value' => 'nullable|numeric',
            'description' => 'nullable|string',
        ];
    }
}
