@extends('layouts.admin')

@section('breadcrumb', 'Project Gantt Chart')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $project->name }} - Gantt Chart</h1>
            <p class="text-slate-500 text-sm mt-1">Visual timeline of project tasks and milestones</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.projects.show', $project) }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
            <button onclick="toggleFullGanttView()" id="fullGanttToggleBtn" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                <i class="bi bi-layout-split"></i> <span>Interactive View</span>
            </button>
            <button onclick="exportFullGanttPNG()" id="fullGanttExportBtn" class="hidden px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                <i class="bi bi-image"></i> Export PNG
            </button>
        </div>
    </div>

    @php
        $tasks = $project->tasks;
        $startDates = $tasks->filter(function($t) { return $t->startdate; })->pluck('startdate');
        $projectStart = $project->start_date ?? ($startDates->min() ?? now()->startOfWeek());
        $projectEnd = $project->deadline ?? now()->addWeeks(4);
        $totalDays = (int)$projectStart->diffInDays($projectEnd) + 1;
        $weeks = ceil($totalDays / 7);
        $criticalTaskIds = isset($criticalPath) && $criticalPath ? $criticalPath->pluck('id')->toArray() : [];
    @endphp

    @if($tasks->count() > 0)
    {{-- Zoom Controls --}}
    <div id="fullGanttZoomControls" class="hidden flex items-center gap-1 bg-slate-100 rounded-lg p-0.5 w-fit">
        <button onclick="setFullGanttView('Day')" class="px-3 py-1.5 text-sm rounded text-slate-600 hover:bg-white hover:shadow-sm full-gantt-zoom-btn" data-view="Day">Day</button>
        <button onclick="setFullGanttView('Week')" class="px-3 py-1.5 text-sm rounded bg-white shadow-sm text-slate-800 font-semibold full-gantt-zoom-btn" data-view="Week">Week</button>
        <button onclick="setFullGanttView('Month')" class="px-3 py-1.5 text-sm rounded text-slate-600 hover:bg-white hover:shadow-sm full-gantt-zoom-btn" data-view="Month">Month</button>
    </div>

    {{-- Blade Gantt Fallback --}}
    <div id="fullGanttBlade" class="bg-white rounded-xl border border-slate-200 p-6">
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
                    @if(count($criticalTaskIds) > 0)
                    <span class="flex items-center gap-1"><span class="w-3 h-2 bg-red-500 rounded"></span> Critical</span>
                    @endif
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
                                        $mDate = $milestone->due_date;
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
        $taskStart = $task->startdate ?? $projectStart;
        $taskEnd = $task->duedate ?? $taskStart->copy()->addDays(3);
        $startWeek = (int)floor(max(0, $projectStart->diffInDays($taskStart)) / 7);
        $endWeek = (int)floor(min($totalDays, $projectStart->diffInDays($taskEnd)) / 7);
        $isOverdue = $task->duedate?->isPast() && $task->status != 3;
                        $isCritical = in_array($task->id, $criticalTaskIds);
                        $barClass = $isCritical ? 'bg-red-500' : ($task->status == 3 ? 'bg-emerald-500' : ($isOverdue ? 'bg-amber-500' : 'bg-blue-500'));
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
                            <span class="truncate {{ $isCritical ? 'text-red-600 font-semibold' : '' }}" title="{{ $task->name }}">{{ $task->name }}</span>
                            @if($isCritical)<span class="text-[9px] text-red-500 font-bold ml-auto">CRITICAL</span>@endif
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
                            @if($isOverdue && !$isCritical)
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
                <span class="text-amber-600">{{ $tasks->filter(fn($t) => $t->duedate?->isPast() && $t->status != 3)->count() }} Overdue</span>
                @if(count($criticalTaskIds) > 0)
                <span class="text-red-600">{{ count($criticalTaskIds) }} Critical</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Interactive Gantt Container --}}
    <div id="fullGanttFrappe" class="hidden bg-white rounded-xl border border-slate-200 p-6">
        <div id="fullGanttChart" style="min-height: 450px;"></div>
    </div>

    {{-- Task Data --}}
    <script id="fullGanttData" type="application/json">
    {
        "tasks": [
            @foreach($tasks as $task)
            @php
                $taskStart = $task->startdate ?? $projectStart;
                $taskEnd = $task->duedate ?? $taskStart->copy()->addDays(3);
                $progress = $task->status == 3 ? 100 : ($task->status == 2 ? 50 : 0);
            @endphp
            {
                "id": "task-{{ $task->id }}",
                "name": {{ json_encode($task->name) }},
                "start": "{{ $taskStart->format('Y-m-d') }}",
                "end": "{{ $taskEnd->format('Y-m-d') }}",
                "progress": {{ $progress }},
                "dependencies": "{{ $task->dependencies ?? '' }}",
                "custom_class": "{{ in_array($task->id, $criticalTaskIds) ? 'critical-task' : '' }}"
            }@if(!$loop->last),@endif
            @endforeach
            @if($project->milestones->count() > 0),@endif
            @foreach($project->milestones as $milestone)
            @php $msDate = $milestone->due_date ? $milestone->due_date->format('Y-m-d') : $projectEnd->format('Y-m-d'); @endphp
            {
                "id": "milestone-{{ $milestone->id }}",
                "name": {{ json_encode('🔖 ' . $milestone->name) }},
                "start": "{{ $msDate }}",
                "end": "{{ $msDate }}",
                "progress": 100,
                "dependencies": "",
                "custom_class": "gantt-milestone"
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
    </script>
    @else
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="bi bi-diagram-3 text-slate-300 text-4xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2">No Tasks Yet</h3>
            <p class="text-slate-500 mb-6">Add tasks to your project to see the Gantt chart.</p>
            <a href="{{ route('admin.tasks.create') }}?rel_type=project&rel_id={{ $project->id }}" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all inline-flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Add Task
            </a>
        </div>
    </div>
    @endif

    @if($tasks->count() > 0)
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h4 class="text-sm font-semibold text-slate-800 mb-4">Project Data Summary</h4>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-slate-50 rounded-xl p-4">
                <div class="text-2xl font-bold text-slate-800">{{ $tasks->count() }}</div>
                <div class="text-sm text-slate-600">Total Tasks</div>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <div class="text-2xl font-bold text-slate-800">{{ $project->milestones->count() }}</div>
                <div class="text-sm text-slate-600">Milestones</div>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <div class="text-2xl font-bold text-slate-800">
                    @php
                        $completed = $tasks->where('status', 3)->count();
                        echo $tasks->count() > 0 ? round(($completed / $tasks->count()) * 100) : 0;
                    @endphp%
                </div>
                <div class="text-sm text-slate-600">Complete</div>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <div class="text-2xl font-bold text-slate-800">{{ count($criticalTaskIds) }}</div>
                <div class="text-sm text-slate-600">Critical Tasks</div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.projects.timesheets', $project) }}" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all inline-flex items-center gap-2">
            <i class="bi bi-clock-history"></i> View Detailed Timesheets
        </a>
    </div>
    @endif
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.css">
<style>
    .critical-task .bar { fill: #ef4444 !important; }
    .critical-task .bar-progress { fill: #dc2626 !important; }
    .critical-task .bar-label { fill: #fff !important; }
    .gantt-milestone .bar { fill: #8b5cf6 !important; }
    .gantt-milestone .bar-progress { fill: #7c3aed !important; }
    .gantt .grid-header { fill: #f8fafc !important; }
    .gantt .grid-row { fill: #fff; }
    .gantt .grid-row:nth-child(even) { fill: #f8fafc; }
    .gantt .row-line { stroke: #e2e8f0; }
    .gantt .tick { stroke: #e2e8f0; }
    .gantt .today-highlight { fill: #fef3c7; }
</style>

<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
var fullGanttInstance = null;
var fullCurrentView = 'Week';

function toggleFullGanttView() {
    var blade = document.getElementById('fullGanttBlade');
    var frappe = document.getElementById('fullGanttFrappe');
    var toggleBtn = document.getElementById('fullGanttToggleBtn');
    var zoomControls = document.getElementById('fullGanttZoomControls');
    var exportBtn = document.getElementById('fullGanttExportBtn');
    var isInteractive = frappe.classList.contains('hidden');

    if (isInteractive) {
        blade.classList.add('hidden');
        frappe.classList.remove('hidden');
        zoomControls.classList.remove('hidden');
        exportBtn.classList.remove('hidden');
        toggleBtn.querySelector('span').textContent = 'Classic View';
        toggleBtn.classList.remove('bg-indigo-600');
        toggleBtn.classList.add('bg-slate-600');
        initFullGantt();
    } else {
        blade.classList.remove('hidden');
        frappe.classList.add('hidden');
        zoomControls.classList.add('hidden');
        exportBtn.classList.add('hidden');
        toggleBtn.querySelector('span').textContent = 'Interactive View';
        toggleBtn.classList.remove('bg-slate-600');
        toggleBtn.classList.add('bg-indigo-600');
    }
}

function initFullGantt() {
    if (fullGanttInstance) return;
    var dataEl = document.getElementById('fullGanttData');
    if (!dataEl) return;
    var data = JSON.parse(dataEl.textContent);
    var tasks = data.tasks || [];

    if (tasks.length === 0) {
        document.getElementById('fullGanttChart').innerHTML = '<div class="text-center py-12 text-slate-500">No tasks to display</div>';
        return;
    }

    fullGanttInstance = new Gantt('#fullGanttChart', tasks, {
        view_mode: fullCurrentView,
        date_format: 'YYYY-MM-DD',
        bar_height: 30,
        bar_corner_radius: 3,
        arrow_curve: 5,
        padding: 18,
        language: 'en',
        on_click: function(task) {
            console.log('Clicked:', task.id);
        },
        on_date_change: function(task, start, end) {
            console.log('Date changed:', task.id, start, end);
        },
        on_progress_change: function(task, progress) {
            console.log('Progress:', task.id, progress);
        },
        on_view_change: function(mode) {
            fullCurrentView = mode;
            document.querySelectorAll('.full-gantt-zoom-btn').forEach(function(btn) {
                btn.classList.remove('bg-white', 'shadow-sm', 'font-semibold');
                if (btn.dataset.view === mode) {
                    btn.classList.add('bg-white', 'shadow-sm', 'font-semibold');
                }
            });
        }
    });

    setFullGanttView(fullCurrentView);
}

function setFullGanttView(view) {
    fullCurrentView = view;
    if (fullGanttInstance) {
        fullGanttInstance.change_view_mode(view);
    }
    document.querySelectorAll('.full-gantt-zoom-btn').forEach(function(btn) {
        btn.classList.remove('bg-white', 'shadow-sm', 'font-semibold');
        if (btn.dataset.view === view) {
            btn.classList.add('bg-white', 'shadow-sm', 'font-semibold');
        }
    });
}

function exportFullGanttPNG() {
    var chart = document.getElementById('fullGanttChart');
    if (!chart) return;
    html2canvas(chart, {
        scale: 2,
        backgroundColor: '#ffffff',
        useCORS: true
    }).then(function(canvas) {
        var link = document.createElement('a');
        link.download = 'gantt-chart-{{ $project->id }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }).catch(function(err) {
        console.error('Export failed:', err);
        alert('Failed to export Gantt chart as PNG');
    });
}
</script>
@endsection
