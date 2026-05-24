@extends('layouts.pane')

@section('pane-content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">New Estimate</h1>
        <p class="text-slate-500 text-sm">Create a new estimate for a client.</p>
    </div>
</div>

<form action="{{ route('admin.estimates.store') }}" method="POST" data-pane-submit="1" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    @csrf
    <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Client <span class="text-red-500">*</span></label>
                    <select name="clientid" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm @error('clientid') border-red-500 @enderror" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->userid }}" {{ old('clientid') == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                        @endforeach
                    </select>
                    @error('clientid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Estimate # <span class="text-red-500">*</span></label>
                    <input type="text" name="number" value="{{ old('number') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="EST-001" required>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Prefix</label>
                    <input type="text" name="prefix" value="{{ old('prefix') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="EST-">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Date</label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Expiry</label>
                        <input type="date" name="expirydate" value="{{ old('expirydate') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Currency</label>
                    <select name="currency" onchange="updateCurrencySymbol()" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" data-symbol="{{ $currency->symbol }}" {{ old('currency') == $currency->id ? 'selected' : '' }}>{{ $currency->symbol }} {{ $currency->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Number Format</label>
                    <select name="number_format" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        <option value="1" {{ old('number_format') == '1' ? 'selected' : '' }}>Number based (EST-001)</option>
                        <option value="2" {{ old('number_format') == '2' ? 'selected' : '' }}>Year based (2024-001)</option>
                        <option value="3" {{ old('number_format') == '3' ? 'selected' : '' }}>Year-Month (2024-01-001)</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Draft</option>
                        <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>Sent</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Sale Agent</label>
                    <select name="sale_agent" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        <option value="">No Agent</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->staffid }}" {{ old('sale_agent') == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Show Quantity As</label>
                    <select name="show_quantity_as" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        <option value="1" {{ old('show_quantity_as') == '1' ? 'selected' : '' }}>Qty</option>
                        <option value="2" {{ old('show_quantity_as') == '2' ? 'selected' : '' }}>Hours</option>
                        <option value="3" {{ old('show_quantity_as') == '3' ? 'selected' : '' }}>Days</option>
                    </select>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                    <input type="checkbox" name="show_shipping_on_estimate" value="1" {{ old('show_shipping_on_estimate') ? 'checked' : '' }} class="rounded text-blue-600">
                    Show Shipping Address
                </label>
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Line Items</h3>
                <button type="button" onclick="paneAddItem()" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-all flex items-center gap-1">
                    <i class="bi bi-plus-lg"></i> Add Item
                </button>
            </div>
            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left w-10 text-xs text-slate-500">#</th>
                            <th class="px-3 py-2 text-left text-xs text-slate-500">Description</th>
                            <th class="px-3 py-2 text-center w-20 text-xs text-slate-500">Qty</th>
                            <th class="px-3 py-2 text-right w-28 text-xs text-slate-500">Rate</th>
                            <th class="px-3 py-2 text-right w-28 text-xs text-slate-500">Amount</th>
                            <th class="px-2 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="pane-items-body" class="divide-y divide-slate-100">
                        <tr class="pane-line-item">
                            <td class="px-3 py-1.5 text-center text-slate-400 text-xs item-number">1</td>
                            <td class="px-3 py-1.5">
                                <input type="text" name="item_description[]" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item description">
                            </td>
                            <td class="px-3 py-1.5">
                                <input type="number" name="item_qty[]" value="1" min="0.01" step="0.01" class="pane-qty w-full px-2 py-1.5 border border-slate-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-blue-500 outline-none" onchange="paneCalcTotal(this)">
                            </td>
                            <td class="px-3 py-1.5">
                                <input type="number" name="item_rate[]" value="0" min="0" step="0.01" class="pane-rate w-full px-2 py-1.5 border border-slate-200 rounded-lg text-sm text-right focus:ring-2 focus:ring-blue-500 outline-none" onchange="paneCalcTotal(this)">
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <span class="pane-amount-display text-sm font-medium">$0.00</span>
                                <input type="hidden" name="item_amount[]" class="pane-amount-input" value="0">
                            </td>
                            <td class="px-2 py-1.5">
                                <button type="button" onclick="paneRemoveItem(this)" class="text-slate-300 hover:text-red-500 transition-colors p-1">
                                    <i class="bi bi-x-lg text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row justify-between gap-4">
            <div class="flex flex-wrap items-start gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Discount Type</label>
                    <select name="discount_type" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">No Discount</option>
                        <option value="before_tax" {{ old('discount_type') == 'before_tax' ? 'selected' : '' }}>Before Tax</option>
                        <option value="after_tax" {{ old('discount_type') == 'after_tax' ? 'selected' : '' }}>After Tax</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Discount %</label>
                    <input type="number" step="0.01" name="discount_percent" value="{{ old('discount_percent') }}" class="w-20 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Discount Total</label>
                    <input type="number" step="0.01" name="discount_total" value="{{ old('discount_total') }}" class="w-20 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Adjustment</label>
                    <input type="number" step="0.01" name="adjustment" value="{{ old('adjustment') }}" class="w-20 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                </div>
            </div>
            <div class="w-full lg:w-64 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Subtotal</span>
                    <span id="pane-subtotal" class="font-medium">$0.00</span>
                    <input type="hidden" name="subtotal" value="0">
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Total Tax</span>
                    <span id="pane-tax" class="font-medium">$0.00</span>
                    <input type="hidden" name="total_tax" value="0">
                </div>
                <div class="flex justify-between text-sm border-t border-slate-200 pt-2">
                    <span class="font-semibold">Total</span>
                    <span id="pane-total" class="font-bold text-amber-600">$0.00</span>
                    <input type="hidden" name="total" value="0">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-xs font-semibold text-slate-700 mb-2">Client Notes</h3>
                <textarea name="clientnote" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Notes visible to client...">{{ old('clientnote') }}</textarea>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-slate-700 mb-2">Terms & Conditions</h3>
                <textarea name="terms" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Terms...">{{ old('terms') }}</textarea>
            </div>
        </div>

        <div>
            <label class="text-xs font-medium text-slate-500 mb-1 block">Reference No</label>
            <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="REF-001">
        </div>
    </div>

    <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
        <button type="button" onclick="parent.closeTaskPane()" class="px-4 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow">Save Estimate</button>
    </div>
</form>
@endsection

@push('pane-scripts')
<script>
let paneItemCount = 1;

function getCurrencySymbol() {
    const sel = document.querySelector('select[name="currency"]');
    if (!sel) return '$';
    const opt = sel.options[sel.selectedIndex];
    return opt ? opt.getAttribute('data-symbol') || '$' : '$';
}

function updateCurrencySymbol() { paneRecalc(); }

function paneCalcTotal(el) {
    const row = el.closest('.pane-line-item');
    const qty = parseFloat(row.querySelector('.pane-qty').value) || 0;
    const rate = parseFloat(row.querySelector('.pane-rate').value) || 0;
    const amount = qty * rate;
    row.querySelector('.pane-amount-display').textContent = getCurrencySymbol() + amount.toFixed(2);
    row.querySelector('.pane-amount-input').value = amount.toFixed(2);
    paneRecalc();
}

function paneRecalc() {
    let subtotal = 0;
    document.querySelectorAll('.pane-line-item').forEach(row => {
        subtotal += parseFloat(row.querySelector('.pane-amount-input').value) || 0;
    });
    document.getElementById('pane-subtotal').textContent = getCurrencySymbol() + subtotal.toFixed(2);
    document.getElementById('pane-tax').textContent = getCurrencySymbol() + '0.00';
    document.getElementById('pane-total').textContent = getCurrencySymbol() + subtotal.toFixed(2);
    document.querySelector('input[name="subtotal"]').value = subtotal.toFixed(2);
    document.querySelector('input[name="total_tax"]').value = '0';
    document.querySelector('input[name="total"]').value = subtotal.toFixed(2);
}

function paneAddItem() {
    const tbody = document.getElementById('pane-items-body');
    const row = document.createElement('tr');
    row.className = 'pane-line-item';
    row.innerHTML = `
        <td class="px-3 py-1.5 text-center text-slate-400 text-xs item-number">${paneItemCount + 1}</td>
        <td class="px-3 py-1.5">
            <input type="text" name="item_description[]" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item description">
        </td>
        <td class="px-3 py-1.5">
            <input type="number" name="item_qty[]" value="1" min="0.01" step="0.01" class="pane-qty w-full px-2 py-1.5 border border-slate-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-blue-500 outline-none" onchange="paneCalcTotal(this)">
        </td>
        <td class="px-3 py-1.5">
            <input type="number" name="item_rate[]" value="0" min="0" step="0.01" class="pane-rate w-full px-2 py-1.5 border border-slate-200 rounded-lg text-sm text-right focus:ring-2 focus:ring-blue-500 outline-none" onchange="paneCalcTotal(this)">
        </td>
        <td class="px-3 py-1.5 text-right">
            <span class="pane-amount-display text-sm font-medium">${getCurrencySymbol()}0.00</span>
            <input type="hidden" name="item_amount[]" class="pane-amount-input" value="0">
        </td>
        <td class="px-2 py-1.5">
            <button type="button" onclick="paneRemoveItem(this)" class="text-slate-300 hover:text-red-500 transition-colors p-1">
                <i class="bi bi-x-lg text-xs"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    paneItemCount++;
}

function paneRemoveItem(btn) {
    const tbody = document.getElementById('pane-items-body');
    if (tbody.children.length > 1) {
        btn.closest('tr').remove();
        tbody.querySelectorAll('.pane-line-item').forEach((row, index) => {
            row.querySelector('.item-number').textContent = index + 1;
        });
        paneRecalc();
    }
}

document.addEventListener('DOMContentLoaded', paneRecalc);
</script>
@endpush
