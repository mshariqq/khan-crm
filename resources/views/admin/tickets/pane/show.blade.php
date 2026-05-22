<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-lg font-bold text-slate-800">{{ $ticket->subject }}</h1>
        <p class="text-xs text-slate-400">Ticket #{{ $ticket->ticketid }} · {{ $ticket->statusRel->name ?? 'Open' }}</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="parent.openTicketPane('{{ route('admin.tickets.pane.edit', $ticket->ticketid) }}')" class="px-3 py-1.5 bg-amber-500 text-white text-xs font-medium rounded-lg hover:bg-amber-600 transition-all flex items-center gap-1">
            <i class="bi bi-pencil"></i> Edit
        </button>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
    <div class="space-y-4">
        <div class="flex items-center gap-4">
            <div>
                <span class="text-[10px] text-slate-400 uppercase block mb-1">Customer</span>
                <p class="text-sm text-slate-700">{{ $ticket->name ?: 'N/A' }}</p>
                @if($ticket->email)
                    <p class="text-xs text-slate-400">{{ $ticket->email }}</p>
                @endif
            </div>
            <div class="text-right ml-auto">
                <span class="text-[10px] text-slate-400 uppercase block mb-1">Priority</span>
                @php $p = $ticket->priorityRel->priorityid ?? 1; @endphp
                <span class="px-2 py-0.5 rounded text-[10px] font-medium {{ $p == 3 ? 'bg-red-100 text-red-700' : ($p == 2 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">{{ $ticket->priorityRel->name ?? 'Low' }}</span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <span class="text-[10px] text-slate-400 uppercase block mb-1">Department</span>
                <p class="text-sm text-slate-700">{{ $ticket->departmentRel->name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 uppercase block mb-1">Assigned To</span>
                <p class="text-sm text-slate-700">{{ $ticket->assignedStaff->firstname ?? 'Unassigned' }} {{ $ticket->assignedStaff->lastname ?? '' }}</p>
            </div>
            @if($ticket->client)
            <div>
                <span class="text-[10px] text-slate-400 uppercase block mb-1">Client</span>
                <p class="text-sm text-slate-700">{{ $ticket->client->company }}</p>
            </div>
            @endif
            @if($ticket->project)
            <div>
                <span class="text-[10px] text-slate-400 uppercase block mb-1">Project</span>
                <p class="text-sm text-blue-600 hover:underline"><a href="{{ route('admin.projects.show', $ticket->project->id) }}">{{ $ticket->project->name }}</a></p>
            </div>
            @endif
        </div>
        @if($ticket->message)
        <div>
            <span class="text-[10px] text-slate-400 uppercase block mb-1">Message</span>
            <div class="text-sm text-slate-600 p-4 bg-slate-50 rounded-lg border border-slate-100">{!! $ticket->message !!}</div>
        </div>
        @endif
    </div>
</div>

@if($ticket->replies->count() > 0)
<div class="mt-6">
    <h3 class="text-sm font-semibold text-slate-700 mb-3">Replies ({{ $ticket->replies->count() }})</h3>
    <div class="space-y-3">
        @foreach($ticket->replies as $reply)
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-slate-700">{{ $reply->name }}</span>
                    @if($reply->admin)
                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[9px] font-bold uppercase">Staff</span>
                    @endif
                </div>
                <span class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($reply->date)->format('M d, Y · g:i A') }}</span>
            </div>
            <div class="text-sm text-slate-600">{!! $reply->message !!}</div>
        </div>
        @endforeach
    </div>
</div>
@endif
