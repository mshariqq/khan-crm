<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Expenses ({{ $expenses->count() }})</h3>
    <a href="{{ route('admin.expenses.create') }}?project_id={{ $project->id }}" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Expense
    </a>
</div>

@if($expenses->count() > 0)
<div class="space-y-2">
    @foreach($expenses as $expense)
    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                <i class="bi bi-cash text-orange-600 text-sm"></i>
            </div>
            <div>
                <span class="text-sm font-medium text-slate-700">{{ $expense->expense_name ?? $expense->description ?? 'Expense' }}</span>
                <span class="text-[10px] text-slate-400 ml-2">{{ $expense->date ? \Carbon\Carbon::parse($expense->date)->format('M j, Y') : '' }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold text-slate-700">${{ number_format($expense->amount, 2) }}</span>
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('admin.expenses.show', $expense->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="View">
                    <i class="bi bi-eye text-xs"></i>
                </a>
                <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="Edit">
                    <i class="bi bi-pencil text-xs"></i>
                </a>
                <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this expense?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete">
                        <i class="bi bi-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-4 p-3 bg-slate-100 rounded-lg flex justify-between">
    <span class="text-sm font-medium text-slate-600">Total Expenses</span>
    <span class="text-sm font-bold text-slate-800">${{ number_format($expenses->sum('amount'), 2) }}</span>
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-cash-stack text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No expenses yet</p>
    <a href="{{ route('admin.expenses.create') }}?project_id={{ $project->id }}" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Expense</a>
</div>
@endif