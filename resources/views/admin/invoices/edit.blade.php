@extends('layouts.admin')

@section('breadcrumb', 'Edit Invoice')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
.invoice-builder { font-family: 'Inter', sans-serif; }
.line-item { transition: all 0.2s ease; }
.line-item:hover { background: #f8fafc; }
.line-item input:focus, .line-item select:focus { 
    border-color: #3b82f6; 
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.amount-display { font-variant-numeric: tabular-nums; }
.ql-editor { font-size: 14px; padding: 15px; min-height: 150px; }
.ql-toolbar { border: 1px solid #e2e8f0 !important; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background: #f8fafc; }
.ql-container { border: 1px solid #e2e8f0 !important; border-top: none !important; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
</style>
@endpush

@section('main')
<div class="invoice-builder p-4 lg:p-6 space-y-6 bg-slate-50 min-h-screen">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Invoice</h1>
            <p class="text-slate-500 text-sm">Update the details of invoice #{{ $invoice->number }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" form="invoice-form" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-check2-lg"></i> Update Invoice
            </button>
        </div>
    </div>

    <form id="invoice-form" action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST" class="space-y-6">
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
                <select name="clientid" id="client-select" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none @error('clientid') border-red-500 @enderror" required onchange="loadClientDetails()">
                    <option value="">Select Client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->userid }}" data-email="{{ $client->email }}" data-phone="{{ $client->phonenumber }}" data-address="{{ $client->address }}" data-city="{{ $client->city }}" data-state="{{ $client->state }}" data-zip="{{ $client->zip }}" {{ old('clientid', $invoice->clientid) == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                    @endforeach
                </select>
                @error('clientid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                
                <div id="client-details" class="mt-4 p-3 bg-slate-50 rounded-xl @if($invoice->clientid) @else hidden @endif">
                    @php $c = $clients->firstWhere('userid', $invoice->clientid); @endphp
                    <p class="text-sm text-slate-600" id="client-email">{{ $c->email ?? '' }}</p>
                    @php $addr = array_filter([$c->address ?? '', $c->city ?? '', $c->state ?? '', $c->zip ?? '']); @endphp
                    <p class="text-sm text-slate-500" id="client-address">{{ $addr ? implode(', ', $addr) : 'No address' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <i class="bi bi-receipt text-emerald-600"></i>
                    </div>
                    <h3 class="font-semibold text-slate-800">Invoice Details</h3>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Invoice #</label>
                        <input type="text" name="number" value="{{ old('number', $invoice->number) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="INV-001" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Currency</label>
                        <select name="currency" id="currency-select" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required onchange="updateCurrencySymbol()">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" data-symbol="{{ $currency->symbol }}" data-placement="{{ $currency->placement ?? 'before' }}" {{ old('currency', $invoice->currency) == $currency->id ? 'selected' : '' }}>{{ $currency->symbol }} {{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Date</label>
                        <input type="date" name="date" value="{{ old('date', $invoice->date) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Due Date</label>
                        <input type="date" name="duedate" value="{{ old('duedate', $invoice->duedate) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
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
                            <option value="1" {{ old('status', $invoice->status) == 1 ? 'selected' : '' }}>Unpaid</option>
                            <option value="2" {{ old('status', $invoice->status) == 2 ? 'selected' : '' }}>Partially Paid</option>
                            <option value="3" {{ old('status', $invoice->status) == 3 ? 'selected' : '' }}>Paid</option>
                            <option value="4" {{ old('status', $invoice->status) == 4 ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Sale Agent</label>
                        <select name="sale_agent" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                            <option value="">No Agent</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->staffid }}" {{ old('sale_agent', $invoice->sale_agent) == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="include_shipping" value="1" {{ old('include_shipping', $invoice->include_shipping) ? 'checked' : '' }} class="rounded text-blue-600">
                        Include Shipping Address
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <i class="bi bi-geo-alt text-slate-400"></i> Billing Address
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div class="md:col-span-2">
                    <input type="text" name="billing_street" value="{{ old('billing_street', $invoice->billing_street) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="Street Address">
                </div>
                <div>
                    <input type="text" name="billing_city" value="{{ old('billing_city', $invoice->billing_city) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="City">
                </div>
                <div>
                    <input type="text" name="billing_state" value="{{ old('billing_state', $invoice->billing_state) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="State">
                </div>
                <div>
                    <input type="text" name="billing_zip" value="{{ old('billing_zip', $invoice->billing_zip) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="ZIP">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <i class="bi bi-geo-alt text-slate-400"></i> Shipping Address
            </h3>
            <label class="flex items-center gap-2 text-sm text-slate-600 mb-4 cursor-pointer">
                <input type="checkbox" name="show_shipping_on_invoice" value="1" {{ old('show_shipping_on_invoice', $invoice->show_shipping_on_invoice) ? 'checked' : '' }} class="rounded text-blue-600">
                Show shipping on invoice
            </label>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div class="md:col-span-2">
                    <input type="text" name="shipping_street" value="{{ old('shipping_street', $invoice->shipping_street) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="Street Address">
                </div>
                <div>
                    <input type="text" name="shipping_city" value="{{ old('shipping_city', $invoice->shipping_city) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="City">
                </div>
                <div>
                    <input type="text" name="shipping_state" value="{{ old('shipping_state', $invoice->shipping_state) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="State">
                </div>
                <div>
                    <input type="text" name="shipping_zip" value="{{ old('shipping_zip', $invoice->shipping_zip) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="ZIP">
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
                            <th class="px-4 py-3 text-left w-40">Tax</th>
                            <th class="px-4 py-3 text-right w-32">Amount</th>
                            <th class="px-2 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="line-items-body" class="divide-y divide-slate-100">
                        @php $existingItems = $invoice->items ?? collect(); @endphp
                        @for($i = 0; $i < max(1, $existingItems->count()); $i++)
                        @php $ei = $existingItems->get($i); @endphp
                        <tr class="line-item" data-index="{{ $i }}">
                            <td class="px-4 py-3 text-sm text-slate-400 text-center item-number">{{ $i + 1 }}</td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2">
                                    <select name="item_id[]" class="item-select w-24 px-2 py-2 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500 outline-none" onchange="selectItem(this)">
                                        <option value="">Select...</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-description="{{ $item->description }}" data-rate="{{ $item->rate }}" data-tax="{{ $item->tax }}" {{ old('item_id.' . $i, '') == $item->id ? 'selected' : '' }}>{{ Str::limit($item->description, 20) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="openItemPane('{{ route('admin.items.pane.create') }}')" class="px-2 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors text-xs" title="Add New Item">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                    <input type="text" name="item_description[]" value="{{ old('item_description.' . $i, $ei->description ?? '') }}" class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item name or description">
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="item_qty[]" value="{{ old('item_qty.' . $i, $ei->qty ?? 1) }}" min="0.01" step="0.01" class="qty-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-center focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="item_rate[]" value="{{ old('item_rate.' . $i, $ei->rate ?? 0) }}" min="0" step="0.01" class="rate-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-right focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
                            </td>
                            <td class="px-4 py-2">
                                <select name="item_tax_id[]" class="tax-select w-full px-2 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
                                    <option value="" data-rate="0">No Tax</option>
                                    @foreach($taxes as $tax)
                                        <option value="{{ $tax->id }}" data-rate="{{ $tax->taxrate }}" {{ old('item_tax_id.' . $i, $ei && $ei->tax_rate > 0 ? $taxes->firstWhere('taxrate', $ei->tax_rate)->id ?? '' : '') == $tax->id ? 'selected' : '' }}>{{ $tax->name }} ({{ $tax->taxrate }}%)</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <div class="amount-display text-right font-medium text-slate-800 bg-slate-50 px-3 py-2 rounded-lg"><span class="currency-symbol">{{ $invoice->currency->symbol ?? '$' }}</span>{{ number_format(($ei->amount ?? 0), 2) }}</div>
                                <input type="hidden" name="item_amount[]" class="amount-input" value="{{ $ei->amount ?? 0 }}">
                            </td>
                            <td class="px-2 py-2">
                                <button type="button" onclick="removeLineItem(this)" class="text-slate-300 hover:text-red-500 transition-colors p-1">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 bg-slate-50 p-5">
                <div class="flex flex-col lg:flex-row justify-end gap-8">
                    <div class="w-full lg:w-72 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Subtotal</span>
                            <span class="font-medium text-slate-800" id="display-subtotal"><span class="currency-symbol">{{ $invoice->currency->symbol ?? '$' }}</span>{{ number_format($invoice->subtotal, 2) }}</span>
                            <input type="hidden" name="subtotal" value="{{ $invoice->subtotal }}">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Tax</span>
                            <span class="font-medium text-slate-800" id="display-tax"><span class="currency-symbol">{{ $invoice->currency->symbol ?? '$' }}</span>{{ number_format($invoice->total_tax, 2) }}</span>
                            <input type="hidden" name="total_tax" value="{{ $invoice->total_tax }}">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Discount</span>
                            <div class="flex items-center gap-2">
                                <input type="number" name="discount_percent" value="{{ old('discount_percent', $invoice->discount_percent) }}" class="w-16 px-2 py-1 rounded border border-slate-200 text-sm text-right" placeholder="%" oninput="recalculateAll()">
                                <span class="text-xs text-slate-400">%</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-200 pt-3">
                            <span class="text-lg font-bold text-slate-800">Total</span>
                            <span class="text-lg font-bold text-blue-600" id="display-total"><span class="currency-symbol">{{ $invoice->currency->symbol ?? '$' }}</span>{{ number_format($invoice->total, 2) }}</span>
                            <input type="hidden" name="total" value="{{ $invoice->total }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-3">Client Notes</h3>
                <div id="clientnote-editor" class="mt-1">{!! old('clientnote', $invoice->clientnote) !!}</div>
                <input type="hidden" name="clientnote" id="clientnote-input">
            </div>
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h3 class="font-semibold text-slate-800 mb-3">Admin Note</h3>
                    <div id="adminnote-editor" class="mt-1">{!! old('adminnote', $invoice->adminnote) !!}</div>
                    <input type="hidden" name="adminnote" id="adminnote-input">
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h3 class="font-semibold text-slate-800 mb-3">Terms & Conditions</h3>
                    <div id="terms-editor" class="mt-1">{!! old('terms', $invoice->terms) !!}</div>
                    <input type="hidden" name="terms" id="terms-input">
                </div>
            </div>
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
            placeholder: 'Notes visible to client...',
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

        const form = document.querySelector('#invoice-form');
        form.onsubmit = function() {
            document.querySelector('#clientnote-input').value = clientnoteQuill.root.innerHTML;
            document.querySelector('#adminnote-input').value = adminnoteQuill.root.innerHTML;
            document.querySelector('#terms-input').value = termsQuill.root.innerHTML;
        };

        updateCurrencySymbol();
        recalculateAll();
        loadClientDetails();
    });
</script>
<script>
let lineItemCount = {{ max(1, ($invoice->items ?? collect())->count()) }};

function addLineItem() {
    const tbody = document.getElementById('line-items-body');
    const row = document.createElement('tr');
    row.className = 'line-item';
    row.dataset.index = lineItemCount;
    row.innerHTML = `
        <td class="px-4 py-3 text-sm text-slate-400 text-center item-number">${lineItemCount + 1}</td>
        <td class="px-4 py-2">
            <div class="flex gap-2">
                <select name="item_id[]" class="item-select w-24 px-2 py-2 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500 outline-none" onchange="selectItem(this)">
                    <option value="">Select...</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-description="{{ $item->description }}" data-rate="{{ $item->rate }}" data-tax="{{ $item->tax }}">{{ Str::limit($item->description, 20) }}</option>
                    @endforeach
                </select>
                <input type="text" name="item_description[]" class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item name or description">
            </div>
        </td>
        <td class="px-4 py-2">
            <input type="number" name="item_qty[]" value="1" min="0.01" step="0.01" class="qty-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-center focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
        </td>
        <td class="px-4 py-2">
            <input type="number" name="item_rate[]" value="0" min="0" step="0.01" class="rate-input w-full px-2 py-2 rounded-lg border border-slate-200 text-sm text-right focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
        </td>
        <td class="px-4 py-2">
            <select name="item_tax_id[]" class="tax-select w-full px-2 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" onchange="calculateTotal(this)">
                <option value="" data-rate="0">No Tax</option>
                @foreach($taxes as $tax)
                    <option value="{{ $tax->id }}" data-rate="{{ $tax->taxrate }}">{{ $tax->name }} ({{ $tax->taxrate }}%)</option>
                @endforeach
            </select>
        </td>
        <td class="px-4 py-2">
            <div class="amount-display text-right font-medium text-slate-800 bg-slate-50 px-3 py-2 rounded-lg"><span class="currency-symbol">$</span>0.00</div>
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

function getCurrencySymbol() {
    const select = document.getElementById('currency-select');
    const option = select.options[select.selectedIndex];
    return option ? option.dataset.symbol || '$' : '$';
}

function updateCurrencySymbol() {
    const symbol = getCurrencySymbol();
    document.querySelectorAll('.currency-symbol').forEach(el => {
        el.textContent = symbol;
    });
}

function calculateTotal(element) {
    const row = element.closest('.line-item');
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
    
    const amount = qty * rate;
    row.querySelector('.amount-display').innerHTML = '<span class="currency-symbol">' + getCurrencySymbol() + '</span>' + amount.toFixed(2);
    row.querySelector('.amount-input').value = amount.toFixed(2);
    
    recalculateAll();
}

function recalculateAll() {
    let subtotal = 0;
    let totalTax = 0;
    
    document.querySelectorAll('.line-item').forEach(row => {
        const amount = parseFloat(row.querySelector('.amount-input').value) || 0;
        const taxSelect = row.querySelector('.tax-select');
        const taxRate = parseFloat(taxSelect.options[taxSelect.selectedIndex].dataset.rate) || 0;
        
        subtotal += amount;
        totalTax += amount * (taxRate / 100);
    });
    
    const discountPercent = parseFloat(document.querySelector('input[name="discount_percent"]').value) || 0;
    const discount = subtotal * (discountPercent / 100);
    
    const total = subtotal + totalTax - discount;
    const symbol = getCurrencySymbol();
    
    document.getElementById('display-subtotal').innerHTML = '<span class="currency-symbol">' + symbol + '</span>' + subtotal.toFixed(2);
    document.getElementById('display-tax').innerHTML = '<span class="currency-symbol">' + symbol + '</span>' + totalTax.toFixed(2);
    document.getElementById('display-total').innerHTML = '<span class="currency-symbol">' + symbol + '</span>' + total.toFixed(2);
    
    document.querySelector('input[name="subtotal"]').value = subtotal.toFixed(2);
    document.querySelector('input[name="total_tax"]').value = totalTax.toFixed(2);
    document.querySelector('input[name="total"]').value = total.toFixed(2);
}

function loadClientDetails() {
    const select = document.getElementById('client-select');
    const option = select.options[select.selectedIndex];
    const details = document.getElementById('client-details');
    
    if (select.value) {
        document.getElementById('client-email').textContent = option.dataset.email || '';
        document.getElementById('client-address').textContent = [
            option.dataset.address,
            option.dataset.city,
            option.dataset.state,
            option.dataset.zip
        ].filter(Boolean).join(', ') || 'No address';
        details.classList.remove('hidden');
    } else {
        details.classList.add('hidden');
    }
}

function selectItem(select) {
    const row = select.closest('.line-item');
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        const description = option.dataset.description || '';
        const rate = parseFloat(option.dataset.rate) || 0;
        const taxId = option.dataset.tax || '';
        
        row.querySelector('input[name="item_description[]"]').value = description;
        row.querySelector('.rate-input').value = rate.toFixed(2);
        
        if (taxId) {
            const taxSelect = row.querySelector('.tax-select');
            const taxOptions = taxSelect.querySelectorAll('option');
            taxOptions.forEach(opt => {
                if (opt.value == taxId) {
                    taxSelect.value = taxId;
                }
            });
        }
        
        calculateTotal(row);
    }
}
</script>
@endpush