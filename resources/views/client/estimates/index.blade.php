@extends('layouts.client')

@section('title', 'My Estimates')
@section('breadcrumb', 'Estimates')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">My Estimates</h1>
            <p class="text-sm text-slate-500">View and manage your project estimates</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('client.estimates.export', request()->all()) }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('client.estimates.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search estimates..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
            </div>
            <div class="w-full md:w-48">
                <select name="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Draft</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Sent</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Accepted</option>
                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Declined</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-900 transition-all">
                    Apply Filters
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('client.estimates.index') }}" class="px-4 py-2.5 text-slate-500 text-sm font-bold hover:text-slate-700 transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr class="text-[10px] uppercase tracking-wider font-bold text-slate-400">
                        <th class="px-6 py-4">Estimate #</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Expiry Date</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($estimates as $estimate)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('client.estimates.show', $estimate->id) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">
                                {{ $estimate->number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ \Carbon\Carbon::parse($estimate->date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $estimate->expirydate ? \Carbon\Carbon::parse($estimate->expirydate)->format('M d, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-800">
                                {{ $estimate->currencyRel->symbol ?? '$' }}{{ number_format($estimate->total, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusMap = [
                                    1 => ['label' => 'Draft', 'class' => 'bg-slate-100 text-slate-600'],
                                    2 => ['label' => 'Sent', 'class' => 'bg-blue-50 text-blue-600'],
                                    3 => ['label' => 'Accepted', 'class' => 'bg-emerald-50 text-emerald-600'],
                                    4 => ['label' => 'Declined', 'class' => 'bg-rose-50 text-rose-600'],
                                ];
                                $s = $statusMap[$estimate->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];
                            @endphp
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ $s['class'] }}">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('client.estimates.show', $estimate->id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition-colors">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-calculator text-3xl text-slate-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">No estimates found</h3>
                            <p class="text-slate-500">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($estimates->hasPages())
    <div class="mt-8">
        {{ $estimates->links() }}
    </div>
    @endif
</div>
@endsection
