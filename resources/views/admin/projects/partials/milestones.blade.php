@if($milestones->count() > 0)
<div class="space-y-2">
    @foreach($milestones as $milestone)
    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg {{ $milestone->due_date && \Carbon\Carbon::parse($milestone->due_date)->isPast() ? 'bg-red-100' : 'bg-indigo-100' }} flex items-center justify-center">
                <i class="bi bi-flag text-{{ $milestone->due_date && \Carbon\Carbon::parse($milestone->due_date)->isPast() ? 'red' : 'indigo' }}-600 text-sm"></i>
            </div>
            <div>
                <a href="{{ route('admin.milestones.show', $milestone->id) }}" data-pane="show" data-pane-type="milestone" data-pane-id="{{ $milestone->id }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">{{ $milestone->name }}</a>
                @if($milestone->due_date)
                <span class="text-[10px] ml-2 {{ \Carbon\Carbon::parse($milestone->due_date)->isPast() ? 'text-red-500' : 'text-slate-400' }}">
                    {{ \Carbon\Carbon::parse($milestone->due_date)->format('M j, Y') }}
                </span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($milestone->due_date)
            <span class="text-[10px] px-2 py-1 rounded-full {{ \Carbon\Carbon::parse($milestone->due_date)->isPast() ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }}">
                {{ \Carbon\Carbon::parse($milestone->due_date)->isPast() ? 'Overdue' : \Carbon\Carbon::parse($milestone->due_date)->diffInDays(now()) . ' days left' }}
            </span>
            @endif
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('admin.milestones.edit', $milestone->id) }}" data-pane="edit" data-pane-type="milestone" data-pane-id="{{ $milestone->id }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="Edit">
                    <i class="bi bi-pencil text-xs"></i>
                </a>
                <form action="{{ route('admin.milestones.destroy', $milestone->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this milestone?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete">
                        <i class="bi bi-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-flag text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No milestones</p>
</div>
@endif

<div class="mt-4 pt-4 border-t border-slate-200">
    <a href="{{ route('admin.milestones.create') }}?project_id={{ $project->id }}" data-pane="create" data-pane-type="milestone" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
        <i class="bi bi-plus-lg"></i> Add Milestone
    </a>
</div>