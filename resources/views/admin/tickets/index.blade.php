@extends('layouts.admin')

@section('breadcrumb', 'Tickets')

@section('main')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Tickets</h1>
            <p class="text-xs text-slate-400">Manage support tickets</p>
        </div>
        <a href="{{ route('admin.tickets.create') }}" data-pane="create" data-pane-type="ticket" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-all flex items-center gap-1">
            <i class="bi bi-plus"></i> New Ticket
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <form action="{{ route('admin.tickets.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="department" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->departmentid }}" {{ request('department') == $dept->departmentid ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>
            <select name="priority" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                <option value="">All Priorities</option>
                @foreach($priorities as $priority)
                    <option value="{{ $priority->priorityid }}" {{ request('priority') == $priority->priorityid ? 'selected' : '' }}>{{ $priority->name }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->ticketstatusid }}" {{ request('status') == $status->ticketstatusid ? 'selected' : '' }}>{{ $status->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded-lg hover:bg-slate-900">Filter</button>
            <a href="{{ route('admin.tickets.index') }}" class="px-3 py-1.5 text-slate-500 text-xs hover:text-slate-700">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Ticket</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Customer</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Dept</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Priority</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Assigned</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Last Reply</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        <div>
                            <a href="{{ route('admin.tickets.show', $ticket->ticketid) }}" data-pane="show" data-pane-type="ticket" data-pane-id="{{ $ticket->ticketid }}" class="text-sm font-medium text-slate-800 hover:text-blue-600">{{ $ticket->subject }}</a>
                            <p class="text-[10px] text-slate-400">#{{ $ticket->ticketid }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if($ticket->client)
                        <a href="{{ route('admin.clients.show', $ticket->client->userid) }}" class="text-xs text-slate-600 hover:text-blue-600">{{ $ticket->client->company }}</a>
                        @elseif($ticket->name)
                        <span class="text-xs text-slate-600">{{ $ticket->name }}</span>
                        @else
                        <span class="text-xs text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-600">
                        {{ $ticket->departmentRel->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3">
                        @php 
                            $priorityColors = ['bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700', 'bg-red-100 text-red-700'];
                            $p = ($ticket->priorityRel && $ticket->priorityRel->priorityid) ? $ticket->priorityRel->priorityid : 1;
                        @endphp
                        <span class="px-2 py-0.5 rounded text-[10px] font-medium {{ $priorityColors[$p-1] }}">{{ $ticket->priorityRel->name ?? 'Low' }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-700">{{ $ticket->statusRel->name ?? 'Open' }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($ticket->assignedStaff)
                        <span class="inline-flex items-center gap-1">
                            <span class="w-5 h-5 rounded-full bg-slate-400 flex items-center justify-center text-white text-[8px] font-semibold">{{ substr($ticket->assignedStaff->firstname, 0, 1) }}</span>
                            <span class="text-xs text-slate-600">{{ $ticket->assignedStaff->firstname }}</span>
                        </span>
                        @else
                        <span class="text-xs text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-500">
                        @if($ticket->lastreply)
                            {{ \Carbon\Carbon::parse($ticket->lastreply)->diffForHumans() }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.tickets.show', $ticket->ticketid) }}" data-pane="show" data-pane-type="ticket" data-pane-id="{{ $ticket->ticketid }}" class="p-1.5 text-slate-400 hover:text-blue-600" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.tickets.edit', $ticket->ticketid) }}" data-pane="edit" data-pane-type="ticket" data-pane-id="{{ $ticket->ticketid }}" class="p-1.5 text-slate-400 hover:text-amber-600" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.tickets.destroy', $ticket->ticketid) }}" method="POST" class="inline" onsubmit="return confirm('Delete?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                        No tickets found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($tickets->hasPages())
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection