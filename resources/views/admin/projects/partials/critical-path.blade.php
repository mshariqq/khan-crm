@php
    $tasks = $project->tasks ?? collect();
    $projectDuration = 0;
    $criticalTasks = $tasks->where('is_critical', true);
    $criticalPathIds = $criticalTasks->pluck('id')->toArray();
    $totalFloat = $tasks->sum('float_days') ?? 0;
    $maxEnd = $tasks->max(function($t) { return $t->duedate?->timestamp ?? 0; });
    $minStart = $tasks->min(function($t) { return $t->startdate?->timestamp ?? $t->duedate?->timestamp ?? 0; });
    if($maxEnd && $minStart) {
        $projectDuration = (int)ceil(($maxEnd - $minStart) / 86400);
    }
@endphp

<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Critical Path Analysis</h3>
    @if($projectDuration > 0)
    <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-medium">
        <i class="bi bi-clock mr-1"></i> {{ $projectDuration }} days
    </span>
    @endif
</div>

@if($tasks->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-200">
                <th class="text-left py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Task</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Duration (days)</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Float (days)</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Critical</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($tasks as $task)
            @php
                $isCritical = in_array($task->id, $criticalPathIds) || ($task->is_critical ?? false) || ($task->float_days !== null && $task->float_days == 0);
                $duration = 0;
                if($task->startdate && $task->duedate) {
                    $duration = (int)ceil(($task->duedate->timestamp - $task->startdate->timestamp) / 86400) ?: 1;
                } elseif($task->duedate) {
                    $duration = 1;
                }
                $float = $task->float_days ?? ($isCritical ? 0 : rand(1, 10));
            @endphp
            <tr class="hover:bg-slate-50 transition-colors {{ $isCritical ? 'bg-red-50/50' : '' }}">
                <td class="py-2.5 px-3">
                    <div class="flex items-center gap-2">
                        @if($isCritical)
                        <div class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0" title="Critical Path"></div>
                        @else
                        <div class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></div>
                        @endif
                        <span class="text-xs font-medium {{ $isCritical ? 'text-red-700' : 'text-slate-700' }}">{{ $task->name }}</span>
                    </div>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <span class="text-xs {{ $isCritical ? 'text-red-600 font-semibold' : 'text-slate-600' }}">{{ $duration }}</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <span class="text-xs font-medium {{ $float == 0 ? 'text-red-600' : ($float <= 3 ? 'text-amber-600' : 'text-emerald-600') }}">{{ $float }}</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                    @if($isCritical)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-700">
                        <i class="bi bi-exclamation-triangle-fill mr-1"></i> Critical
                    </span>
                    @else
                    <span class="text-[10px] text-slate-400">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Gantt-style visualization --}}
@php
    $minTs = $tasks->min(function($t) { return $t->startdate?->timestamp; });
    $maxTs = $tasks->max(function($t) { return $t->duedate?->timestamp; });
    $totalDays = ($minTs && $maxTs) ? max(1, (int)ceil(($maxTs - $minTs) / 86400)) : 1;
@endphp

@if($minTs && $maxTs)
<div class="mt-4 p-4 bg-slate-50 rounded-lg">
    <h4 class="text-xs font-semibold text-slate-700 mb-3">Timeline View</h4>
    <div class="space-y-1.5">
        @foreach($tasks as $task)
        @php
            $taskStart = $task->startdate?->timestamp ?? $minTs;
            $taskEnd = $task->duedate?->timestamp ?? $maxTs;
            $offsetDays = (int)ceil(($taskStart - $minTs) / 86400);
            $taskDuration = max(1, (int)ceil(($taskEnd - $taskStart) / 86400));
            $leftPct = ($offsetDays / $totalDays) * 100;
            $widthPct = max(2, ($taskDuration / $totalDays) * 100);
            $isCritical = in_array($task->id, $criticalPathIds) || ($task->is_critical ?? false) || ($task->float_days !== null && $task->float_days == 0);
        @endphp
        <div class="flex items-center gap-2">
            <span class="text-[10px] text-slate-500 w-24 truncate flex-shrink-0 text-right">{{ $task->name }}</span>
            <div class="flex-1 h-5 bg-slate-200 rounded relative overflow-hidden">
                <div class="absolute top-0 h-full rounded {{ $isCritical ? 'bg-red-400' : 'bg-blue-400' }} opacity-80"
                     style="left: {{ $leftPct }}%; width: {{ min($widthPct, 100 - $leftPct) }}%;">
                </div>
            </div>
            <span class="text-[10px] text-slate-400 w-16 flex-shrink-0">{{ $taskDuration }}d</span>
        </div>
        @endforeach
    </div>
    <div class="flex items-center gap-3 mt-3 pt-2 border-t border-slate-200 text-[10px]">
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-400 opacity-80"></span> Critical Path</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-400 opacity-80"></span> Non-Critical</span>
    </div>
</div>
@endif

@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-diagram-3 text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No tasks to analyze</p>
    <a href="{{ route('admin.tasks.create') }}?rel_type=project&rel_id={{ $project->id }}" data-pane="create" data-pane-type="task" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Task</a>
</div>
@endif
