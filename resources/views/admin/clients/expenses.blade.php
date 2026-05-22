@extends('layouts.admin')

@section('breadcrumb', 'Client Expenses')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-cash-stack text-rose-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Expenses</h2>
                    <p class="text-xs text-slate-500">{{ $data->total() }} records</p>
                </div>
            </div>
            <a href="{{ route('admin.expenses.create', ['client_id' => $client->userid]) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> New Expense
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Billable</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $expense)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($expense->date)->format('M j, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-slate-800">{{ $expense->expense_name }}</span>
                                @if($expense->note)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ Str::limit($expense->note, 60) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $expense->categoryRel->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-800">{{ ($expense->currencyRel->symbol ?? '$') . number_format($expense->amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $expense->billable ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $expense->billable ? 'Billable' : 'Not Billable' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.expenses.show', $expense->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View Expense">
                                    <i class="bi bi-eye text-lg"></i>
                                </a>
                                <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="p-2 text-slate-400 hover:text-amber-600 transition-colors" title="Edit Expense">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-cash-stack text-4xl block mb-2"></i>
                                <p class="text-sm font-medium">No expenses found</p>
                                <p class="text-xs mt-1">Log expenses to track spending for this client.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($data->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $data->appends(['tab' => 'expenses'])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
