@props(['task', 'showProject' => false, 'showAssignee' => true])

@php
    $priorityColors = [
        3 => ['indicator' => 'bg-red-500', 'badge' => 'bg-red-50 text-red-700'], 
        2 => ['indicator' => 'bg-amber-500', 'badge' => 'bg-amber-50 text-amber-700'], 
        1 => ['indicator' => 'bg-emerald-500', 'badge' => 'bg-emerald-50 text-emerald-700']
    ];
    
    $statusLabels = [
        0 => 'Backlog',
        1 => 'To Do', 
        2 => 'In Progress',
        3 => 'Done'
    ];
    
    $progressPercent = $task->status == 3 ? 100 : ($task->status == 2 ? 50 : 0);
@endphp

<div class="bg-white rounded-xl border border-slate-200 hover:border-slate-300 hover:shadow-lg transition-all shadow-sm overflow-hidden task-card-{{ $task->id }}"
     data-pane="show" data-pane-id="{{ $task->id }}"
     style="cursor: pointer;">
    
    {{-- Priority Indicator Bar --}}
    <div class="h-1 bg-gradient-to-r {{ $priorityColors[$task->priority]['indicator'] ?? 'bg-slate-400' }}"></div>
    
    <div class="p-4">
        {{-- Task Header --}}
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-slate-800 leading-tight mb-1 hover:text-blue-600 transition-colors">
                    {{ $task->name }}
                </h3>
                @if($showProject && $task->project)
                    <a href="{{ route('admin.projects.show', $task->project->id) }}" class="text-xs text-slate-600 font-medium hover:text-blue-600">
                        <i class="bi bi-folder2 text-slate-400 mr-1"></i> {{ $task->project->name }}
                    </a>
                @endif
            </div>
            <div class="flex items-center gap-1 ml-2">
                <span class="w-2 h-2 rounded-full status-dot {{ $priorityColors[$task->priority]['indicator'] ?? 'bg-slate-400' }}"></span>
            </div>
        </div>

        {{-- Task Meta --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                @if($task->priority)
                    <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $priorityColors[$task->priority]['badge'] }}">
                        P{{ $task->priority }}
                    </span>
                @endif
                @if($task->status !== null)
                    <span class="text-xs text-slate-500 font-medium">{{ $statusLabels[$task->status] ?? 'Unknown' }}</span>
                @endif
            </div>
            @if($showAssignee && $task->assignedStaff->isNotEmpty())
                <div class="flex items-center gap-1">
                    @foreach($task->assignedStaff->take(2) as $assignee)
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0" 
                             title="{{ $assignee->full_name }} ({{ $assignee->email }})">
                            {{ substr($assignee->full_name, 0, 1) }}
                        </div>
                    @endforeach
                    @if($task->assignedStaff->count() > 2)
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-center">
                            +{{ $task->assignedStaff->count() - 2 }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Progress Bar --}}
        @if($progressPercent > 0)
            <div class="mb-3">
                <div class="w-full bg-slate-200 rounded-full h-1">
                    <div class="bg-blue-600 h-1 rounded-full transition-all duration-300" 
                         style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        @endif

        {{-- Task Details --}}
        <div class="grid grid-cols-2 gap-3 text-xs text-slate-600 mb-3">
            @if($task->duedate)
                <div class="flex items-center gap-1">
                    <i class="bi bi-calendar3 text-slate-400"></i>
                    <span class="{{ \Carbon\Carbon::parse($task->duedate)->isPast() ? 'text-red-600 font-semibold' : '' }}">
                        {{ \Carbon\Carbon::parse($task->duedate)->format('M d') }}
                    </span>
                </div>
            @endif
            
            @if($task->hourly_rate && $task->billable)
                <div class="flex items-center gap-1 justify-end">
                    <i class="bi bi-currency-dollar text-emerald-600"></i>
                    <span class="text-emerald-700 font-semibold">{{ $task->hourly_rate }}/h</span>
                </div>
            @endif
        </div>

        {{-- Task Stats --}}
        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
            <div class="flex items-center gap-3 text-xs text-slate-500">
                @if($task->comments_count ?? $task->comments->count())
                    <span class="flex items-center gap-1">
                        <i class="bi bi-chat-dots"></i> {{ $task->comments_count ?? $task->comments->count() }}
                    </span>
                @endif
                
                @if($task->files_count ?? $task->files->count())
                    <span class="flex items-center gap-1">
                        <i class="bi bi-paperclip"></i> {{ $task->files_count ?? $task->files->count() }}
                    </span>
                @endif
                
                @if($task->checklistItems->where('finished', 1)->count() > 0)
                    <span class="text-emerald-600">
                        {{ $task->checklistItems->where('finished', 1)->count() }}/{{ $task->checklistItems->count() }}
                    </span>
                @endif
            </div>
            
            <div class="text-xs text-slate-500">
                {{ ($task->created_at ?? $task->dateadded)->diffForHumans() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .task-card-{{ $task->id }}:hover {
        transform: translateY(-2px);
    }
    .task-card-{{ $task->id }} {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush