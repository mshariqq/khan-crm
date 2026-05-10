@extends('layouts.admin')

@section('breadcrumb', 'Credit Notes')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Credit Notes</h1>
            <p class="text-slate-500 text-sm">Manage credit notes applied to invoices.</p>
        </div>
        @can('credit_notes_create')
        <a href="{{ route('admin.creditnotes.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add Credit Note
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium animate-fade-in">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium animate-fade-in">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-50 rounded-xl"><i class="bi bi-receipt-cutoff text-blue-600 text-xl"></i></div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Total Credit Notes</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $total }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-green-50 rounded-xl"><i class="bi bi-currency-dollar text-green-600 text-xl"></i></div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Total Amount</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($totalAmount, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-purple-50 rounded-xl"><i class="bi bi-people text-purple-600 text-xl"></i></div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Clients</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $clients->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('admin.creditnotes.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Client</label>
                <select name="client" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->userid }}" {{ request('client') == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-all">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('admin.creditnotes.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition-all">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">ID</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Invoice #</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Amount</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Date Applied</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Staff</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($credits as $credit)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">#{{ $credit->id }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $credit->invoice->number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-800">
                            {{ number_format($credit->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $credit->date_applied ? \Carbon\Carbon::parse($credit->date_applied)->format('M j, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $credit->staff->firstname ?? 'N/A' }} {{ $credit->staff->lastname ?? '' }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.creditnotes.show', $credit->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View"><i class="bi bi-eye text-lg"></i></a>
                            @can('credit_notes_edit')
                            <a href="{{ route('admin.creditnotes.edit', $credit->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit"><i class="bi bi-pencil-square text-lg"></i></a>
                            @endcan
                            @can('credit_notes_delete')
                            <form action="{{ route('admin.creditnotes.destroy', $credit->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this credit note?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete"><i class="bi bi-trash text-lg"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-slate-500">No credit notes found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">{{ $credits->links() }}</div>
    </div>
</div>
@endSection