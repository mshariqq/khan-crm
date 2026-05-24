@extends('layouts.admin')

@section('breadcrumb', 'Edit Estimate')

@push('styles')
<style>
.invoice-builder { font-family: 'Inter', sans-serif; }
.line-item { transition: all 0.2s ease; }
.line-item:hover { background: #f8fafc; }
.line-item input:focus, .line-item select:focus { 
    border-color: #3b82f6; 
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.amount-display { font-variant-numeric: tabular-nums; }
</style>
@endpush

@section('main')
<div class="invoice-builder p-4 lg:p-6 space-y-6 bg-slate-50 min-h-screen">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Estimate</h1>
            <p class="text-slate-500 text-sm">Update the details of estimate #{{ $estimate->number }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.estimates.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" form="estimate-form" class="px-6 py-2.5 bg-amber-600 text-white font-semibold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-500/20 flex items-center gap-2">
                <i class="bi bi-check2-lg"></i> Update Estimate
            </button>
        </div>
    </div>

    <form id="estimate-form" action="{{ route('admin.estimates.update', $estimate->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="bi bi-person-fill text-blue-600"></i>
                    </div>
                    <h3 class="font-semibold text-slate-800">Client</h3>
                </div>
                <select name="clientid" id="client-select" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none @error('clientid') border-red-500 @enderror" required>
                    <option value="">Select Client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->userid }}" {{ old('clientid', $estimate->clientid) == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                    @endforeach
                </select>
                @error('clientid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                        <i class="bi bi-file-earmark-ruled text-amber-600"></i>
                    </div>
                    <h3 class="font-semibold text-slate-800">Estimate Details</h3>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Estimate #</label>
                        <input type="text" name="number" value="{{ old('number', $estimate->number) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Prefix</label>
                        <input type="text" name="prefix" value="{{ old('prefix', $estimate->prefix) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Currency</label>
                        <select name="currency" onchange="updateCurrencySymbol()" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" data-symbol="{{ $currency->symbol }}" {{ old('currency', $estimate->currency) == $currency->id ? 'selected' : '' }}>{{ $currency->symbol }} {{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Number Format</label>
                        <select name="number_format" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                            <option value="1" {{ old('number_format', $estimate->number_format) == '1' ? 'selected' : '' }}>Number based (EST-001)</option>
                            <option value="2" {{ old('number_format', $estimate->number_format) == '2' ? 'selected' : '' }}>Year based (2024-001)</option>
                            <option value="3" {{ old('number_format', $estimate->number_format) == '3' ? 'selected' : '' }}>Year-Month (2024-01-001)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Date</label>
                        <input type="date" name="date" value="{{ old('date', $estimate->date) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Expiry</label>
                        <input type="date" name="expirydate" value="{{ old('expirydate', $estimate->expirydate) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="bi bi-gear text-purple-600"></i>
                    </div>
                    <h3 class="font-semibold text-slate-800">Options</h3>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Status</label>
                        <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                            <option value="1" {{ old('status', $estimate->status) == 1 ? 'selected' : '' }}>Draft</option>
                            <option value="2" {{ old('status', $estimate->status) == 2 ? 'selected' : '' }}>Sent</option>
                            <option value="3" {{ old('status', $estimate->status) == 3 ? 'selected' : '' }}>Accepted</option>
                            <option value="4" {{ old('status', $estimate->status) == 4 ? 'selected' : '' }}>Declined</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Sale Agent</label>
                        <select name="sale_agent" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                            <option value="">No Agent</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->staffid }}" {{ old('sale_agent', $estimate->sale_agent) == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Show Quantity As</label>
                        <select name="show_quantity_as" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                            <option value="1" {{ old('show_quantity_as', $estimate->show_quantity_as) == '1' ? 'selected' : '' }}>Qty</option>
                            <option value="2" {{ old('show_quantity_as', $estimate->show_quantity_as) == '2' ? 'selected' : '' }}>Hours</option>
                            <option value="3" {{ old('show_quantity_as', $estimate->show_quantity_as) == '3' ? 'selected' : '' }}>Days</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="show_shipping_on_estimate" value="1" {{ old('show_shipping_on_estimate', $estimate->show_shipping_on_estimate) ? 'checked' : '' }} class="rounded text-blue-600">
                        Show Shipping Address
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-list-check text-blue-500"></i> Line Items
                </h3>
                <button type="button" onclick="addLineItem()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-all flex items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Add Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left w-12">#</th>
                            <th class="px-4 py-3 text-left min-w-[200px]">Description</th>
                            <th class="px-4 py-3 text-center w-24">Qty</th>
                            <th class="px-4 py-3 text-right w-32">Rate</th>
                            <th class="px-4 py-3 text-right w-32">Amount</th>
                            <th class="px-2 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="line-items-body" class="divide-y divide-slate-100">
                        @foreach($estimate->items as $index => $item)
                        <tr class="line-item" data-index="{{ $index }}">
                            <td class="px-4 py-3 text-sm text-slate-400 text-center item-number">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">
                                <input type="text" name="items[{{ $index }}][description]" value="{{ old('items.' . $index . '.description', $item->description) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item name or description">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="items[{{ $index }}][qty]" value="{{ old('items.' . $index . '.qty', $item->qty) }}" min="0.01" step="0.01" class="qty-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-center focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="items[{{ $index }}][rate]" value="{{ old('items.' . $index . '.rate', $item->rate) }}" min="0" step="0.01" class="rate-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-right focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
                            </td>
                            <td class="px-4 py-2">
                                <div class="amount-display text-right font-medium text-slate-800 bg-slate-50 px-3 py-2 rounded-lg">{{ app_format_money($item->amount ?? ($item->qty * $item->rate), $estimate->currency) }}</div>
                                <input type="hidden" name="items[{{ $index }}][amount]" class="amount-input" value="{{ $item->amount ?? ($item->qty * $item->rate) }}">
                            </td>
                            <td class="px-2 py-2">
                                <button type="button" onclick="removeLineItem(this)" class="text-slate-300 hover:text-red-500 transition-colors p-1">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 bg-slate-50 p-5">
                <div class="flex flex-col lg:flex-row justify-between gap-8">
                    <div class="flex items-start gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-slate-500">Discount Type</label>
                            <select name="discount_type" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="">No Discount</option>
                                <option value="before_tax" {{ old('discount_type', $estimate->discount_type) == 'before_tax' ? 'selected' : '' }}>Before Tax</option>
                                <option value="after_tax" {{ old('discount_type', $estimate->discount_type) == 'after_tax' ? 'selected' : '' }}>After Tax</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-slate-500">Discount Percent</label>
                            <input type="number" step="0.01" name="discount_percent" value="{{ old('discount_percent', $estimate->discount_percent) }}" class="w-24 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-slate-500">Discount Total</label>
                            <input type="number" step="0.01" name="discount_total" value="{{ old('discount_total', $estimate->discount_total) }}" class="w-24 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-slate-500">Adjustment</label>
                            <input type="number" step="0.01" name="adjustment" value="{{ old('adjustment', $estimate->adjustment) }}" class="w-24 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                    </div>
                    <div class="w-full lg:w-72 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Subtotal</span>
                            <span class="font-medium text-slate-800" id="display-subtotal">{{ app_format_money($estimate->subtotal, $estimate->currency) }}</span>
                            <input type="hidden" name="subtotal" value="{{ $estimate->subtotal }}">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Total Tax</span>
                            <span class="font-medium text-slate-800" id="display-tax">{{ app_format_money($estimate->total_tax, $estimate->currency) }}</span>
                            <input type="hidden" name="total_tax" value="{{ $estimate->total_tax }}">
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-200 pt-3">
                            <span class="text-lg font-bold text-slate-800">Total</span>
                            <span class="text-lg font-bold text-amber-600" id="display-total">{{ app_format_money($estimate->total, $estimate->currency) }}</span>
                            <input type="hidden" name="total" value="{{ $estimate->total }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-3">Billing Address</h3>
                <div class="space-y-3">
                    <textarea name="billing_street" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Street">{{ old('billing_street', $estimate->billing_street) }}</textarea>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="text" name="billing_city" value="{{ old('billing_city', $estimate->billing_city) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="City">
                        <input type="text" name="billing_state" value="{{ old('billing_state', $estimate->billing_state) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="State">
                        <input type="text" name="billing_zip" value="{{ old('billing_zip', $estimate->billing_zip) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="ZIP">
                    </div>
                    <input type="text" name="billing_country" value="{{ old('billing_country', $estimate->billing_country) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Country ID">
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-3">Shipping Address</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 mb-2">
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="include_shipping" value="1" {{ old('include_shipping', $estimate->include_shipping) ? 'checked' : '' }} class="rounded border-slate-300"> Include Shipping
                        </label>
                    </div>
                    <textarea name="shipping_street" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Street">{{ old('shipping_street', $estimate->shipping_street) }}</textarea>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="text" name="shipping_city" value="{{ old('shipping_city', $estimate->shipping_city) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="City">
                        <input type="text" name="shipping_state" value="{{ old('shipping_state', $estimate->shipping_state) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="State">
                        <input type="text" name="shipping_zip" value="{{ old('shipping_zip', $estimate->shipping_zip) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="ZIP">
                    </div>
                    <input type="text" name="shipping_country" value="{{ old('shipping_country', $estimate->shipping_country) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Country ID">
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-3">Notes</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Client Note</label>
                        <textarea name="clientnote" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Notes visible to client...">{{ old('clientnote', $estimate->clientnote) }}</textarea>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Admin Note</label>
                        <textarea name="adminnote" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Internal notes...">{{ old('adminnote', $estimate->adminnote) }}</textarea>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Terms & Conditions</label>
                        <textarea name="terms" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Terms...">{{ old('terms', $estimate->terms) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-xs font-medium text-slate-500 mb-1 block">Reference No</label>
            <input type="text" name="reference_no" value="{{ old('reference_no', $estimate->reference_no) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="REF-001">
        </div>
    </form>
</div>

<script>
let lineItemCount = {{ $estimate->items->count() }};

function getCurrencySymbol() {
    const sel = document.querySelector('select[name="currency"]');
    if (!sel) return '$';
    const opt = sel.options[sel.selectedIndex];
    return opt ? opt.getAttribute('data-symbol') || '$' : '$';
}

function updateCurrencySymbol() { recalculateAll(); }

function addLineItem() {
    const tbody = document.getElementById('line-items-body');
    const row = document.createElement('tr');
    row.className = 'line-item';
    row.dataset.index = lineItemCount;
    row.innerHTML = `
        <td class="px-4 py-3 text-sm text-slate-400 text-center item-number">${lineItemCount + 1}</td>
        <td class="px-4 py-2">
            <input type="text" name="items[${lineItemCount}][description]" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item name or description">
        </td>
        <td class="px-4 py-2">
            <input type="number" name="items[${lineItemCount}][qty]" value="1" min="0.01" step="0.01" class="qty-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-center focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
        </td>
        <td class="px-4 py-2">
            <input type="number" name="items[${lineItemCount}][rate]" value="0" min="0" step="0.01" class="rate-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-right focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
        </td>
        <td class="px-4 py-2">
            <div class="amount-display text-right font-medium text-slate-800 bg-slate-50 px-3 py-2 rounded-lg">${getCurrencySymbol()}0.00</div>
            <input type="hidden" name="items[${lineItemCount}][amount]" class="amount-input" value="0">
        </td>
        <td class="px-2 py-2">
            <button type="button" onclick="removeLineItem(this)" class="text-slate-300 hover:text-red-500 transition-colors p-1">
                <i class="bi bi-x-lg"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    lineItemCount++;
    recalculateAll();
}

function removeLineItem(btn) {
    const tbody = document.getElementById('line-items-body');
    if (tbody.children.length > 1) {
        btn.closest('tr').remove();
        tbody.querySelectorAll('.line-item').forEach((row, index) => {
            row.querySelector('.item-number').textContent = index + 1;
        });
        recalculateAll();
    }
}

function calculateTotal(element) {
    const row = element.closest('.line-item');
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
    const amount = qty * rate;
    row.querySelector('.amount-display').textContent = getCurrencySymbol() + amount.toFixed(2);
    row.querySelector('.amount-input').value = amount.toFixed(2);
    recalculateAll();
}

function recalculateAll() {
    let subtotal = 0;
    document.querySelectorAll('.line-item').forEach(row => {
        subtotal += parseFloat(row.querySelector('.amount-input').value) || 0;
    });
    document.getElementById('display-subtotal').textContent = getCurrencySymbol() + subtotal.toFixed(2);
    document.getElementById('display-total').textContent = getCurrencySymbol() + subtotal.toFixed(2);
    document.querySelector('input[name="subtotal"]').value = subtotal.toFixed(2);
    document.querySelector('input[name="total"]').value = subtotal.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    recalculateAll();
});
</script>
@endsection