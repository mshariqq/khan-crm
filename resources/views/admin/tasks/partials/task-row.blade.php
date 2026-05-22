<div class="task-row flex items-center gap-4 p-4 hover:bg-slate-50 transition-colors {{ $isNested ? 'pl-12 border-l-2 border-slate-100' : 'border-b border-slate-100' }}" onclick="window.location='{{ route('admin.tasks.show', $task->id) }}'" style="cursor: pointer;">
    {{-- Priority Indicator --}}
    <div class="priority-indicator self-stretch rounded-full w-1 flex-shrink-0 {{ $task->priority == 'high' || $task->priority == 3 ? 'bg-red-500' : ($task->priority == 'medium' || $task->priority == 2 ? 'bg-amber-500' : 'bg-emerald-500') }}"></div>

    {{-- Status --}}
    <div class="flex-shrink-0">
        <span class="status-dot inline-block w-2 h-2 rounded-full {{ $task->status == 3 ? 'bg-emerald-500' : ($task->status == 2 ? 'bg-blue-500' : 'bg-slate-300') }}"></span>
    </div>

    {{-- Task Info --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
            <h3 class="text-sm font-bold text-slate-800 truncate">{{ $task->name }}</h3>
            @if($task->milestoneRelation && !$isNested)
                <span class="text-xs px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full border border-purple-100">{{ $task->milestoneRelation->name }}</span>
            @endif
        </div>
        <div class="flex items-center gap-3 mt-1">
            @if($task->relatedEntity && $task->rel_type === 'project')
            <a href="{{ route('admin.projects.show', $task->rel_id) }}" class="text-xs text-slate-500 flex items-center gap-1 hover:text-blue-600">
                <i class="bi bi-folder text-slate-400"></i> {{ $task->relatedEntity->name }}
            </a>
            @endif
            <span class="text-xs text-slate-500">{{ $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('M j') : 'No date' }}</span>
        </div>
    </div>

    {{-- Priority --}}
    <div class="flex-shrink-0 hidden md:block">
        <span class="text-xs px-2 py-1 rounded-lg font-medium {{ $task->priority == 'high' || $task->priority == 3 ? 'bg-red-50 text-red-700' : ($task->priority == 'medium' || $task->priority == 2 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">
            {{ is_numeric($task->priority) ? ($task->priority == 3 ? 'High' : ($task->priority == 2 ? 'Medium' : 'Low')) : ucfirst($task->priority) }}
        </span>
    </div>

    {{-- Status --}}
    <div class="flex-shrink-0">
        <span class="text-xs px-2 py-1 rounded-lg font-medium {{ $task->status == 3 ? 'bg-emerald-50 text-emerald-700' : ($task->status == 2 ? 'bg-blue-50 text-blue-700' : 'bg-slate-50 text-slate-700') }}">
            {{ $task->status == 3 ? 'Done' : ($task->status == 2 ? 'In Progress' : ($task->status == 0 ? 'Backlog' : 'To Do')) }}
        </span>
    </div>

    {{-- Assignees --}}
    <div class="flex-shrink-0 hidden lg:flex -space-x-1.5">
        @forelse($task->assignedStaff->take(3) as $assignee)
            <div class="w-7 h-7 rounded-full bg-slate-400 border-2 border-white flex items-center justify-center text-[10px] font-bold text-white" title="{{ $assignee->firstname }} {{ $assignee->lastname }}">
                {{ substr($assignee->firstname, 0, 1) }}
            </div>
        @empty
        @endforelse
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-1 flex-shrink-0" onclick="event.stopPropagation();">
        <a href="{{ route('admin.tasks.show', $task->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View">
            <i class="bi bi-eye"></i>
        </a>
        <a href="{{ route('admin.tasks.edit', $task->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
        <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
</div>