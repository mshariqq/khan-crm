@extends('layouts.client')

@section('title', $project->name)
@section('breadcrumb', 'Project Details')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.projects.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $project->name }}</h1>
                <p class="text-sm text-slate-500">Project overview and progress</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @php
                $statusColor = 'bg-slate-100 text-slate-600';
                $statusLabel = 'Unknown';
                if($project->status == 1) {
                    $statusColor = 'bg-emerald-50 text-emerald-700';
                    $statusLabel = 'In Progress';
                } elseif($project->status == 2) {
                    $statusColor = 'bg-blue-50 text-blue-700';
                    $statusLabel = 'Completed';
                } elseif($project->status == 3) {
                    $statusColor = 'bg-amber-50 text-amber-700';
                    $statusLabel = 'On Hold';
                }
            @endphp
            <span class="px-4 py-1.5 rounded-xl text-xs font-bold {{ $statusColor }}">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Project Overview</h3>
                <div class="prose prose-slate max-w-none text-slate-600 text-sm">
                    {!! nl2br(e($project->description)) !!}
                </div>
            </div>

            <!-- Tasks -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800">Recent Tasks</h3>
                    <span class="text-xs font-bold text-slate-500">{{ $project->tasks->count() }} Total Tasks</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider font-bold text-slate-400">
                                <th class="px-6 py-3">Task Name</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Due Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($project->tasks->take(10) as $task)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-slate-800">{{ $task->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $taskStatusColor = 'bg-slate-100 text-slate-600';
                                        $taskStatusLabel = 'Unknown';
                                        if($task->status == 1) { $taskStatusColor = 'bg-blue-50 text-blue-600'; $taskStatusLabel = 'Not Started'; }
                                        elseif($task->status == 2) { $taskStatusColor = 'bg-amber-50 text-amber-600'; $taskStatusLabel = 'In Progress'; }
                                        elseif($task->status == 3) { $taskStatusColor = 'bg-emerald-50 text-emerald-600'; $taskStatusLabel = 'Completed'; }
                                        elseif($task->status == 4) { $taskStatusColor = 'bg-indigo-50 text-indigo-600'; $taskStatusLabel = 'Testing'; }
                                        elseif($task->status == 5) { $taskStatusColor = 'bg-rose-50 text-rose-600'; $taskStatusLabel = 'Awaiting Feedback'; }
                                    @endphp
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ $taskStatusColor }}">
                                        {{ $taskStatusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('M d, Y') : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-slate-400 text-sm">No tasks found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Progress</h3>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-500 font-medium">Completion</span>
                    <span class="text-sm font-bold text-slate-900">{{ $project->progress }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all duration-1000 bg-blue-600" style="width: {{ $project->progress }}%"></div>
                </div>
                
                <div class="mt-8 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Start Date</span>
                        <span class="text-xs font-bold text-slate-800">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Deadline</span>
                        <span class="text-xs font-bold {{ $project->deadline && \Carbon\Carbon::parse($project->deadline)->isPast() ? 'text-red-600' : 'text-slate-800' }}">
                            {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M d, Y') : 'No deadline' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Billing Type</span>
                        <span class="text-xs font-bold text-slate-800">
                            @if($project->billing_type == 1) Fixed Rate
                            @elseif($project->billing_type == 2) Hourly Rate
                            @else Project Hours @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Project Team</h3>
                <div class="space-y-4">
                    @forelse($project->staff as $member)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs">
                            {{ substr($member->firstname, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">{{ $member->firstname }} {{ $member->lastname }}</p>
                            <p class="text-[10px] text-slate-400">Team Member</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400">No team members assigned</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
