@extends('layouts.client')

@section('title', 'My Projects')
@section('breadcrumb', 'Projects')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">My Projects</h1>
            <p class="text-sm text-slate-500">View and track your ongoing projects</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('client.projects.export', request()->all()) }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('client.projects.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
            </div>
            <div class="w-full md:w-48">
                <select name="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>In Progress</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Completed</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>On Hold</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-900 transition-all">
                    Apply Filters
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('client.projects.index') }}" class="px-4 py-2.5 text-slate-500 text-sm font-bold hover:text-slate-700 transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($projects as $project)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-2">
                        @php
                            $statusColor = 'bg-slate-100 text-slate-600';
                            $statusLabel = 'Unknown';
                            if($project->status == 1) {
                                $statusColor = 'bg-emerald-50 text-emerald-700';
                                $statusLabel = 'In Progress';
                            } elseif($project->status == 2) {
                                $statusColor = 'bg-blue-50 text-blue-700';
                                $statusLabel = 'Completed';
                            } elseif($project->status == 3) {
                                $statusColor = 'bg-amber-50 text-amber-700';
                                $statusLabel = 'On Hold';
                            }
                        @endphp
                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <a href="{{ route('client.projects.show', $project->id) }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                        <i class="bi bi-arrow-right-circle text-xl"></i>
                    </a>
                </div>

                <a href="{{ route('client.projects.show', $project->id) }}" class="block group">
                    <h3 class="text-lg font-bold text-slate-800 group-hover:text-blue-600 transition-colors truncate">{{ $project->name }}</h3>
                </a>

                <div class="mt-6">
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="text-slate-500 font-medium">Project Progress</span>
                        <span class="text-slate-900 font-bold">{{ $project->progress }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-500
                            @if($project->progress == 100) bg-emerald-500
                            @elseif($project->progress >= 50) bg-blue-500
                            @else bg-amber-500 @endif"
                            style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider font-bold mb-1">Tasks</p>
                        <p class="text-sm font-bold text-slate-800">{{ $project->tasks->count() }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider font-bold mb-1">Deadline</p>
                        <p class="text-sm font-bold {{ $project->deadline && \Carbon\Carbon::parse($project->deadline)->isPast() ? 'text-red-600' : 'text-slate-800' }}">
                            {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M d, Y') : 'No deadline' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-dashed border-slate-300">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                <i class="bi bi-folder2-open text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No projects found</h3>
            <p class="text-slate-500">Try adjusting your search or filters.</p>
        </div>
        @endforelse
    </div>

    @if($projects->hasPages())
    <div class="mt-8">
        {{ $projects->links() }}
    </div>
    @endif
</div>
@endsection
