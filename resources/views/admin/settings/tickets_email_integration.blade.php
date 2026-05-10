@extends('layouts.admin')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Ticket Email Integration</h1>
            <p class="text-slate-500 text-sm">Configure IMAP settings to automatically convert emails into support tickets.</p>
        </div>
        <a href="{{ route('admin.settings.ticket_email_integration.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-all">
            <i class="bi bi-plus-lg mr-2"></i> Add New Integration
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Email / Server</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Department / Priority</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Last Run</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($integrations as $integration)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700">{{ $integration->email }}</span>
                            <span class="text-xs text-slate-400">{{ $integration->imap_server }}:{{ $integration->port }} ({{ $integration->encryption ?: 'No Encryption' }})</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-slate-600">{{ $integration->department->name ?? 'N/A' }}</span>
                            <span class="text-xs text-slate-400">Priority: {{ $integration->priority->name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($integration->active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-[10px] font-bold uppercase">Active</span>
                        @else
                            <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-bold uppercase">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs text-slate-500">{{ $integration->last_run ? $integration->last_run->diffForHumans() : 'Never' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.settings.ticket_email_integration.edit', $integration->id) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.settings.ticket_email_integration.destroy', $integration->id) }}" method="POST" onsubmit="return confirm('Delete this integration?');" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="max-w-xs mx-auto">
                            <i class="bi bi-envelope-at text-4xl text-slate-200 mb-4 block"></i>
                            <h3 class="text-slate-800 font-bold mb-1">No Email Integrations</h3>
                            <p class="text-slate-400 text-xs">Configure your first IMAP mailbox to start receiving tickets via email.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
