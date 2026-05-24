@php use App\Models\Project; @endphp
@extends('layouts.admin')

@section('breadcrumb', 'Projects')

@section('main')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Projects</h1>
            <p class="text-xs text-slate-400">Manage your projects</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.projects.portfolio') }}" class="px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition-all flex items-center gap-1">
                <i class="bi bi-grid-3x3-gap"></i> Portfolio
            </a>
            <a href="{{ route('admin.projects.profitability') }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-all flex items-center gap-1">
                <i class="bi bi-graph-up"></i> Profitability
            </a>
            <a href="{{ route('admin.projects.calendar') }}" class="px-3 py-1.5 bg-cyan-600 text-white text-xs font-medium rounded-lg hover:bg-cyan-700 transition-all flex items-center gap-1">
                <i class="bi bi-calendar"></i> Calendar
            </a>
            <a href="{{ route('admin.projects.templates.index') }}" class="px-3 py-1.5 bg-slate-600 text-white text-xs font-medium rounded-lg hover:bg-slate-700 transition-all flex items-center gap-1">
                <i class="bi bi-file-earmark"></i> Templates
            </a>
            <a href="{{ route('admin.projects.export') }}?{{ http_build_query(request()->only(['search', 'client', 'status'])) }}" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-1">
                <i class="bi bi-download"></i> Export
            </a>
            <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-0.5">
                <button onclick="setViewMode('grid')" id="viewGridBtn" class="px-2 py-1.5 text-xs rounded-md transition-all flex items-center gap-1"><i class="bi bi-grid"></i></button>
                <button onclick="setViewMode('list')" id="viewListBtn" class="px-2 py-1.5 text-xs rounded-md transition-all flex items-center gap-1"><i class="bi bi-list"></i></button>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-all flex items-center gap-1">
                <i class="bi bi-plus"></i> New Project
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="bi bi-folder text-blue-600 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-slate-500">Total</p>
                <p class="text-lg font-bold text-slate-800">{{ $total }}</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3 flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                <i class="bi bi-play-circle text-emerald-600 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-slate-500">Active</p>
                <p class="text-lg font-bold text-emerald-600">{{ $activeCount }}</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3 flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                <i class="bi bi-check-circle text-indigo-600 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-slate-500">Completed</p>
                <p class="text-lg font-bold text-indigo-600">{{ $completedCount }}</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3 flex items-center gap-3">
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                <i class="bi bi-pause-circle text-amber-600 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-slate-500">On Hold</p>
                <p class="text-lg font-bold text-amber-600">{{ $onHoldCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
        <form action="{{ route('admin.projects.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <div class="flex-1 min-w-[150px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
            </div>
            <select name="client" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                <option value="">All Clients</option>
                @foreach($clients as $client)
                    <option value="{{ $client->userid }}" {{ request('client') == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                <option value="">All Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Completed</option>
                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>On Hold</option>
                <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <select name="priority" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                <option value="">All Priority</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
            <select name="category_id" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                <option value="">All Categories</option>
                @foreach($categories ?? [] as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="sort" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                <option value="">Default Sort</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                <option value="deadline_asc" {{ request('sort') == 'deadline_asc' ? 'selected' : '' }}>Deadline ↑</option>
                <option value="deadline_desc" {{ request('sort') == 'deadline_desc' ? 'selected' : '' }}>Deadline ↓</option>
                <option value="progress_asc" {{ request('sort') == 'progress_asc' ? 'selected' : '' }}>Progress ↑</option>
                <option value="progress_desc" {{ request('sort') == 'progress_desc' ? 'selected' : '' }}>Progress ↓</option>
                <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
            </select>
            <a href="{{ route('admin.projects.index', request()->has('my_projects') ? array_merge(request()->except('my_projects')) : array_merge(request()->all(), ['my_projects' => '1'])) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-all flex items-center gap-1.5 {{ request('my_projects') ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                <i class="bi {{ request('my_projects') ? 'bi-check-circle-fill' : 'bi-person' }}"></i>
                My Projects
            </a>
            <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded-lg hover:bg-slate-900">Filter</button>
            <a href="{{ route('admin.projects.index') }}" class="px-3 py-1.5 text-slate-500 text-xs hover:text-slate-700">Reset</a>
        </form>
    </div>

    <div id="batchBar" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-center gap-3">
        <label class="flex items-center gap-1.5 text-xs text-blue-700 cursor-pointer">
            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this.checked)" class="rounded border-blue-300 text-blue-600 focus:ring-blue-500">
            Select All
        </label>
        <span class="w-px h-4 bg-blue-200"></span>
        <span class="text-xs text-blue-700"><span id="selectedCount">0</span> selected</span>
        <select id="batchStatusSelect" class="px-2 py-1 text-xs border border-blue-200 rounded bg-white">
            <option value="">Change Status</option>
            <option value="1">Active</option>
            <option value="2">Completed</option>
            <option value="3">On Hold</option>
            <option value="4">Cancelled</option>
        </select>
        <button onclick="batchStatus()" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Apply</button>
        <button onclick="batchDelete()" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Delete</button>
        <button onclick="clearSelection()" class="px-3 py-1 text-slate-500 text-xs hover:text-slate-700">Clear</button>
    </div>

    <div class="flex items-center gap-2 px-1 text-[10px] text-slate-400 font-medium uppercase tracking-wider">
        <a href="{{ route('admin.projects.index', array_merge(request()->except('sort'), ['sort' => request('sort') === 'name_asc' ? 'name_desc' : 'name_asc'])) }}" class="flex items-center gap-1 hover:text-slate-700 transition-colors">
            Name {!! request('sort') === 'name_asc' ? '<i class="bi bi-sort-up text-xs"></i>' : (request('sort') === 'name_desc' ? '<i class="bi bi-sort-down text-xs"></i>' : '') !!}
        </a>
        <span class="w-px h-3 bg-slate-200"></span>
        <a href="{{ route('admin.projects.index', array_merge(request()->except('sort'), ['sort' => request('sort') === 'progress_desc' ? 'progress_asc' : 'progress_desc'])) }}" class="flex items-center gap-1 hover:text-slate-700 transition-colors">
            Progress {!! request('sort') === 'progress_asc' ? '<i class="bi bi-sort-up text-xs"></i>' : (request('sort') === 'progress_desc' ? '<i class="bi bi-sort-down text-xs"></i>' : '') !!}
        </a>
        <span class="w-px h-3 bg-slate-200"></span>
        <a href="{{ route('admin.projects.index', array_merge(request()->except('sort'), ['sort' => request('sort') === 'deadline_asc' ? 'deadline_desc' : 'deadline_asc'])) }}" class="flex items-center gap-1 hover:text-slate-700 transition-colors">
            Deadline {!! request('sort') === 'deadline_asc' ? '<i class="bi bi-sort-up text-xs"></i>' : (request('sort') === 'deadline_desc' ? '<i class="bi bi-sort-down text-xs"></i>' : '') !!}
        </a>
        <span class="w-px h-3 bg-slate-200"></span>
        <a href="{{ route('admin.projects.index', array_merge(request()->except('sort'), ['sort' => request('sort') === 'status' ? '' : 'status'])) }}" class="flex items-center gap-1 hover:text-slate-700 transition-colors">
            Status {!! request('sort') === 'status' ? '<i class="bi bi-check2 text-xs"></i>' : '' !!}
        </a>
    </div>
    <div id="projectsContainer" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($projects as $project)
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-1">
                        <input type="checkbox" class="project-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500" value="{{ $project->id }}" onchange="updateBatchBar()">
                    </div>
                    <div class="flex items-center gap-2">
                        @if($project->status === Project::STATUS_ACTIVE)
                            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        @elseif($project->status === Project::STATUS_COMPLETED)
                            <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                        @elseif($project->status === Project::STATUS_ON_HOLD)
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            @else
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded
                            @if($project->status === Project::STATUS_ACTIVE) bg-emerald-50 text-emerald-700
                            @elseif($project->status === Project::STATUS_COMPLETED) bg-indigo-50 text-indigo-700
                            @elseif($project->status === Project::STATUS_ON_HOLD) bg-amber-50 text-amber-700
                            @else bg-red-50 text-red-700 @endif">
                            @if($project->status === Project::STATUS_ACTIVE) Active
                            @elseif($project->status === Project::STATUS_COMPLETED) Completed
                            @elseif($project->status === Project::STATUS_ON_HOLD) On Hold
                            @else Cancelled @endif
                        </span>
                        @if($project->priority)
                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded {{ \App\Models\Project::priorityColors()[$project->priority] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($project->priority) }}
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.projects.show', $project->id) }}" class="p-1 text-slate-400 hover:text-blue-600" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.projects.edit', $project->id) }}" class="p-1 text-slate-400 hover:text-amber-600" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Delete Project', 'Are you sure you want to delete this project?', '{{ route('admin.projects.destroy', $project->id) }}', 'DELETE');">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1 text-slate-400 hover:text-red-600" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <a href="{{ route('admin.projects.show', $project->id) }}" class="block">
                    <h3 class="text-sm font-bold text-slate-800 hover:text-blue-600 truncate">{{ $project->name }}</h3>
                </a>
                <p class="text-xs text-slate-500 mt-1">{{ $project->client->company ?? 'No Client' }}</p>
                @if($project->category)
                <span class="inline-block mt-1 text-[10px] px-1.5 py-0.5 rounded" style="background: {{ $project->category->color }}20; color: {{ $project->category->color }}">
                    {{ $project->category->name }}
                </span>
                @endif

                <div class="mt-3">
                    <div class="flex items-center justify-between text-[10px] mb-1">
                        <span class="text-slate-500">Progress</span>
                        <span class="text-slate-700 font-medium">{{ $project->progress }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full
                            @if($project->progress == 100) bg-emerald-500
                            @elseif($project->progress >= 50) bg-blue-500
                            @else bg-amber-500 @endif"
                            style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between text-[10px] text-slate-500">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1">
                            <i class="bi bi-list-check"></i> {{ $project->tasks->count() }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="bi bi-receipt"></i> {{ $project->invoices->count() }}
                        </span>
                    </div>
                    @if($project->deadline)
                    <span class="{{ $project->deadline->isPast() ? 'text-red-600' : '' }}">
                        {{ $project->deadline->format('M j') }}
                    </span>
                    @endif
                </div>

                @if($project->staff->count() > 0)
                <div class="mt-3 flex items-center gap-2">
                    <div class="flex -space-x-1">
                        @foreach($project->staff->take(3) as $member)
                        <span class="w-5 h-5 rounded-full bg-slate-400 flex items-center justify-center text-[8px] text-white font-semibold border border-white">
                            {{ substr($member->firstname, 0, 1) }}
                        </span>
                        @endforeach
                        @if($project->staff->count() > 3)
                        <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[8px] text-slate-600 font-semibold border border-white">
                            +{{ $project->staff->count() - 3 }}
                        </span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16">
            <div class="w-20 h-20 mx-auto mb-4 bg-slate-50 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <p class="text-sm font-medium text-slate-500 mb-1">No projects found</p>
            <p class="text-xs text-slate-400">Try adjusting your filters or create a new project</p>
            <a href="{{ route('admin.projects.create') }}" class="mt-4 inline-flex items-center gap-1 px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700"><i class="bi bi-plus"></i> New Project</a>
        </div>
        @endforelse
    </div>

    @if($projects->hasPages())
    <div class="bg-slate-50 border-t border-slate-200 px-4 py-2">
        {{ $projects->links() }}
    </div>
    @endif
</div>
<script>
document.addEventListener('keydown', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) return;
    if (e.key === 'n') { window.location.href = '{{ route("admin.projects.create") }}'; }
    if (e.key === 'f') { document.querySelector('input[name="search"]')?.focus(); }
});

function setViewMode(mode) {
    const container = document.getElementById('projectsContainer');
    const gridBtn = document.getElementById('viewGridBtn');
    const listBtn = document.getElementById('viewListBtn');
    if (mode === 'list') {
        container.className = 'space-y-2';
        gridBtn.className = 'px-2 py-1.5 text-xs rounded-md text-slate-400 hover:text-slate-600';
        listBtn.className = 'px-2 py-1.5 text-xs rounded-md bg-white shadow-sm text-slate-800';
    } else {
        container.className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4';
        gridBtn.className = 'px-2 py-1.5 text-xs rounded-md bg-white shadow-sm text-slate-800';
        listBtn.className = 'px-2 py-1.5 text-xs rounded-md text-slate-400 hover:text-slate-600';
    }
    localStorage.setItem('projectViewMode', mode);
}

document.addEventListener('DOMContentLoaded', function() {
    const savedMode = localStorage.getItem('projectViewMode');
    if (savedMode) setViewMode(savedMode);
    else setViewMode('grid');
});

function updateBatchBar() {
    const checked = document.querySelectorAll('.project-checkbox:checked');
    const bar = document.getElementById('batchBar');
    const count = document.getElementById('selectedCount');
    if (checked.length > 0) {
        bar.classList.remove('hidden');
        count.textContent = checked.length;
    } else {
        bar.classList.add('hidden');
    }
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.project-checkbox:checked')).map(cb => cb.value);
}

function batchStatus() {
    const ids = getSelectedIds();
    const status = document.getElementById('batchStatusSelect').value;
    if (!status || !ids.length) return;
    if (!confirm('Update status for ' + ids.length + ' projects?')) return; // native OK for batch
    fetch('/admin/projects/batch/status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ project_ids: ids, status: parseInt(status) })
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}

function batchDelete() {
    const ids = getSelectedIds();
    if (!ids.length) return;
    if (!confirm('Delete ' + ids.length + ' projects? This cannot be undone.')) return;
    fetch('/admin/projects/batch/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ project_ids: ids })
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}

function toggleSelectAll(checked) {
    document.querySelectorAll('.project-checkbox').forEach(cb => cb.checked = checked);
    updateBatchBar();
}

function clearSelection() {
    document.querySelectorAll('.project-checkbox:checked').forEach(cb => cb.checked = false);
    document.getElementById('selectAllCheckbox').checked = false;
    document.getElementById('batchBar').classList.add('hidden');
}
</script>
@endsection