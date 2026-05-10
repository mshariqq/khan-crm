@extends('layouts.client')

@section('title', 'Support Tickets')
@section('breadcrumb', 'Tickets')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Support Tickets</h1>
            <p class="text-sm text-slate-500">View and manage your support requests</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('client.tickets.export', request()->all()) }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <a href="{{ route('client.tickets.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Open New Ticket
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('client.tickets.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
            </div>
            <div class="w-full md:w-48">
                <select name="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Open</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>In Progress</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Answered</option>
                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>On Hold</option>
                    <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-900 transition-all">
                    Apply Filters
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('client.tickets.index') }}" class="px-4 py-2.5 text-slate-500 text-sm font-bold hover:text-slate-700 transition-all">
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
                        <th class="px-6 py-4">Department</th>
                        <th class="px-6 py-4 text-center">Priority</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4">Last Reply</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('client.tickets.show', $ticket->ticketid) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">
                                {{ $ticket->subject }}
                            </a>
                            <p class="text-[10px] text-slate-400 mt-0.5">#{{ $ticket->ticketid }}</p>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $ticket->departmentRel->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $priorityColors = [
                                    1 => 'bg-emerald-50 text-emerald-600',
                                    2 => 'bg-amber-50 text-amber-600',
                                    3 => 'bg-rose-50 text-rose-600'
                                ];
                                $p = ($ticket->priorityRel && $ticket->priorityRel->priorityid) ? $ticket->priorityRel->priorityid : 1;
                            @endphp
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ $priorityColors[$p] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $ticket->priorityRel->name ?? 'Low' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-blue-50 text-blue-600">
                                {{ $ticket->statusRel->name ?? 'Open' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $ticket->lastreply ? \Carbon\Carbon::parse($ticket->lastreply)->diffForHumans() : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('client.tickets.show', $ticket->ticketid) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition-colors">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-ticket-perforated text-3xl text-slate-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">No tickets found</h3>
                            <p class="text-slate-500">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($tickets->hasPages())
    <div class="mt-8">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
