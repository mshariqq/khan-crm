<?php

namespace App\Http\Requests;

class UpdateInvoiceRequest extends StoreInvoiceRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('invoices_edit') ?? false;
    }
}
