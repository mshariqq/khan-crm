@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('breadcrumb', 'Dashboard')

@section('main')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ $user->firstname }}!</h1>
            <p class="text-gray-500 mt-1">Here's what's happening in your CRM today.</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    {{-- Attendance Widget --}}
    <div class="lg:col-span-1">
        @include('admin.partials.attendance_widget')
    </div>

    {{-- Stats Grid --}}
    <div class="lg:col-span-3 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="bi bi-people text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Clients</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_clients'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <i class="bi bi-kanban-fill text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Projects</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['active_projects'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                    <i class="bi bi-list-task text-amber-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Tasks</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['pending_tasks'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="bi bi-receipt text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Overdue</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['overdue_invoices'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="bi bi-ticket-perforated-fill text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Tickets</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['open_tickets'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl border border-gray-100">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Latest Projects</h3>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($projects as $project)
            <a href="{{ route('admin.projects.show', $project->id) }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-kanban text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $project->name }}</p>
                        <p class="text-xs text-gray-500">{{ $project->client->company ?? 'No client' }}</p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $project->status == 1 ? 'bg-emerald-100 text-emerald-700' : ($project->status == 2 ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ $project->status == 1 ? 'Active' : ($project->status == 2 ? 'Completed' : 'On Hold') }}
                </span>
            </a>
            @empty
            <div class="p-8 text-center text-gray-500 text-sm">No recent projects</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Latest Tickets</h3>
            <a href="{{ route('admin.tickets.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($tickets as $ticket)
            <a href="{{ route('admin.tickets.show', $ticket->ticketid) }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                        <i class="bi bi-ticket text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 truncate max-w-[200px]">{{ $ticket->subject }}</p>
                        <p class="text-xs text-gray-500">{{ $ticket->statusRel->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <i class="bi bi-chevron-right text-gray-400 text-sm"></i>
            </a>
            @empty
            <div class="p-8 text-center text-gray-500 text-sm">No recent tickets</div>
            @endforelse
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl border border-gray-100">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Latest Tasks</h3>
            <a href="{{ route('admin.tasks.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($tasks as $task)
            <a href="{{ route('admin.tasks.show', $task->id) }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg {{ $task->status ? 'bg-emerald-50' : 'bg-amber-50' }} flex items-center justify-center">
                        <i class="bi {{ $task->status ? 'bi-check-circle-fill text-emerald-600' : 'bi-circle text-amber-600' }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>
                        <p class="text-xs text-gray-500">{{ $task->status ? 'Completed' : 'Pending' }}</p>
                    </div>
                </div>
                <i class="bi bi-chevron-right text-gray-400 text-sm"></i>
            </a>
            @empty
            <div class="p-8 text-center text-gray-500 text-sm">No recent tasks</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Latest Invoices</h3>
            <a href="{{ route('admin.invoices.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($invoices as $invoice)
            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-cyan-50 flex items-center justify-center">
                        <i class="bi bi-receipt text-cyan-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->formatted_number ?? $invoice->number }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($invoice->total, 2) }}</p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $invoice->status == 3 ? 'bg-emerald-100 text-emerald-700' : ($invoice->status == 1 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ $invoice->status == 3 ? 'Paid' : ($invoice->status == 1 ? 'Unpaid' : 'Other') }}
                </span>
            </a>
            @empty
            <div class="p-8 text-center text-gray-500 text-sm">No recent invoices</div>
            @endforelse
        </div>
    </div>
</div>

@can_do('manage_settings')
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold">System Administration</h3>
            <p class="text-blue-100 mt-1">Manage settings, roles, permissions, and more.</p>
        </div>
        <a href="/admin/settings" class="px-4 py-2 bg-white text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors">Settings</a>
    </div>
</div>
@endcan_do
@endsection