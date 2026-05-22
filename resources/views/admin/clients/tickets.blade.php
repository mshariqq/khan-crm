@extends('layouts.admin')

@section('breadcrumb', 'Client Tickets')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-ticket-perforated text-orange-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Tickets</h2>
                    <p class="text-xs text-slate-500">{{ $data->total() }} records</p>
                </div>
            </div>
            <a href="{{ route('admin.tickets.create', ['client_id' => $client->userid]) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> New Ticket
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $ticket)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.tickets.show', $ticket->ticketid) }}" class="text-sm font-semibold text-slate-800 hover:text-blue-600">{{ $ticket->subject }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $ticket->departmentRel->name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">{{ $ticket->statusRel->name ?? 'Unknown' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                    @php
                                        $pName = strtolower($ticket->priorityRel->name ?? '');
                                    @endphp
                                    @if(str_contains($pName, 'high') || str_contains($pName, 'critical'))
                                        bg-red-100 text-red-700
                                    @elseif(str_contains($pName, 'medium'))
                                        bg-amber-100 text-amber-700
                                    @else
                                        bg-blue-100 text-blue-700
                                    @endif
                                ">{{ $ticket->priorityRel->name ?? 'Normal' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($ticket->date)->format('M j, Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.tickets.show', $ticket->ticketid) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View Ticket">
                                    <i class="bi bi-eye text-lg"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-ticket-perforated text-4xl block mb-2"></i>
                                <p class="text-sm font-medium">No tickets found</p>
                                <p class="text-xs mt-1">Support tickets from this client will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($data->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $data->appends(['tab' => 'tickets'])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
