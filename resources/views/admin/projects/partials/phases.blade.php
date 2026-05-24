<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Phases ({{ $project->phases->count() }})</h3>
    <button onclick="document.getElementById('inlinePhaseForm').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Phase
    </button>
</div>

<form id="inlinePhaseForm" action="{{ route('admin.projects.phases.store', $project->id) }}" method="POST" class="hidden mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
            <input type="text" name="name" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Color</label>
            <input type="color" name="color" class="w-full h-9 px-1 border border-slate-200 rounded-lg cursor-pointer">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Start Date</label>
            <input type="date" name="start_date" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">End Date</label>
            <input type="date" name="end_date" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Description</label>
        <textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
    </div>
    <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('inlinePhaseForm').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800">Cancel</button>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Create Phase</button>
    </div>
</form>

@if($project->phases->count() > 0)
<div class="relative">
    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
    <div class="space-y-4 relative">
        @foreach($project->phases as $phase)
        <div class="relative pl-10">
            <div class="absolute left-2.5 top-3 w-3 h-3 rounded-full border-2 border-white shadow" style="background-color: {{ $phase->color ?? '#6366f1' }};"></div>
            <div class="p-4 bg-white border border-slate-200 rounded-lg hover:border-slate-300 transition-colors group">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full flex-shrink-0 mt-0.5" style="background-color: {{ $phase->color ?? '#6366f1' }};"></div>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800">{{ $phase->name }}</h4>
                            @if($phase->description)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $phase->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                            @if($phase->status == 'completed') bg-emerald-100 text-emerald-700
                            @elseif($phase->status == 'in_progress') bg-blue-100 text-blue-700
                            @elseif($phase->status == 'pending') bg-amber-100 text-amber-700
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ ucfirst(str_replace('_', ' ', $phase->status ?? 'pending')) }}
                        </span>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-1 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="Edit"><i class="bi bi-pencil text-xs"></i></button>
                            <form action="{{ route('admin.projects.phases.destroy', [$project->id, $phase->id]) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this phase?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete"><i class="bi bi-trash text-xs"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4 mt-3 text-[10px] text-slate-400">
                    @if($phase->start_date)
                    <span class="flex items-center gap-1"><i class="bi bi-calendar3"></i> {{ $phase->start_date->format('M j, Y') }}</span>
                    @endif
                    @if($phase->end_date)
                    <span class="flex items-center gap-1"><i class="bi bi-calendar-check"></i> {{ $phase->end_date->format('M j, Y') }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-layers text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No phases yet</p>
    <button onclick="document.getElementById('inlinePhaseForm').classList.remove('hidden')" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Phase</button>
</div>
@endif
