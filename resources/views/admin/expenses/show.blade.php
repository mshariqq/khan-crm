@extends('layouts.admin')

@section('breadcrumb', 'Expense Detail')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.expenses.index') }}" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $expense->expense_name }}</h1>
                <p class="text-slate-500 text-sm">{{ $expense->categoryRel->name ?? 'Uncategorized' }}</p>
            </div>
            <span class="ml-2">
                @if($expense->billable)
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i class="bi bi-check-circle me-1"></i>Billable</span>
                @else
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-medium">Non-Billable</span>
                @endif
            </span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Expense Information</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-slate-500">Amount</p>
                        <p class="text-2xl font-bold text-slate-800">{{ number_format($expense->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Category</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $expense->categoryRel->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Payment Mode</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $expense->paymentmode ? \App\Models\PaymentMode::find($expense->paymentmode)->name ?? 'N/A' : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Date</p>
                        <p class="text-sm font-semibold text-slate-800">{{ \Carbon\Carbon::parse($expense->date)->format('M j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Reference No</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $expense->reference_no ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Created By</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $expense->creator->firstname ?? '' }} {{ $expense->creator->lastname ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Date Added</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $expense->dateadded ? \Carbon\Carbon::parse($expense->dateadded)->format('M j, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Billable</p>
                        <p class="text-sm font-semibold">
                            @if($expense->billable)
                                <span class="text-green-600">Yes — this expense can be billed to the client</span>
                            @else
                                <span class="text-slate-600">No — internal expense</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if($expense->note)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Notes</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $expense->note }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Linked Records</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold tracking-wide">Client</p>
                        @if($expense->client)
                            <a href="{{ route('admin.clients.show', $expense->client->userid) }}" class="text-sm font-semibold text-slate-800 mt-1 hover:text-blue-600">{{ $expense->client->company }}</a>
                            <p class="text-xs text-slate-500">{{ $expense->client->email ?? '' }}</p>
                        @else
                            <p class="text-sm text-slate-500 mt-1">No client linked</p>
                        @endif
                    </div>
                    <div class="border-t pt-3">
                        <p class="text-xs text-slate-500 uppercase font-semibold tracking-wide">Project</p>
                        @if($expense->project)
                            <a href="{{ route('admin.projects.show', $expense->project->id) }}" class="text-sm font-semibold text-slate-800 mt-1 hover:text-blue-600">{{ $expense->project->name }}</a>
                            <p class="text-xs text-slate-500">Status: {{ $expense->project->status == 1 ? 'Active' : ($expense->project->status == 2 ? 'Completed' : 'On Hold') }}</p>
                        @else
                            <p class="text-sm text-slate-500 mt-1">No project linked</p>
                        @endif
                    </div>
                    <div class="border-t pt-3">
                        <p class="text-xs text-slate-500 uppercase font-semibold tracking-wide">Invoice</p>
                        @if($expense->invoice)
                            <p class="text-sm font-semibold text-slate-800 mt-1">{{ $expense->invoice->number ?? '#' . $expense->invoice->id }}</p>
                        @else
                            <p class="text-sm text-slate-500 mt-1">Not linked to an invoice</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection