<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Create Proposal</h1>
        <p class="text-slate-500 text-sm">Create a new business proposal.</p>
    </div>
</div>

<form action="{{ route('admin.proposals.store') }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.proposals.index') }}" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    @csrf
    <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">General</h3>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Subject <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Proposal To <span class="text-red-500">*</span></label>
                    <input type="text" name="proposal_to" value="{{ old('proposal_to') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>
            </div>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Settings</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Open Till</label>
                        <input type="date" name="open_till" value="{{ old('open_till') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Currency <span class="text-red-500">*</span></label>
                    <select name="currency" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('currency') == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->symbol ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Assigned Staff</label>
                    <select name="assigned" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        <option value="">Unassigned</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->staffid }}" {{ old('assigned') == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Items</h3>
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-20">Qty</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-24">Rate</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-24">Tax</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-28 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200" id="pane-items-body">
                    @for($i = 0; $i < 5; $i++)
                    <tr class="pane-line-item">
                        <td class="px-4 py-2">
                            <input type="text" name="items[{{ $i }}][description]" value="{{ old('items.' . $i . '.description') }}" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item description">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" name="items[{{ $i }}][qty]" value="{{ old('items.' . $i . '.qty', 1) }}" step="0.01" class="pane-qty w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" name="items[{{ $i }}][rate]" value="{{ old('items.' . $i . '.rate') }}" step="0.01" class="pane-rate w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
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
                            <input type="hidden" name="items[{{ $i }}][amount]" class="pane-amount-input" value="0">
                            <span class="pane-amount-display text-sm font-medium text-slate-700">0.00</span>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div></div>
            <div>
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Totals</h3>
                <div class="space-y-3 bg-slate-50 p-4 rounded-xl">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Subtotal</span>
                        <input type="number" step="0.01" name="subtotal" value="{{ old('subtotal', 0) }}" class="pane-subtotal w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00" readonly>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Tax Total</span>
                        <input type="number" step="0.01" name="total_tax" value="{{ old('total_tax', 0) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Adjustment</span>
                        <input type="number" step="0.01" name="adjustment" value="{{ old('adjustment', 0) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-200 pt-3">
                        <span class="text-base font-bold text-slate-800">Total</span>
                        <input type="number" step="0.01" name="total" value="{{ old('total', 0) }}" class="pane-total w-36 px-3 py-1.5 text-sm text-right font-bold border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
        <button type="button" onclick="parent.closeTaskPane()" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</button>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Create Proposal</button>
    </div>
</form>

<script>
document.querySelectorAll('.pane-qty, .pane-rate').forEach(el => {
    el.addEventListener('change', function() { paneRecalculateRow(this); });
    el.addEventListener('keyup', function() { paneRecalculateRow(this); });
});

function paneRecalculateRow(element) {
    const row = element.closest('.pane-line-item');
    const qty = parseFloat(row.querySelector('.pane-qty').value) || 0;
    const rate = parseFloat(row.querySelector('.pane-rate').value) || 0;
    const amount = qty * rate;
    row.querySelector('.pane-amount-input').value = amount.toFixed(2);
    row.querySelector('.pane-amount-display').textContent = amount.toFixed(2);
    paneRecalculateAll();
}

function paneRecalculateAll() {
    let subtotal = 0;
    document.querySelectorAll('.pane-line-item').forEach(row => {
        subtotal += parseFloat(row.querySelector('.pane-amount-input').value) || 0;
    });
    document.querySelector('.pane-subtotal').value = subtotal.toFixed(2);
    document.querySelector('.pane-total').value = subtotal.toFixed(2);
}

paneRecalculateAll();
</script>