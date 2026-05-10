@extends('layouts.admin')

@section('breadcrumb', 'Edit Invoice')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-editor { font-size: 14px; padding: 15px; min-height: 150px; }
    .ql-toolbar { border: 1px solid #e2e8f0 !important; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background: #f8fafc; }
    .ql-container { border: 1px solid #e2e8f0 !important; border-top: none !important; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
</style>
@endpush

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Invoice</h1>
            <p class="text-slate-500 text-sm">Update the details of invoice #{{ $invoice->number }}.</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Invoice Info</h3>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Invoice Number <span class="text-red-500">*</span></label>
                        <input type="text" name="number" value="{{ old('number', $invoice->number) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('number') border-red-500 @enderror" placeholder="INV-001" required>
                        @error('number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Prefix</label>
                            <input type="text" name="prefix" value="{{ old('prefix', $invoice->prefix) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('prefix') border-red-500 @enderror" placeholder="INV-">
                            @error('prefix') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Number Format</label>
                            <select name="number_format" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('number_format') border-red-500 @enderror">
                                <option value="1" {{ old('number_format', $invoice->number_format) == '1' ? 'selected' : '' }}>0001</option>
                                <option value="2" {{ old('number_format', $invoice->number_format) == '2' ? 'selected' : '' }}>00001</option>
                            </select>
                            @error('number_format') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Client & Currency</h3>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Client <span class="text-red-500">*</span></label>
                        <select name="clientid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('clientid') border-red-500 @enderror" required>
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->userid }}" {{ old('clientid', $invoice->clientid) == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                            @endforeach
                        </select>
                        @error('clientid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Currency <span class="text-red-500">*</span></label>
                        <select name="currency" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('currency') border-red-500 @enderror" required>
                            <option value="">Select Currency</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('currency', $invoice->currency) == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->symbol ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Sale Agent</label>
                        <select name="sale_agent" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">No Agent</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->staffid }}" {{ old('sale_agent', $invoice->sale_agent) == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Dates & Status</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Invoice Date <span class="text-red-500">*</span></label>
                            <input type="date" name="date" value="{{ old('date', $invoice->date) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('date') border-red-500 @enderror" required>
                            @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Due Date</label>
                            <input type="date" name="duedate" value="{{ old('duedate', $invoice->duedate) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('duedate') border-red-500 @enderror">
                            @error('duedate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('status') border-red-500 @enderror" required>
                            <option value="1" {{ old('status', $invoice->status) == '1' ? 'selected' : '' }}>Unpaid</option>
                            <option value="2" {{ old('status', $invoice->status) == '2' ? 'selected' : '' }}>Partially Paid</option>
                            <option value="3" {{ old('status', $invoice->status) == '3' ? 'selected' : '' }}>Paid</option>
                            <option value="4" {{ old('status', $invoice->status) == '4' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Show Quantity As</label>
                        <select name="show_quantity_as" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="qty" {{ old('show_quantity_as', $invoice->show_quantity_as) == 'qty' ? 'selected' : '' }}>Qty</option>
                            <option value="hours" {{ old('show_quantity_as', $invoice->show_quantity_as) == 'hours' ? 'selected' : '' }}>Hours</option>
                            <option value="items" {{ old('show_quantity_as', $invoice->show_quantity_as) == 'items' ? 'selected' : '' }}>Items</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Project & Subscription</h3>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Project</label>
                        <select name="project_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">No Project</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ old('project_id', $invoice->project_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Subscription</label>
                        <select name="subscription_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">No Subscription</option>
                            @foreach($subscriptions as $sub)
                                <option value="{{ $sub->id }}" {{ old('subscription_id', $invoice->subscription_id) == $sub->id ? 'selected' : '' }}>{{ $sub->name ?? '#' . $sub->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Recurring</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Recurring</label>
                            <input type="number" name="recurring" value="{{ old('recurring', $invoice->recurring) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Type</label>
                            <select name="recurring_type" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="month" {{ old('recurring_type', $invoice->recurring_type) == 'month' ? 'selected' : '' }}>Month</option>
                                <option value="year" {{ old('recurring_type', $invoice->recurring_type) == 'year' ? 'selected' : '' }}>Year</option>
                                <option value="week" {{ old('recurring_type', $invoice->recurring_type) == 'week' ? 'selected' : '' }}>Week</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Cycles</label>
                            <input type="number" name="cycles" value="{{ old('cycles', $invoice->cycles) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0">
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="include_shipping" value="1" {{ old('include_shipping', $invoice->include_shipping) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Include Shipping
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="cancel_overdue_reminders" value="1" {{ old('cancel_overdue_reminders', $invoice->cancel_overdue_reminders) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Cancel Overdue Reminders
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Billing Address</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Street</label>
                        <input type="text" name="billing_street" value="{{ old('billing_street', $invoice->billing_street) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">City</label>
                        <input type="text" name="billing_city" value="{{ old('billing_city', $invoice->billing_city) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">State</label>
                        <input type="text" name="billing_state" value="{{ old('billing_state', $invoice->billing_state) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">ZIP</label>
                        <input type="text" name="billing_zip" value="{{ old('billing_zip', $invoice->billing_zip) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Country</label>
                        <input type="text" name="billing_country" value="{{ old('billing_country', $invoice->billing_country) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Shipping Address</h3>
                <label class="flex items-center gap-2 text-sm text-slate-600 mb-4">
                    <input type="checkbox" name="show_shipping_on_invoice" value="1" {{ old('show_shipping_on_invoice', $invoice->show_shipping_on_invoice) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    Show shipping on invoice
                </label>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Street</label>
                        <input type="text" name="shipping_street" value="{{ old('shipping_street', $invoice->shipping_street) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">City</label>
                        <input type="text" name="shipping_city" value="{{ old('shipping_city', $invoice->shipping_city) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">State</label>
                        <input type="text" name="shipping_state" value="{{ old('shipping_state', $invoice->shipping_state) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">ZIP</label>
                        <input type="text" name="shipping_zip" value="{{ old('shipping_zip', $invoice->shipping_zip) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Country</label>
                        <input type="text" name="shipping_country" value="{{ old('shipping_country', $invoice->shipping_country) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Invoice Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border border-slate-200 rounded-lg">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Description</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Quantity</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Rate</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Tax</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @php
                                $existingItems = $invoice->items ?? collect();
                            @endphp
                            @for($i = 1; $i <= max(5, $existingItems->count()); $i++)
                            @php
                                $ei = $existingItems->get($i - 1);
                            @endphp
                            <tr>
                                <td class="px-4 py-2 text-sm text-slate-500">{{ $i }}</td>
                                <td class="px-4 py-2">
                                    <input type="text" name="item_description[]" value="{{ old('item_description.' . ($i-1), $ei->description ?? '') }}" class="w-full px-3 py-1.5 rounded border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Item description">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" step="0.01" name="item_qty[]" value="{{ old('item_qty.' . ($i-1), $ei->qty ?? '') }}" class="w-20 px-3 py-1.5 rounded border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm text-center" placeholder="1" min="0">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" step="0.01" name="item_rate[]" value="{{ old('item_rate.' . ($i-1), $ei->rate ?? '') }}" class="w-24 px-3 py-1.5 rounded border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm text-right" placeholder="0.00" min="0">
                                </td>
                                <td class="px-4 py-2">
                                    <select name="item_tax_id[]" class="w-full px-3 py-1.5 rounded border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                                        <option value="">No Tax</option>
                                        @foreach($taxes as $tax)
                                            <option value="{{ $tax->id }}" {{ old('item_tax_id.' . ($i-1), $ei->tax_id ?? '') == $tax->id ? 'selected' : '' }}>{{ $tax->name }} ({{ $tax->taxrate }}%)</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2 text-sm text-slate-700 font-medium text-right">—</td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Notes</h3>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Client Note</label>
                        <div id="clientnote-editor" class="mt-1">{!! old('clientnote', $invoice->clientnote) !!}</div>
                        <input type="hidden" name="clientnote" id="clientnote-input">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Admin Note</label>
                        <div id="adminnote-editor" class="mt-1">{!! old('adminnote', $invoice->adminnote) !!}</div>
                        <input type="hidden" name="adminnote" id="adminnote-input">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Terms & Conditions</label>
                        <div id="terms-editor" class="mt-1">{!! old('terms', $invoice->terms) !!}</div>
                        <input type="hidden" name="terms" id="terms-input">
                    </div>
                </div>
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Totals</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-600">Subtotal</label>
                            <input type="number" step="0.01" name="subtotal" value="{{ old('subtotal', $invoice->subtotal) }}" class="w-40 px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all text-right" placeholder="0.00">
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-slate-600">Discount</label>
                                <select name="discount_type" class="px-2 py-1 rounded border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="percent" {{ old('discount_type', $invoice->discount_type) == 'percent' ? 'selected' : '' }}>%</option>
                                    <option value="fixed" {{ old('discount_type', $invoice->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" step="0.01" name="discount_percent" value="{{ old('discount_percent', $invoice->discount_percent) }}" class="w-24 px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all text-right" placeholder="0">
                                <input type="number" step="0.01" name="discount_total" value="{{ old('discount_total', $invoice->discount_total) }}" class="w-28 px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all text-right" placeholder="0.00">
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-600">Tax Total</label>
                            <input type="number" step="0.01" name="total_tax" value="{{ old('total_tax', $invoice->total_tax) }}" class="w-40 px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all text-right" placeholder="0.00">
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-600">Adjustment</label>
                            <input type="number" step="0.01" name="adjustment" value="{{ old('adjustment', $invoice->adjustment) }}" class="w-40 px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all text-right" placeholder="0.00">
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200 pt-3">
                            <label class="text-base font-bold text-slate-800">Total <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="total" value="{{ old('total', $invoice->total) }}" class="w-40 px-4 py-2 rounded-lg border border-blue-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all text-right font-bold text-base" placeholder="0.00" required>
                        </div>
                        @error('total') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1 mt-4">
                        <label class="text-sm font-medium text-slate-600">Allowed Payment Modes</label>
                        <select name="allowed_payment_modes" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">All Payment Modes</option>
                            @foreach($paymentModes as $pm)
                                <option value="{{ $pm->id }}" {{ old('allowed_payment_modes', $invoice->allowed_payment_modes) == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.invoices.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Invoice</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toolbarOptions = [
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'clean']
        ];

        const clientnoteQuill = new Quill('#clientnote-editor', {
            theme: 'snow',
            placeholder: 'Note for the client...',
            modules: { toolbar: toolbarOptions }
        });

        const adminnoteQuill = new Quill('#adminnote-editor', {
            theme: 'snow',
            placeholder: 'Internal note...',
            modules: { toolbar: toolbarOptions }
        });

        const termsQuill = new Quill('#terms-editor', {
            theme: 'snow',
            placeholder: 'Payment terms...',
            modules: { toolbar: toolbarOptions }
        });

        const form = document.querySelector('form');
        form.onsubmit = function() {
            document.querySelector('#clientnote-input').value = clientnoteQuill.root.innerHTML;
            document.querySelector('#adminnote-input').value = adminnoteQuill.root.innerHTML;
            document.querySelector('#terms-input').value = termsQuill.root.innerHTML;
        };
    });
</script>
@endpush