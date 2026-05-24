<div class="flex items-start justify-between mb-6">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-emerald-200 flex-shrink-0">
            {{ strtoupper(substr($expense->expense_name, 0, 1)) }}
        </div>
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-xl font-bold text-slate-800">{{ $expense->expense_name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $expense->billable ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $expense->billable ? 'Billable' : 'Non-Billable' }}
                </span>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-400">
                <span>#{{ $expense->id }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>{{ $expense->categoryRel->name ?? 'Uncategorized' }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>{{ app_format_money($expense->amount, $expense->currency) }}</span>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <button onclick="parent.openExpensePane('{{ route('admin.expenses.pane.edit', $expense->id) }}')" class="px-3 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-1.5">
            <i class="bi bi-pencil"></i> Edit
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-receipt text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Expense Information</h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Amount</span>
                        <p class="mt-1 text-2xl font-bold text-slate-800">{{ app_format_money($expense->amount, $expense->currency) }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Category</span>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $expense->categoryRel->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date</span>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $expense->date ? \Carbon\Carbon::parse($expense->date)->format('M j, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Reference No</span>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $expense->reference_no ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Payment Mode</span>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $expense->paymentmode ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Created By</span>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $expense->creator->firstname ?? '' }} {{ $expense->creator->lastname ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($expense->note)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-card-text text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Notes</h3>
            </div>
            <div class="p-5 text-sm text-slate-600 leading-relaxed">{{ $expense->note }}</div>
        </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-link-45deg text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Linked Records</h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Client</span>
                    @if($expense->client)
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                            <i class="bi bi-building text-sm"></i>
                        </div>
                        <p class="text-sm font-medium text-slate-800">{{ $expense->client->company }}</p>
                    </div>
                    @else
                    <p class="text-sm text-slate-400">No client linked</p>
                    @endif
                </div>
                <div class="border-t pt-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Project</span>
                    @if($expense->project)
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0">
                            <i class="bi bi-folder text-sm"></i>
                        </div>
                        <p class="text-sm font-medium text-slate-800">{{ $expense->project->name }}</p>
                    </div>
                    @else
                    <p class="text-sm text-slate-400">No project linked</p>
                    @endif
                </div>
                <div class="border-t pt-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Invoice</span>
                    @if($expense->invoice)
                    <p class="text-sm font-medium text-slate-800">{{ $expense->invoice->number ?? '#' . $expense->invoice->id }}</p>
                    @else
                    <p class="text-sm text-slate-400">Not linked to an invoice</p>
                    @endif
                </div>
                <div class="border-t pt-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Billable</span>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $expense->billable ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                        <span class="text-sm font-medium text-slate-700">{{ $expense->billable ? 'Yes — can be billed' : 'No — internal' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
