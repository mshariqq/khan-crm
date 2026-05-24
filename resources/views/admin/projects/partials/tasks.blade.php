<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Tasks ({{ $taskCount }})</h3>
    <a href="{{ route('admin.tasks.create') }}?rel_type=project&rel_id={{ $project->id }}" data-pane="create" data-pane-type="task" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Task
    </a>
</div>
@if($taskCount > 0)
<div class="space-y-3">
    @foreach($groupedTasks as $milestoneId => $tasks)
        @if($milestoneId !== 'no_milestone' && $milestone = $project->milestones->find($milestoneId))
        <div class="flex items-center gap-2 px-3 py-2 bg-purple-50 rounded-lg border border-purple-100">
            <div class="w-5 h-5 rounded bg-purple-200 flex items-center justify-center">
                <i class="bi bi-flag-fill text-purple-600 text-[10px]"></i>
            </div>
            <span class="text-xs font-semibold text-purple-700">{{ $milestone->name }}</span>
            <span class="text-[10px] text-purple-400 ml-auto">{{ $tasks->count() }} tasks</span>
        </div>
        @foreach($tasks as $task)
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group ml-4 border-l-2 border-purple-200">
            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 {{ $task->status == 3 ? 'bg-emerald-500 border-emerald-500' : ($task->status == 2 ? 'bg-blue-500 border-blue-500' : 'border-slate-300') }}">
                @if($task->status == 3)<i class="bi bi-check text-white text-[8px]"></i>@elseif($task->status == 2)<i class="bi bi-arrow-right text-white text-[8px]"></i>@endif
            </div>
            <a href="{{ route('admin.tasks.show', $task->id) }}" data-pane="show" data-pane-type="task" data-pane-id="{{ $task->id }}" class="text-sm {{ $task->status == 3 ? 'text-slate-400 line-through' : 'text-slate-700 hover:text-blue-600' }} flex-1 font-medium flex items-center gap-2">
                <span class="text-[10px] px-1.5 py-0.5 rounded font-medium
                    @if($task->status == 3) bg-emerald-100 text-emerald-700
                    @elseif($task->status == 2) bg-blue-100 text-blue-700
                    @elseif($task->status == 1) bg-amber-100 text-amber-700
                    @else bg-slate-100 text-slate-600 @endif">
                    @if($task->status == 3) Done
                    @elseif($task->status == 2) In Progress
                    @elseif($task->status == 1) To Do
                    @else Backlog @endif
                </span>
                {{ $task->name }}
            </a>
            <span class="text-[10px] px-2 py-1 rounded-full font-medium
                @if($task->priority == 3) bg-red-100 text-red-700
                @elseif($task->priority == 2) bg-amber-100 text-amber-700
                @else bg-emerald-100 text-emerald-700 @endif">
                {{ $task->priority == 3 ? 'High' : ($task->priority == 2 ? 'Medium' : 'Low') }}
            </span>
            @if($task->assignedStaff->count() > 0)
            <div class="flex -space-x-1">
                @foreach($task->assignedStaff->take(2) as $staff)
                <div class="w-5 h-5 rounded-full bg-slate-400 border border-white flex items-center justify-center text-[8px] font-semibold text-white" title="{{ $staff->firstname }} {{ $staff->lastname }}">
                    {{ substr($staff->firstname, 0, 1) }}
                </div>
                @endforeach
            </div>
            @endif
            <span class="text-[10px] text-slate-400">{{ $task->duedate?->format('M j') }}</span>
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('admin.tasks.edit', $task->id) }}" data-pane="edit" data-pane-type="task" data-pane-id="{{ $task->id }}" class="p-1 text-slate-400 hover:text-blue-600" title="Edit">
                    <i class="bi bi-pencil text-xs"></i>
                </a>
                <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete task?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1 text-slate-400 hover:text-red-600" title="Delete">
                        <i class="bi bi-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
        @else
        @foreach($tasks as $task)
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group">
            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 {{ $task->status == 3 ? 'bg-emerald-500 border-emerald-500' : ($task->status == 2 ? 'bg-blue-500 border-blue-500' : 'border-slate-300') }}">
                @if($task->status == 3)<i class="bi bi-check text-white text-[8px]"></i>@elseif($task->status == 2)<i class="bi bi-arrow-right text-white text-[8px]"></i>@endif
            </div>
            <a href="{{ route('admin.tasks.show', $task->id) }}" data-pane="show" data-pane-type="task" data-pane-id="{{ $task->id }}" class="text-sm {{ $task->status == 3 ? 'text-slate-400 line-through' : 'text-slate-700 hover:text-blue-600' }} flex-1 font-medium flex items-center gap-2">
                <span class="text-[10px] px-1.5 py-0.5 rounded font-medium
                    @if($task->status == 3) bg-emerald-100 text-emerald-700
                    @elseif($task->status == 2) bg-blue-100 text-blue-700
                    @elseif($task->status == 1) bg-amber-100 text-amber-700
                    @else bg-slate-100 text-slate-600 @endif">
                    @if($task->status == 3) Done
                    @elseif($task->status == 2) In Progress
                    @elseif($task->status == 1) To Do
                    @else Backlog @endif
                </span>
                {{ $task->name }}
            </a>
            <span class="text-[10px] px-2 py-1 rounded-full font-medium
                @if($task->priority == 3) bg-red-100 text-red-700
                @elseif($task->priority == 2) bg-amber-100 text-amber-700
                @else bg-emerald-100 text-emerald-700 @endif">
                {{ $task->priority == 3 ? 'High' : ($task->priority == 2 ? 'Medium' : 'Low') }}
            </span>
            @if($task->assignedStaff->count() > 0)
            <div class="flex -space-x-1">
                @foreach($task->assignedStaff->take(2) as $staff)
                <div class="w-5 h-5 rounded-full bg-slate-400 border border-white flex items-center justify-center text-[8px] font-semibold text-white" title="{{ $staff->firstname }} {{ $staff->lastname }}">
                    {{ substr($staff->firstname, 0, 1) }}
                </div>
                @endforeach
            </div>
            @endif
            <span class="text-[10px] text-slate-400">{{ $task->duedate?->format('M j') }}</span>
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('admin.tasks.edit', $task->id) }}" data-pane="edit" data-pane-type="task" data-pane-id="{{ $task->id }}" class="p-1 text-slate-400 hover:text-blue-600" title="Edit">
                    <i class="bi bi-pencil text-xs"></i>
                </a>
                <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete task?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1 text-slate-400 hover:text-red-600" title="Delete">
                        <i class="bi bi-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
        @endif
    @endforeach
</div>
        </div>
        @endforeach
        @endif
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-list-check text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No tasks yet</p>
    <a href="{{ route('admin.tasks.create') }}?rel_type=project&rel_id={{ $project->id }}" data-pane="create" data-pane-type="task" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Task</a>
</div>
@endif