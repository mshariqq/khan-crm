@extends('layouts.admin')

@section('breadcrumb', 'Project Timesheets')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $project->name }} - Timesheets</h1>
            <p class="text-slate-500 text-sm mt-1">Time tracking summary for all project tasks</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.projects.show', $project) }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
            <button class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-download"></i> Export
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Hours</p>
                    <p class="text-2xl font-bold text-slate-800">
                        @php
                            $totalMinutes = $project->tasks->flatMap(function($task) {
                                return $task->timers->where('end_time', '!=', null);
                            })->sum(function($timer) {
                                return \Carbon\Carbon::parse($timer->start_time)->diffInMinutes(\Carbon\Carbon::parse($timer->end_time));
                            });
                            echo number_format($totalMinutes / 60, 1);
                        @endphp
                    </p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-clock-history text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Billable Hours</p>
                    <p class="text-2xl font-bold text-emerald-600">
                        @php
                            $billableMinutes = $project->tasks->flatMap(function($task) {
                                return $task->timers->where('end_time', '!=', null)->where('is_billable', 1);
                            })->sum(function($timer) {
                                return \Carbon\Carbon::parse($timer->start_time)->diffInMinutes(\Carbon\Carbon::parse($timer->end_time));
                            });
                            echo number_format($billableMinutes / 60, 1);
                        @endphp
                    </p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-currency-dollar text-emerald-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Staff Members</p>
                    <p class="text-2xl font-bold text-slate-800">
                        @php
                            $staffCount = $project->tasks->flatMap(function($task) {
                                return $task->timers;
                            })->pluck('staff_id')->unique()->count();
                            echo $staffCount;
                        @endphp
                    </p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-people text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Tasks Tracked</p>
                    <p class="text-2xl font-bold text-slate-800">
                        @php
                            $trackedTasks = $project->tasks->filter(function($task) {
                                return $task->timers->count() > 0;
                            })->count();
                            echo $trackedTasks;
                        @endphp
                    </p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-list-ul text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Timesheet Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Staff Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Start Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">End Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Billable</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($project->tasks->flatMap(function($task) { return $task->timers; })->sortByDesc('start_time') as $timer)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($timer->staff->full_name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-900">{{ $timer->staff->full_name ?? 'Unknown' }}</div>
                                        <div class="text-sm text-slate-500">{{ $timer->staff->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900">{{ $timer->task->name ?? 'Unknown Task' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ \Carbon\Carbon::parse($timer->start_time)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ \Carbon\Carbon::parse($timer->start_time)->format('h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($timer->end_time)
                                    <span class="text-sm text-slate-600">{{ \Carbon\Carbon::parse($timer->end_time)->format('h:i A') }}</span>
                                @else
                                    <span class="text-sm text-amber-600 font-medium"><i class="bi bi-play-circle"></i> Running</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $duration = $timer->end_time ? 
                                        \Carbon\Carbon::parse($timer->start_time)->diffInMinutes(\Carbon\Carbon::parse($timer->end_time)) :
                                        now()->diffInMinutes(\Carbon\Carbon::parse($timer->start_time));
                                    $hours = floor($duration / 60);
                                    $minutes = $duration % 60;
                                @endphp
                                <span class="text-sm font-semibold text-slate-800">
                                    {{ sprintf('%02d:%02d', $hours, $minutes) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($timer->is_billable)
                                    <span class="text-sm font-semibold text-emerald-700">${{ $timer->hourly_rate ?? 0 }}/h</span>
                                @else
                                    <span class="text-sm text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($timer->is_billable)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">Billable</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-800">Non-billable</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <i class="bi bi-clock-history text-3xl opacity-50 mb-4"></i>
                                <p class="text-lg">No time logged yet</p>
                                <p class="text-sm mt-2">Time tracked on project tasks will appear here</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection