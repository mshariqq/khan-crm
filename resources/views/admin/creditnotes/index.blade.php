@extends('layouts.admin')

@section('breadcrumb', 'Credit Notes')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Credit Notes</h1>
            <p class="text-slate-500 text-sm">Manage credit notes issued to clients.</p>
        </div>
        @can('credit_notes_create')
        <a href="{{ route('admin.creditnotes.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Create Credit Note
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
                    <p class="text-xs font-semibold text-slate-500 uppercase">Total Value</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($totalAmount, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-purple-50 rounded-xl"><i class="bi bi-people text-purple-600 text-xl"></i></div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Active Clients</p>
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
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Number</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Client</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Date</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Total</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Remaining</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($creditNotes as $cn)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.creditnotes.show', $cn->id) }}" class="font-bold text-blue-600 hover:text-blue-800">
                                {{ $cn->prefix }}{{ str_pad($cn->number, 4, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $cn->client->company ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($cn->date)->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-slate-800">
                            {{ $cn->currencyRel->symbol ?? '$' }}{{ number_format($cn->total, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium {{ $cn->remainingCredit() > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                            {{ $cn->currencyRel->symbol ?? '$' }}{{ number_format($cn->remainingCredit(), 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusMap = [
                                    1 => ['label' => 'Open', 'class' => 'bg-blue-50 text-blue-700'],
                                    2 => ['label' => 'Closed', 'class' => 'bg-slate-100 text-slate-600'],
                                    3 => ['label' => 'Void', 'class' => 'bg-rose-50 text-rose-700'],
                                ];
                                $s = $statusMap[$cn->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-50 text-slate-600'];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $s['class'] }}">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.creditnotes.show', $cn->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View"><i class="bi bi-eye text-lg"></i></a>
                            @can('credit_notes_edit')
                            <a href="{{ route('admin.creditnotes.edit', $cn->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit"><i class="bi bi-pencil-square text-lg"></i></a>
                            @endcan
                            @can('credit_notes_delete')
                            <form action="{{ route('admin.creditnotes.destroy', $cn->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure? This will also remove applied credits.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete"><i class="bi bi-trash text-lg"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-10 text-center text-slate-500">No credit notes found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">{{ $creditNotes->links() }}</div>
    </div>
</div>
@endSection
