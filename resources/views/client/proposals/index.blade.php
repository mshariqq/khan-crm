@extends('layouts.client')

@section('title', 'My Proposals')
@section('breadcrumb', 'Proposals')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">My Proposals</h1>
            <p class="text-sm text-slate-500">View and manage your project proposals</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('client.proposals.export', request()->all()) }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('client.proposals.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search proposals..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
            </div>
            <div class="w-full md:w-48">
                <select name="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Open</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Accepted</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Declined</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-900 transition-all">
                    Apply Filters
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('client.proposals.index') }}" class="px-4 py-2.5 text-slate-500 text-sm font-bold hover:text-slate-700 transition-all">
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
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Open Till</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($proposals as $proposal)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('client.proposals.show', $proposal->id) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">
                                {{ $proposal->subject }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ \Carbon\Carbon::parse($proposal->date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $proposal->open_till ? \Carbon\Carbon::parse($proposal->open_till)->format('M d, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-800">
                                {{ app_format_money($proposal->total, $proposal->currency) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusMap = [
                                    1 => ['label' => 'Open', 'class' => 'bg-blue-50 text-blue-600'],
                                    2 => ['label' => 'Accepted', 'class' => 'bg-emerald-50 text-emerald-600'],
                                    3 => ['label' => 'Declined', 'class' => 'bg-rose-50 text-rose-600'],
                                ];
                                $s = $statusMap[$proposal->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];
                            @endphp
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ $s['class'] }}">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('client.proposals.show', $proposal->id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition-colors">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-file-earmark-text text-3xl text-slate-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">No proposals found</h3>
                            <p class="text-slate-500">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($proposals->hasPages())
    <div class="mt-8">
        {{ $proposals->links() }}
    </div>
    @endif
</div>
@endsection
