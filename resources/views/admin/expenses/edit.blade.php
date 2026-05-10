@extends('layouts.admin')

@section('breadcrumb', 'Edit Expense')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Expense</h1>
            <p class="text-slate-500 text-sm">Update the details of {{ $expense->expense_name }}.</p>
        </div>
        <a href="{{ route('admin.expenses.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.expenses.update', $expense->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Expense Details</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Expense Name <span class="text-red-500">*</span></label>
                        <input type="text" name="expense_name" value="{{ old('expense_name', $expense->expense_name) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('expense_name') border-red-500 @enderror" required>
                        @error('expense_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Category</label>
                        <select name="category" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('category') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category', $expense->category) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Currency <span class="text-red-500">*</span></label>
                            <select name="currency" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('currency') border-red-500 @enderror" required>
                                <option value="">Select Currency</option>
                                @foreach($currencies as $curr)
                                    <option value="{{ $curr->id }}" {{ old('currency', $expense->currency) == $curr->id ? 'selected' : '' }}>{{ $curr->name }} ({{ $curr->symbol }})</option>
                                @endforeach
                            </select>
                            @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Amount <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="amount" value="{{ old('amount', $expense->amount) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('amount') border-red-500 @enderror" required>
                            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Tax (%)</label>
                            <input type="number" name="tax" value="{{ old('tax', $expense->tax) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0">
                            @error('tax') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="date" value="{{ old('date', $expense->date) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('date') border-red-500 @enderror" required>
                            @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Reference No</label>
                            <input type="text" name="reference_no" value="{{ old('reference_no', $expense->reference_no) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            @error('reference_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Payment Mode</label>
                        <select name="paymentmode" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('paymentmode') border-red-500 @enderror">
                            <option value="">Select Mode</option>
                            @foreach($paymentModes as $pm)
                                <option value="{{ $pm->name }}" {{ old('paymentmode', $expense->paymentmode) == $pm->name ? 'selected' : '' }}>{{ $pm->name }}</option>
                            @endforeach
                        </select>
                        @error('paymentmode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="billable" value="0">
                        <input type="checkbox" name="billable" value="1" id="billable" {{ old('billable', $expense->billable) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <label for="billable" class="text-sm font-medium text-slate-600">This expense is billable</label>
                    </div>
                    @error('billable') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Associations</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Client <span class="text-red-500">*</span></label>
                        <select name="clientid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('clientid') border-red-500 @enderror" required>
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->userid }}" {{ old('clientid', $expense->clientid) == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                            @endforeach
                        </select>
                        @error('clientid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Project</label>
                        <select name="project_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('project_id') border-red-500 @enderror">
                            <option value="">No Project</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ old('project_id', $expense->project_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Invoice (Link to)</label>
                        <select name="invoiceid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">No Invoice</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" {{ old('invoiceid', $expense->invoiceid) == $inv->id ? 'selected' : '' }}>{{ $inv->number ?? '#' . $inv->id }} — {{ number_format($inv->total ?? 0, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Recurring Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Is Recurring?</label>
                        <select name="recurring" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="0" {{ old('recurring', $expense->recurring) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('recurring', $expense->recurring) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Type</label>
                        <select name="recurring_type" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">Select Type</option>
                            <option value="daily" {{ old('recurring_type', $expense->recurring_type) == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ old('recurring_type', $expense->recurring_type) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ old('recurring_type', $expense->recurring_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('recurring_type', $expense->recurring_type) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Repeat Every</label>
                        <input type="number" name="repeat_every" value="{{ old('repeat_every', $expense->repeat_every) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="1">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Cycles</label>
                        <input type="number" name="cycles" value="{{ old('cycles', $expense->cycles) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Total Cycles</label>
                        <input type="number" name="total_cycles" value="{{ old('total_cycles', $expense->total_cycles) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Custom Recurring</label>
                        <select name="custom_recurring" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="0" {{ old('custom_recurring', $expense->custom_recurring) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('custom_recurring', $expense->custom_recurring) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-6 py-2">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="create_invoice_billable" value="1" id="create_invoice" {{ old('create_invoice_billable', $expense->create_invoice_billable) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <label for="create_invoice" class="text-sm font-medium text-slate-600">Create Invoice if Billable</label>
                    </div>
                    @error('create_invoice_billable') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="send_invoice_to_customer" value="1" id="send_invoice" {{ old('send_invoice_to_customer', $expense->send_invoice_to_customer) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <label for="send_invoice" class="text-sm font-medium text-slate-600">Send Invoice to Customer</label>
                    </div>
                    @error('send_invoice_to_customer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Notes</label>
                <textarea name="note" rows="4" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('note') border-red-500 @enderror" placeholder="Enter additional details about this expense...">{{ old('note', $expense->note) }}</textarea>
                @error('note') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.expenses.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Expense</button>
        </div>
    </form>
</div>
@endsection
