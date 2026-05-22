<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-lg font-bold text-slate-800">{{ $milestone->name }}</h1>
        <p class="text-xs text-slate-400">Milestone</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="parent.openMilestonePane('{{ route('admin.milestones.pane.edit', $milestone->id) }}')" class="px-3 py-1.5 bg-amber-500 text-white text-xs font-medium rounded-lg hover:bg-amber-600 transition-all flex items-center gap-1">
            <i class="bi bi-pencil"></i> Edit
        </button>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
    <div class="space-y-4">
        <div>
            <span class="text-[10px] text-slate-400 uppercase block mb-1">Project</span>
            <p class="text-sm text-slate-700">
                <a href="{{ route('admin.projects.show', $milestone->project->id) }}" class="text-blue-600 hover:underline">{{ $milestone->project->name ?? 'N/A' }}</a>
            </p>
        </div>
        <div>
            <span class="text-[10px] text-slate-400 uppercase block mb-1">Due Date</span>
            <p class="text-sm text-slate-700">{{ $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('F j, Y') : '-' }}</p>
        </div>
        @if($milestone->description)
        <div>
            <span class="text-[10px] text-slate-400 uppercase block mb-1">Description</span>
            <div class="text-sm text-slate-600 whitespace-pre-wrap">{{ $milestone->description }}</div>
        </div>
        @endif
    </div>
</div>
