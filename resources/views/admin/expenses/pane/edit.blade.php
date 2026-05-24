<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Edit Expense</h1>
        <p class="text-slate-500 text-sm">Update the details of {{ $expense->expense_name }}.</p>
    </div>
</div>

<form action="{{ route('admin.expenses.update', $expense->id) }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.expenses.pane.show', $expense->id) }}" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    @csrf
    @method('PUT')
    <div class="p-6 space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Expense Details</h3>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Expense Name <span class="text-red-500">*</span></label>
                    <input type="text" name="expense_name" value="{{ old('expense_name', $expense->expense_name) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Category</label>
                        <select name="category" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category', $expense->category) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Currency <span class="text-red-500">*</span></label>
                        <select name="currency" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                            <option value="">Select Currency</option>
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->id }}" {{ old('currency', $expense->currency) == $curr->id ? 'selected' : '' }}>{{ $curr->name }} ({{ $curr->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Amount <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount', $expense->amount) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ old('date', $expense->date) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Reference No</label>
                        <input type="text" name="reference_no" value="{{ old('reference_no', $expense->reference_no) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Payment Mode</label>
                        <select name="paymentmode" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">Select Mode</option>
                            @foreach($paymentModes as $pm)
                                <option value="{{ $pm->name }}" {{ old('paymentmode', $expense->paymentmode) == $pm->name ? 'selected' : '' }}>{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="billable" value="0">
                    <input type="checkbox" name="billable" value="1" id="billable" {{ old('billable', $expense->billable) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <label for="billable" class="text-sm font-medium text-slate-600">This expense is billable</label>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Associations</h3>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Client <span class="text-red-500">*</span></label>
                    <select name="clientid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->userid }}" {{ old('clientid', $expense->clientid) == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Project</label>
                    <select name="project_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <option value="">No Project</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('project_id', $expense->project_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Invoice (Link to)</label>
                    <select name="invoiceid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <option value="">No Invoice</option>
                        @foreach($invoices as $inv)
                            <option value="{{ $inv->id }}" {{ old('invoiceid', $expense->invoiceid) == $inv->id ? 'selected' : '' }}>{{ $inv->number ?? '#' . $inv->id }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-600">Notes</label>
            <textarea name="note" rows="4" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Enter additional details about this expense...">{{ old('note', $expense->note) }}</textarea>
        </div>
    </div>

    <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
        <button type="button" onclick="parent.closeTaskPane()" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</button>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Expense</button>
    </div>
</form>
