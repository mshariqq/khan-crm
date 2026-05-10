@extends('layouts.admin')

@section('breadcrumb', 'Projects')

@section('main')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Projects</h1>
            <p class="text-xs text-slate-400">Manage your projects</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-all flex items-center gap-1">
            <i class="bi bi-plus"></i> New Project
        </a>
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
            </select>
            <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded-lg hover:bg-slate-900">Filter</button>
            <a href="{{ route('admin.projects.index') }}" class="px-3 py-1.5 text-slate-500 text-xs hover:text-slate-700">Reset</a>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($projects as $project)
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        @if($project->status == 1)
                            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        @elseif($project->status == 2)
                            <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                        @else
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        @endif
                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded
                            @if($project->status == 1) bg-emerald-50 text-emerald-700
                            @elseif($project->status == 2) bg-indigo-50 text-indigo-700
                            @else bg-amber-50 text-amber-700 @endif">
                            @if($project->status == 1) Active
                            @elseif($project->status == 2) Completed
                            @else On Hold @endif
                        </span>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.projects.show', $project->id) }}" class="p-1 text-slate-400 hover:text-blue-600" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.projects.edit', $project->id) }}" class="p-1 text-slate-400 hover:text-amber-600" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete?');">
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
                    <span class="{{ \Carbon\Carbon::parse($project->deadline)->isPast() ? 'text-red-600' : '' }}">
                        {{ \Carbon\Carbon::parse($project->deadline)->format('M j') }}
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
        <div class="col-span-full text-center py-12 text-slate-400">
            <i class="bi bi-folder text-2xl mb-2"></i>
            <p class="text-sm">No projects found</p>
        </div>
        @endforelse
    </div>

    @if($projects->hasPages())
    <div class="bg-slate-50 border-t border-slate-200 px-4 py-2">
        {{ $projects->links() }}
    </div>
    @endif
</div>
@endsection