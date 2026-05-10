@extends('layouts.admin')

@section('breadcrumb', 'Create Estimate')

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
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Create Estimate</h1>
            <p class="text-slate-500 text-sm">Create a project quotation</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.estimates.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" form="estimate-form" class="px-6 py-2.5 bg-amber-600 text-white font-semibold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-500/20 flex items-center gap-2">
                <i class="bi bi-check2-lg"></i> Create Estimate
            </button>
        </div>
    </div>

    <form id="estimate-form" action="{{ route('admin.estimates.store') }}" method="POST" class="space-y-6">
        @csrf
        
        {{-- Top Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Client Selection --}}
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
                        <option value="{{ $client->userid }}" {{ old('clientid') == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                    @endforeach
                </select>
                @error('clientid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Estimate Details --}}
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
                        <input type="text" name="number" value="{{ old('number') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="EST-001" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Currency</label>
                        <select name="currency" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('currency') == $currency->id ? 'selected' : '' }}>{{ $currency->symbol }} {{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
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

            {{-- Options --}}
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
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="show_shipping_on_estimate" value="1" {{ old('show_shipping_on_estimate') ? 'checked' : '' }} class="rounded text-blue-600">
                        Show Shipping Address
                    </label>
                </div>
            </div>
        </div>

        {{-- Line Items Section --}}
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
                        <tr class="line-item" data-index="0">
                            <td class="px-4 py-3 text-sm text-slate-400 text-center">1</td>
                            <td class="px-4 py-2">
                                <input type="text" name="item_description[]" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item name or description">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="item_qty[]" value="1" min="0.01" step="0.01" class="qty-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-center focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="item_rate[]" value="0" min="0" step="0.01" class="rate-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-right focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
                            </td>
                            <td class="px-4 py-2">
                                <div class="amount-display text-right font-medium text-slate-800 bg-slate-50 px-3 py-2 rounded-lg">$0.00</div>
                                <input type="hidden" name="item_amount[]" class="amount-input" value="0">
                            </td>
                            <td class="px-2 py-2">
                                <button type="button" onclick="removeLineItem(this)" class="text-slate-300 hover:text-red-500 transition-colors p-1">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 bg-slate-50 p-5">
                <div class="flex flex-col lg:flex-row justify-end gap-8">
                    <div class="w-full lg:w-72 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Subtotal</span>
                            <span class="font-medium text-slate-800" id="display-subtotal">$0.00</span>
                            <input type="hidden" name="subtotal" value="0">
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-200 pt-3">
                            <span class="text-lg font-bold text-slate-800">Total</span>
                            <span class="text-lg font-bold text-amber-600" id="display-total">$0.00</span>
                            <input type="hidden" name="total" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-3">Client Notes</h3>
                <textarea name="clientnote" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Notes visible to client...">{{ old('clientnote') }}</textarea>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-3">Terms & Conditions</h3>
                <textarea name="terms" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Terms...">{{ old('terms') }}</textarea>
            </div>
        </div>
    </form>
</div>

<script>
let lineItemCount = 1;

function addLineItem() {
    const tbody = document.getElementById('line-items-body');
    const row = document.createElement('tr');
    row.className = 'line-item';
    row.dataset.index = lineItemCount;
    row.innerHTML = `
        <td class="px-4 py-3 text-sm text-slate-400 text-center item-number">${lineItemCount + 1}</td>
        <td class="px-4 py-2">
            <input type="text" name="item_description[]" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item name or description">
        </td>
        <td class="px-4 py-2">
            <input type="number" name="item_qty[]" value="1" min="0.01" step="0.01" class="qty-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-center focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
        </td>
        <td class="px-4 py-2">
            <input type="number" name="item_rate[]" value="0" min="0" step="0.01" class="rate-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-right focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
        </td>
        <td class="px-4 py-2">
            <div class="amount-display text-right font-medium text-slate-800 bg-slate-50 px-3 py-2 rounded-lg">$0.00</div>
            <input type="hidden" name="item_amount[]" class="amount-input" value="0">
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
    row.querySelector('.amount-display').textContent = '$' + amount.toFixed(2);
    row.querySelector('.amount-input').value = amount.toFixed(2);
    recalculateAll();
}

function recalculateAll() {
    let subtotal = 0;
    document.querySelectorAll('.line-item').forEach(row => {
        subtotal += parseFloat(row.querySelector('.amount-input').value) || 0;
    });
    document.getElementById('display-subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('display-total').textContent = '$' + subtotal.toFixed(2);
    document.querySelector('input[name="subtotal"]').value = subtotal.toFixed(2);
    document.querySelector('input[name="total"]').value = subtotal.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    recalculateAll();
});
</script>
@endsection