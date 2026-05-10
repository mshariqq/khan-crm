@extends('layouts.admin')

@section('breadcrumb', 'Create Proposal')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
.invoice-builder { font-family: 'Inter', sans-serif; }
.line-item { transition: all 0.2s ease; }
.line-item:hover { background: #f8fafc; }
.line-item input:focus, .line-item select:focus, .line-item textarea:focus { 
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
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Create Proposal</h1>
            <p class="text-slate-500 text-sm">Create a business proposal</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.proposals.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" form="proposal-form" class="px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                <i class="bi bi-check2-lg"></i> Create Proposal
            </button>
        </div>
    </div>

    <form id="proposal-form" action="{{ route('admin.proposals.store') }}" method="POST" class="space-y-6">
        @csrf
        
        {{-- Top Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Proposal Info --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <i class="bi bi-file-earmark-text text-indigo-600"></i>
                    </div>
                    <h3 class="font-semibold text-slate-800">Proposal Details</h3>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Project Name" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Proposal To <span class="text-red-500">*</span></label>
                        <input type="text" name="proposal_to" value="{{ old('proposal_to') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Company Name" required>
                    </div>
                </div>
            </div>

            {{-- Contact Details --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="bi bi-person-fill text-blue-600"></i>
                    </div>
                    <h3 class="font-semibold text-slate-800">Contact</h3>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="contact@company.com">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="+1 234 567 890">
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
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Date</label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
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
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Status</label>
                        <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Draft</option>
                            <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>Sent</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Open Till</label>
                        <input type="date" name="open_till" value="{{ old('open_till') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="allow_comments" value="1" {{ old('allow_comments', 1) ? 'checked' : '' }} class="rounded text-blue-600">
                        Allow Comments
                    </label>
                </div>
            </div>
        </div>

        {{-- Address Section --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <i class="bi bi-geo-alt text-slate-400"></i> Address
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div class="md:col-span-2">
                    <input type="text" name="address" value="{{ old('address') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="Street Address">
                </div>
                <div>
                    <input type="text" name="city" value="{{ old('city') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="City">
                </div>
                <div>
                    <input type="text" name="state" value="{{ old('state') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="State">
                </div>
                <div>
                    <input type="text" name="zip" value="{{ old('zip') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="ZIP">
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <h3 class="font-semibold text-slate-800 mb-3">Proposal Content</h3>
            <div id="content-editor" class="mt-1"></div>
            <input type="hidden" name="content" id="content-input">
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
                            <span class="text-lg font-bold text-indigo-600" id="display-total">$0.00</span>
                            <input type="hidden" name="total" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-3">Admin Notes</h3>
                <div id="adminnote-editor" class="mt-1"></div>
                <input type="hidden" name="adminnote" id="adminnote-input">
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-3">Terms & Conditions</h3>
                <div id="terms-editor" class="mt-1"></div>
                <input type="hidden" name="terms" id="terms-input">
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toolbarOptions = [
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'clean']
        ];

        const contentQuill = new Quill('#content-editor', {
            theme: 'snow',
            placeholder: 'Describe your proposal, services, deliverables...',
            modules: { toolbar: toolbarOptions }
        });

        const adminnoteQuill = new Quill('#adminnote-editor', {
            theme: 'snow',
            placeholder: 'Internal notes...',
            modules: { toolbar: toolbarOptions }
        });

        const termsQuill = new Quill('#terms-editor', {
            theme: 'snow',
            placeholder: 'Terms...',
            modules: { toolbar: toolbarOptions }
        });

        const form = document.querySelector('#proposal-form');
        form.onsubmit = function() {
            document.querySelector('#content-input').value = contentQuill.root.innerHTML;
            document.querySelector('#adminnote-input').value = adminnoteQuill.root.innerHTML;
            document.querySelector('#terms-input').value = termsQuill.root.innerHTML;
        };
    });
</script>
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