@if($tasks->count() > 0)
@php
$startDates = $tasks->filter(function($t) { return $t->startdate; })->pluck('startdate');
$projectStart = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : ($startDates->min() ? \Carbon\Carbon::parse($startDates->min()) : now()->startOfWeek());
$projectEnd = $project->deadline ? \Carbon\Carbon::parse($project->deadline) : now()->addWeeks(4);
$totalDays = (int)$projectStart->diffInDays($projectEnd) + 1;
$weeks = ceil($totalDays / 7);
@endphp

<div class="mb-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Gantt Chart</h3>
            <p class="text-xs text-slate-400">{{ $projectStart->format('M d, Y') }} - {{ $projectEnd->format('M d, Y') }} ({{ $totalDays }} days)</p>
        </div>
        <div class="flex items-center gap-3 text-xs">
            <span class="flex items-center gap-1"><span class="w-3 h-2 bg-blue-500 rounded"></span> In Progress</span>
            <span class="flex items-center gap-1"><span class="w-3 h-2 bg-emerald-500 rounded"></span> Done</span>
            <span class="flex items-center gap-1"><span class="w-3 h-2 bg-amber-500 rounded"></span> Overdue</span>
        </div>
    </div>
</div>

<div class="border border-slate-200 rounded-lg overflow-hidden bg-white">
    {{-- Timeline Header --}}
    <div class="flex bg-slate-50 border-b border-slate-200 text-xs">
        <div class="w-56 p-3 font-semibold text-slate-600 border-r border-slate-200 bg-slate-100">Task</div>
        <div class="flex-1 flex">
            @for($w = 0; $w < $weeks; $w++)
                @php $weekStart = $projectStart->copy()->addWeeks($w); @endphp
                <div class="flex-1 p-2 text-center border-r border-slate-200 bg-slate-50">
                    <div class="text-[10px] font-medium text-slate-500">Week {{ $w + 1 }}</div>
                    <div class="text-[9px] text-slate-400">{{ $weekStart->format('M d') }}</div>
                </div>
            @endfor
        </div>
    </div>

    {{-- Task Rows --}}
    <div class="max-h-[500px] overflow-y-auto">
        @php $currentMilestone = null; $rowIndex = 0; @endphp
        @foreach($tasks as $task)
            @if($task->milestone && $task->milestone != $currentMilestone)
                @php $currentMilestone = $task->milestone; @endphp
                @if($milestone = $project->milestones->find($currentMilestone))
                <div class="flex bg-purple-50 border-b border-purple-100">
                    <div class="w-56 p-2 text-xs font-semibold text-purple-700 border-r border-purple-200 flex items-center gap-1 sticky left-0 bg-purple-50">
                        <i class="bi bi-flag-fill text-[10px]"></i> {{ $milestone->name }}
                    </div>
                    <div class="flex-1 flex">
                        @for($w = 0; $w < $weeks; $w++)
                            @php
                                $weekStart = $projectStart->copy()->addWeeks($w);
                                $weekEnd = $weekStart->copy()->addDays(6);
                                $mDate = $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date) : null;
                                $inWeek = $mDate && $mDate->between($weekStart, $weekEnd);
                            @endphp
                            <div class="flex-1 h-8 border-r border-slate-100 relative {{ $inWeek ? 'bg-purple-200' : '' }}">
                                @if($inWeek)
                                <div class="absolute top-1/2 -translate-y-1/2 left-1/2 -translate-x-1/2 bg-purple-600 text-white text-[9px] px-2 py-0.5 rounded">
                                    Due {{ $mDate->format('M d') }}
                                </div>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
                @endif
            @endif

            @php
                $taskStart = $task->startdate ? \Carbon\Carbon::parse($task->startdate) : $projectStart;
                $taskEnd = $task->duedate ? \Carbon\Carbon::parse($task->duedate) : ($taskStart->copy()->addDays(3));
                $startWeek = (int)floor(max(0, $projectStart->diffInDays($taskStart)) / 7);
                $endWeek = (int)floor(min($totalDays, $projectStart->diffInDays($taskEnd)) / 7);
                $isOverdue = $task->duedate && \Carbon\Carbon::parse($task->duedate)->isPast() && $task->status != 3;
                $barClass = $task->status == 3 ? 'bg-emerald-500' : ($isOverdue ? 'bg-amber-500' : 'bg-blue-500');
                $rowIndex++;
            @endphp
            <div class="flex hover:bg-slate-50 border-b border-slate-100">
                <div class="w-56 p-2 text-xs text-slate-700 border-r border-slate-200 flex items-center gap-2 sticky left-0 bg-white">
                    @if($task->status == 3)
                    <span class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center flex-shrink-0"><i class="bi bi-check text-white text-[8px]"></i></span>
                    @elseif($task->status == 2)
                    <span class="w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0"><i class="bi bi-arrow-right text-white text-[8px]"></i></span>
                    @else
                    <span class="w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0"></span>
                    @endif
                    <span class="truncate" title="{{ $task->name }}">{{ $task->name }}</span>
                </div>
                <div class="flex-1 flex relative">
                    @for($w = 0; $w < $weeks; $w++)
                        <div class="flex-1 h-10 border-r border-slate-100 relative">
                            @if($w >= $startWeek && $w <= $endWeek)
                            <div class="absolute top-1/2 -translate-y-1/2 h-5 rounded-sm {{ $barClass }} flex items-center px-1"
                                 style="width: {{ $w == $startWeek && $w == $endWeek ? '80%' : '100%' }}{{ $w == $startWeek ? ';left:0' : '' }}{{ $w == $endWeek ? ';right:0' : '' }}">
                                @if($w == $endWeek)
                                <span class="text-[9px] text-white truncate">{{ $taskEnd->format('M d') }}</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    @endfor
                    @if($isOverdue)
                    <div class="absolute right-2 top-1/2 -translate-y-1/2">
                        <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Overdue</span>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Legend --}}
<div class="mt-4 flex items-center justify-between text-xs text-slate-500 bg-slate-50 p-3 rounded-lg">
    <div class="flex items-center gap-4">
        <span class="font-medium text-slate-600">{{ $tasks->count() }} Tasks</span>
        <span>{{ $project->milestones->count() }} Milestones</span>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-emerald-600">{{ $tasks->where('status', 3)->count() }} Done</span>
        <span class="text-blue-600">{{ $tasks->where('status', 2)->count() }} In Progress</span>
        <span class="text-amber-600">{{ $tasks->filter(function($t) { return $t->duedate && \Carbon\Carbon::parse($t->duedate)->isPast() && $t->status != 3; })->count() }} Overdue</span>
    </div>
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-bar-chart-steps text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No tasks to display</p>
    <a href="{{ route('admin.tasks.create') }}?rel_type=project&rel_id={{ $project->id }}" data-pane="create" data-pane-type="task" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Task</a>
</div>
@endif