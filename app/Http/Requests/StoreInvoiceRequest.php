<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('invoices_create') ?? false;
    }

    public function rules(): array
    {
        return [
            'number' => 'required|string|max:255',
            'clientid' => 'required|integer',
            'currency' => 'required|integer',
            'date' => 'required|date',
            'duedate' => 'nullable|date',
            'subtotal' => 'nullable|numeric',
            'total_tax' => 'nullable|numeric',
            'total' => 'required|numeric',
            'adjustment' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'discount_total' => 'nullable|numeric',
            'discount_type' => 'nullable|string|max:50',
            'status' => 'required|integer',
            'prefix' => 'nullable|string|max:50',
            'number_format' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'subscription_id' => 'nullable|integer',
            'sale_agent' => 'nullable|integer',
            'billing_street' => 'nullable|string',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_zip' => 'nullable|string|max:50',
            'billing_country' => 'nullable|integer',
            'shipping_street' => 'nullable|string',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_state' => 'nullable|string|max:255',
            'shipping_zip' => 'nullable|string|max:50',
            'shipping_country' => 'nullable|integer',
            'include_shipping' => 'nullable|boolean',
            'show_shipping_on_invoice' => 'nullable|boolean',
            'show_quantity_as' => 'nullable|string|max:50',
            'clientnote' => 'nullable|string',
            'adminnote' => 'nullable|string',
            'terms' => 'nullable|string',
            'recurring' => 'nullable|integer',
            'recurring_type' => 'nullable|string|max:50',
            'cycles' => 'nullable|integer',
            'cancel_overdue_reminders' => 'nullable|boolean',
            'allowed_payment_modes' => 'nullable|string',
        ];
    }
}
