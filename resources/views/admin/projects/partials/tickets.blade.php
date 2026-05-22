<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Tickets ({{ $ticketCount }})</h3>
    <a href="{{ route('admin.tickets.create') }}?project_id={{ $project->id }}" data-pane="create" data-pane-type="ticket" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Ticket
    </a>
</div>

@if($ticketCount > 0)
@php
$statusGroups = [
    4 => ['label' => 'Closed', 'color' => 'emerald', 'icon' => 'bi-check-circle-fill'],
    3 => ['label' => 'On Hold', 'color' => 'amber', 'icon' => 'bi-pause-circle-fill'],
    2 => ['label' => 'In Progress', 'color' => 'blue', 'icon' => 'bi-arrow-repeat'],
    1 => ['label' => 'Open', 'color' => 'slate', 'icon' => 'bi-inbox-fill'],
];
$groupedTickets = $tickets->groupBy('status');
$order = [4, 3, 2, 1];
@endphp

<div class="space-y-6">
    @foreach($order as $status)
        @if(isset($groupedTickets[$status]) && $groupedTickets[$status]->count() > 0)
        <div class="relative">
            <div class="sticky top-0 z-10 bg-white pt-2 pb-1">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center bg-{{ $statusGroups[$status]['color'] }}-100">
                        <i class="bi {{ $statusGroups[$status]['icon'] }} text-{{ $statusGroups[$status]['color'] }}-600 text-xs"></i>
                    </div>
                    <span class="text-sm font-bold text-{{ $statusGroups[$status]['color'] }}-700">{{ $statusGroups[$status]['label'] }}</span>
                    <span class="px-2 py-0.5 text-[10px] font-medium bg-{{ $statusGroups[$status]['color'] }}-100 text-{{ $statusGroups[$status]['color'] }}-700 rounded-full">{{ $groupedTickets[$status]->count() }}</span>
                </div>
            </div>
            <div class="mt-2 space-y-2 border-l-2 border-{{ $statusGroups[$status]['color'] }}-200 pl-4">
                @foreach($groupedTickets[$status] as $ticket)
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group border border-slate-100 hover:border-slate-200">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-{{ $statusGroups[$status]['color'] }}-100">
                            <i class="bi bi-headset text-sm text-{{ $statusGroups[$status]['color'] }}-600"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <a href="{{ route('admin.tickets.show', $ticket->ticketid) }}" data-pane="show" data-pane-type="ticket" data-pane-id="{{ $ticket->ticketid }}" class="text-sm font-medium text-slate-700 hover:text-blue-600 truncate">
                                    #{{ $ticket->ticketkey }} - {{ $ticket->subject }}
                                </a>
                                @php
                                $priorityColors = [
                                    1 => ['bg' => 'slate-100', 'text' => 'slate-600', 'label' => 'Low'],
                                    2 => ['bg' => 'blue-100', 'text' => 'blue-700', 'label' => 'Medium'],
                                    3 => ['bg' => 'amber-100', 'text' => 'amber-700', 'label' => 'High'],
                                    4 => ['bg' => 'red-100', 'text' => 'red-700', 'label' => 'Urgent'],
                                ];
                                $priority = $ticket->priority && is_object($ticket->priority) ? $ticket->priority->priorityid : $ticket->priority;
                                @endphp
                                @if($priority && isset($priorityColors[$priority]))
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-{{ $priorityColors[$priority]['bg'] }} text-{{ $priorityColors[$priority]['text'] }}">{{ $priorityColors[$priority]['label'] }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-[10px] text-slate-400">
                                <span>{{ $ticket->date ? \Carbon\Carbon::parse($ticket->date)->format('M j, Y') : '' }}</span>
                                @if($ticket->department && is_object($ticket->department))
                                <span class="px-1.5 py-0.5 bg-slate-100 rounded">{{ $ticket->department->name }}</span>
                                @elseif($ticket->department)
                                <span class="px-1.5 py-0.5 bg-slate-100 rounded">Dept #{{ $ticket->department }}</span>
                                @endif
                                @if($ticket->assignedStaff && is_object($ticket->assignedStaff))
                                <span class="flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-slate-500 flex items-center justify-center text-white text-[8px] font-medium">
                                        {{ substr($ticket->assignedStaff->firstname, 0, 1) }}
                                    </span>
                                    <span class="text-slate-600 font-medium">{{ $ticket->assignedStaff->firstname }} {{ $ticket->assignedStaff->lastname }}</span>
                                </span>
                                @elseif($ticket->assigned)
                                <span class="flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-slate-400 flex items-center justify-center text-white text-[8px]">?</span>
                                    <span class="text-slate-500">{{ $ticket->assigned }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('admin.tickets.show', $ticket->ticketid) }}" data-pane="show" data-pane-type="ticket" data-pane-id="{{ $ticket->ticketid }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="View">
                            <i class="bi bi-eye text-xs"></i>
                        </a>
                        <a href="{{ route('admin.tickets.edit', $ticket->ticketid) }}" data-pane="edit" data-pane-type="ticket" data-pane-id="{{ $ticket->ticketid }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="Edit">
                            <i class="bi bi-pencil text-xs"></i>
                        </a>
                        <form action="{{ route('admin.tickets.destroy', $ticket->ticketid) }}" method="POST" class="inline" onsubmit="return confirm('Delete this ticket?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete">
                                <i class="bi bi-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        </div>
        @endif
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-headset text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No tickets yet</p>
    <a href="{{ route('admin.tickets.create') }}?project_id={{ $project->id }}" data-pane="create" data-pane-type="ticket" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Ticket</a>
</div>
@endif