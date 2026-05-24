<?php

namespace App\Http\Requests;

class UpdateLeadRequest extends StoreLeadRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('leads_edit') ?? false;
    }
}
