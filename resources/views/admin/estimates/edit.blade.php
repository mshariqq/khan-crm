@extends('layouts.admin')

@section('breadcrumb', 'Edit Estimate')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Estimate</h1>
            <p class="text-slate-500 text-sm">Update the details of estimate #{{ $estimate->number }}.</p>
        </div>
        <a href="{{ route('admin.estimates.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.estimates.update', $estimate->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">General</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Number <span class="text-red-500">*</span></label>
                        <input type="text" name="number" value="{{ old('number', $estimate->number) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('number') border-red-500 @enderror" required>
                        @error('number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Prefix</label>
                        <input type="text" name="prefix" value="{{ old('prefix', $estimate->prefix) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Number Format</label>
                        <select name="number_format" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="1" {{ old('number_format', $estimate->number_format) == '1' ? 'selected' : '' }}>Number based (EST-001)</option>
                            <option value="2" {{ old('number_format', $estimate->number_format) == '2' ? 'selected' : '' }}>Year based (2024-001)</option>
                            <option value="3" {{ old('number_format', $estimate->number_format) == '3' ? 'selected' : '' }}>Year-Month (2024-01-001)</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Reference No</label>
                        <input type="text" name="reference_no" value="{{ old('reference_no', $estimate->reference_no) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Client & Currency</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Client <span class="text-red-500">*</span></label>
                        <select name="clientid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('clientid') border-red-500 @enderror" required>
                            @foreach($clients as $client)
                                <option value="{{ $client->userid }}" {{ old('clientid', $estimate->clientid) == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                            @endforeach
                        </select>
                        @error('clientid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Currency <span class="text-red-500">*</span></label>
                        <select name="currency" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('currency') border-red-500 @enderror" required>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('currency', $estimate->currency) == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->symbol ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Project</label>
                        <select name="project_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">No Project</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ old('project_id', $estimate->project_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Sale Agent</label>
                        <select name="sale_agent" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">No Agent</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->staffid }}" {{ old('sale_agent', $estimate->sale_agent) == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Dates & Status</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Estimate Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ old('date', $estimate->date) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('date') border-red-500 @enderror" required>
                        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Expiry Date</label>
                        <input type="date" name="expirydate" value="{{ old('expirydate', $estimate->expirydate) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        @error('expirydate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                            <option value="1" {{ old('status', $estimate->status) == '1' ? 'selected' : '' }}>Draft</option>
                            <option value="2" {{ old('status', $estimate->status) == '2' ? 'selected' : '' }}>Sent</option>
                            <option value="3" {{ old('status', $estimate->status) == '3' ? 'selected' : '' }}>Accepted</option>
                            <option value="4" {{ old('status', $estimate->status) == '4' ? 'selected' : '' }}>Declined</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Show Quantity As</label>
                        <select name="show_quantity_as" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="1" {{ old('show_quantity_as', $estimate->show_quantity_as) == '1' ? 'selected' : '' }}>Qty</option>
                            <option value="2" {{ old('show_quantity_as', $estimate->show_quantity_as) == '2' ? 'selected' : '' }}>Hours</option>
                            <option value="3" {{ old('show_quantity_as', $estimate->show_quantity_as) == '3' ? 'selected' : '' }}>Days</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Billing & Shipping</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-slate-600 uppercase">Billing Address</h4>
                        <textarea name="billing_street" rows="2" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Street">{{ old('billing_street', $estimate->billing_street) }}</textarea>
                        <div class="grid grid-cols-3 gap-3">
                            <input type="text" name="billing_city" value="{{ old('billing_city', $estimate->billing_city) }}" class="px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="City">
                            <input type="text" name="billing_state" value="{{ old('billing_state', $estimate->billing_state) }}" class="px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="State">
                            <input type="text" name="billing_zip" value="{{ old('billing_zip', $estimate->billing_zip) }}" class="px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="ZIP">
                        </div>
                        <input type="text" name="billing_country" value="{{ old('billing_country', $estimate->billing_country) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Country ID">
                    </div>
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-slate-600 uppercase">Shipping Address</h4>
                        <div class="flex items-center gap-3 mb-2">
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="include_shipping" value="1" {{ old('include_shipping', $estimate->include_shipping) ? 'checked' : '' }} class="rounded border-slate-300"> Include Shipping
                            </label>
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="show_shipping_on_estimate" value="1" {{ old('show_shipping_on_estimate', $estimate->show_shipping_on_estimate) ? 'checked' : '' }} class="rounded border-slate-300"> Show on Estimate
                            </label>
                        </div>
                        <textarea name="shipping_street" rows="2" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Street">{{ old('shipping_street', $estimate->shipping_street) }}</textarea>
                        <div class="grid grid-cols-3 gap-3">
                            <input type="text" name="shipping_city" value="{{ old('shipping_city', $estimate->shipping_city) }}" class="px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="City">
                            <input type="text" name="shipping_state" value="{{ old('shipping_state', $estimate->shipping_state) }}" class="px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="State">
                            <input type="text" name="shipping_zip" value="{{ old('shipping_zip', $estimate->shipping_zip) }}" class="px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="ZIP">
                        </div>
                        <input type="text" name="shipping_country" value="{{ old('shipping_country', $estimate->shipping_country) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Country ID">
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left" id="items-table">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Description</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-20">Qty</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-24">Rate</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-24">Tax</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-28 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200" id="items-body">
                            @for($i = 0; $i < 5; $i++)
                            <tr>
                                <td class="px-4 py-2">
                                    <input type="text" name="items[{{ $i }}][description]" value="{{ old('items.' . $i . '.description') }}" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item description">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="items[{{ $i }}][qty]" value="{{ old('items.' . $i . '.qty', 1) }}" step="0.01" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="items[{{ $i }}][rate]" value="{{ old('items.' . $i . '.rate') }}" step="0.01" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none rate-input" placeholder="0.00">
                                </td>
                                <td class="px-4 py-2">
                                    <select name="items[{{ $i }}][tax]" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                        <option value="">None</option>
                                        @foreach($taxes as $tax)
                                            <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->taxrate }}%)</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <span class="text-sm font-medium text-slate-700 amount-display">0.00</span>
                                </td>
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
                        <textarea name="clientnote" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">{{ old('clientnote', $estimate->clientnote) }}</textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Admin Note</label>
                        <textarea name="adminnote" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">{{ old('adminnote', $estimate->adminnote) }}</textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Terms & Conditions</label>
                        <textarea name="terms" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">{{ old('terms', $estimate->terms) }}</textarea>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Totals</h3>
                    <div class="space-y-3 bg-slate-50 p-4 rounded-xl">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Subtotal</span>
                            <input type="number" step="0.01" name="subtotal" value="{{ old('subtotal', $estimate->subtotal) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Tax Total</span>
                            <input type="number" step="0.01" name="total_tax" value="{{ old('total_tax', $estimate->total_tax) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Discount Type</span>
                            <select name="discount_type" class="w-36 px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="">No Discount</option>
                                <option value="before_tax" {{ old('discount_type', $estimate->discount_type) == 'before_tax' ? 'selected' : '' }}>Before Tax</option>
                                <option value="after_tax" {{ old('discount_type', $estimate->discount_type) == 'after_tax' ? 'selected' : '' }}>After Tax</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Discount Percent</span>
                            <input type="number" step="0.01" name="discount_percent" value="{{ old('discount_percent', $estimate->discount_percent) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Discount Total</span>
                            <input type="number" step="0.01" name="discount_total" value="{{ old('discount_total', $estimate->discount_total) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Adjustment</span>
                            <input type="number" step="0.01" name="adjustment" value="{{ old('adjustment', $estimate->adjustment) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                            <span class="text-base font-bold text-slate-800">Total</span>
                            <input type="number" step="0.01" name="total" value="{{ old('total', $estimate->total) }}" class="w-36 px-3 py-1.5 text-sm text-right font-bold border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                        @error('total') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.estimates.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Estimate</button>
        </div>
    </form>
</div>
@endsection