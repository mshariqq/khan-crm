@php
$taskStatusLabels = ['Backlog', 'To Do', 'In Progress', 'Done'];
$taskStatusColors = ['bg-slate-100 text-slate-700', 'bg-amber-100 text-amber-700', 'bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700'];
$totalTasks = $tasks->count();
$doneTasks = $tasks->where('status', 3)->count();
$progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
$isOverdue = $milestone->due_date && \Carbon\Carbon::parse($milestone->due_date)->isPast();
@endphp

<div class="flex items-start justify-between mb-5">
    <div class="flex items-start gap-3 min-w-0 flex-1">
        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-lg flex-shrink-0" style="background: {{ $milestone->color ?: 'linear-gradient(135deg, #6366f1, #4f46e5)' }}">
            <i class="bi bi-flag-fill"></i>
        </div>
        <div class="min-w-0">
            <div class="flex items-center gap-2.5 mb-0.5 flex-wrap">
                <h1 class="text-xl font-bold text-slate-800 truncate">{{ $milestone->name }}</h1>
                @if($isOverdue)
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-red-100 text-red-700 border-red-200">Overdue</span>
                @endif
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-400 flex-wrap">
                <span>Milestone</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>Due {{ $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('M j, Y') : 'No date' }}</span>
                @if($milestone->project)
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <a href="{{ route('admin.projects.show', $milestone->project->id) }}" class="text-blue-600 hover:underline">{{ $milestone->project->name }}</a>
                @endif
            </div>
        </div>
    </div>
    <button onclick="parent.openMilestonePane('{{ route('admin.milestones.pane.edit', $milestone->id) }}')" class="px-3 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-1.5 flex-shrink-0">
        <i class="bi bi-pencil"></i> Edit
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
    {{-- Main Content --}}
    <div class="lg:col-span-3 space-y-5">

        {{-- Description --}}
        @if($milestone->description)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-card-text text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Description</h3>
            </div>
            <div class="p-5 text-sm text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $milestone->description }}</div>
        </div>
        @endif

        {{-- Tasks --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-list-task text-blue-600"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Tasks</h3>
                </div>
                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-[10px] font-bold">{{ $totalTasks }}</span>
            </div>
            <div class="p-5">
                @if($totalTasks > 0)
                <div class="w-full bg-slate-100 rounded-full h-2 mb-4">
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                </div>
                <div class="space-y-2">
                    @foreach($tasks as $task)
                    @php
                        $tcomp = $task->checklistItems->where('finished', 1)->count();
                        $ttot = $task->checklistItems->count();
                        $tprog = $ttot > 0 ? round(($tcomp / $ttot) * 100) : 0;
                    @endphp
                    <div class="flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0 {{ $taskStatusColors[$task->status] ?? 'bg-slate-100' }}">
                                <i class="bi {{ $task->status == 3 ? 'bi-check-lg' : ($task->status == 2 ? 'bi-arrow-right' : 'bi-circle') }}"></i>
                            </span>
                            <div class="min-w-0">
                                <span class="text-sm font-medium text-slate-700 truncate block">{{ $task->name }}</span>
                                <span class="text-[10px] text-slate-400 flex items-center gap-2">
                                    <span class="{{ $taskStatusColors[$task->status] ?? '' }} px-1.5 py-0.5 rounded text-[9px] font-medium">{{ $taskStatusLabels[$task->status] ?? 'Unknown' }}</span>
                                    @if($ttot > 0)
                                    <span>· {{ $tcomp }}/{{ $ttot }} checklist</span>
                                    @endif
                                    @if($task->assignedStaff->count() > 0)
                                    <span>·</span>
                                    <span class="flex items-center gap-1">
                                        @foreach($task->assignedStaff->take(2) as $s)
                                        <span class="w-4 h-4 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-600 text-[6px] font-bold">{{ strtoupper(substr($s->firstname, 0, 1)) }}</span>
                                        @endforeach
                                        @if($task->assignedStaff->count() > 2)
                                        <span class="text-[9px] text-slate-400">+{{ $task->assignedStaff->count() - 2 }}</span>
                                        @endif
                                    </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <button onclick="parent.openTaskPane('{{ route('admin.tasks.pane.show', $task->id) }}', 'Task')" class="px-2.5 py-1 text-[10px] font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-all flex-shrink-0">
                            View
                        </button>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-list-task text-slate-300 text-xl"></i>
                    </div>
                    <p class="text-sm text-slate-500 mb-1">No tasks in this milestone</p>
                    <p class="text-xs text-slate-400">Tasks can be assigned to this milestone during creation</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Milestone Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-info-circle text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Milestone Info</h3>
            </div>
            <div class="p-5 space-y-4">
                @if($milestone->project)
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Project</span>
                    <a href="{{ route('admin.projects.show', $milestone->project->id) }}" class="text-sm font-medium text-blue-600 hover:underline flex items-center gap-2">
                        <i class="bi bi-folder"></i>
                        {{ $milestone->project->name }}
                    </a>
                </div>
                @endif
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Due Date</span>
                        <p class="text-sm font-medium {{ $isOverdue ? 'text-red-600' : 'text-slate-700' }}">
                            {{ $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('M j, Y') : '-' }}
                            @if($isOverdue)
                            <span class="text-[10px] text-red-500 ml-1">(Overdue)</span>
                            @elseif($milestone->due_date)
                            <span class="text-[10px] text-slate-400 ml-1">({{ \Carbon\Carbon::parse($milestone->due_date)->diffInDays(now()) }}d left)</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Color</span>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="w-5 h-5 rounded-full border border-slate-200" style="background: {{ $milestone->color ?: '#6366f1' }}"></span>
                            <span class="text-sm text-slate-600">{{ $milestone->color ?: 'Default (Indigo)' }}</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Total Tasks</span>
                        <p class="text-lg font-bold text-slate-800">{{ $totalTasks }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Completed</span>
                        <p class="text-lg font-bold text-emerald-600">{{ $doneTasks }} <span class="text-xs text-slate-400 font-medium">/ {{ $totalTasks }}</span></p>
                    </div>
                </div>
                @if($totalTasks > 0)
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Progress</span>
                    <div class="w-full bg-slate-100 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2.5 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1.5 text-right">{{ $progress }}% complete</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Task Status Breakdown --}}
        @if($totalTasks > 0)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-pie-chart text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Status Breakdown</h3>
            </div>
            <div class="p-5 space-y-3">
                @foreach([0 => 'Backlog', 1 => 'To Do', 2 => 'In Progress', 3 => 'Done'] as $k => $label)
                @php $count = $tasks->where('status', $k)->count(); @endphp
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-slate-600">{{ $label }}</span>
                        <span class="font-semibold {{ $k == 3 ? 'text-emerald-600' : 'text-slate-700' }}">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $k == 3 ? 'bg-emerald-500' : ($k == 2 ? 'bg-blue-500' : ($k == 1 ? 'bg-amber-500' : 'bg-slate-400')) }}" style="width: {{ $totalTasks > 0 ? ($count / $totalTasks) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Dates --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-calendar3 text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Dates</h3>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Created</span>
                    <span class="text-xs font-medium text-slate-700">{{ $milestone->datecreated ? \Carbon\Carbon::parse($milestone->datecreated)->format('M j, Y') : '-' }}</span>
                </div>
                <div class="border-t border-slate-100"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Start Date</span>
                    <span class="text-xs font-medium text-slate-700">{{ $milestone->start_date ? \Carbon\Carbon::parse($milestone->start_date)->format('M j, Y') : '-' }}</span>
                </div>
                <div class="border-t border-slate-100"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Due Date</span>
                    <span class="text-xs font-medium {{ $isOverdue ? 'text-red-600' : 'text-slate-700' }}">{{ $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('M j, Y') : '-' }}</span>
                </div>
                @if($milestone->hide_from_customer !== null)
                <div class="border-t border-slate-100"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Hidden from Customer</span>
                    <span class="text-xs font-medium text-slate-700">{{ $milestone->hide_from_customer ? 'Yes' : 'No' }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
