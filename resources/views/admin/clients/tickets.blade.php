@extends('layouts.admin')

@section('breadcrumb', 'Client Tickets')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="space-y-4">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Subject</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Priority</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $ticket)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $ticket->subject }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">{{ $ticket->status->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">{{ $ticket->priority->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="/admin/tickets/{{ $ticket->ticketid }}" class="text-blue-600 hover:underline text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="bi bi-ticket-perforated text-3xl"></i>
                                    <p>No tickets found yet</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $data->appends(['tab' => 'tickets'])->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
