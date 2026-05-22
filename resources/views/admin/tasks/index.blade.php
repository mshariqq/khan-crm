@extends('layouts.admin')

@section('breadcrumb', 'Task List')

@push('styles')
<style>
    .task-row {
        transition: all 0.2s;
    }
    .task-row:hover {
        background-color: #f8fafc;
    }
    .priority-indicator {
        width: 3px;
        transition: all 0.2s;
    }
    .task-row:hover .priority-indicator {
        width: 4px;
    }
    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
</style>
@endpush

@section('main')
<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tasks</h1>
            <p class="text-slate-500 text-sm mt-1">Manage tasks across all your projects.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tasks.kanban') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-kanban"></i> Board View
            </a>
            <a href="{{ route('admin.tasks.create') }}" data-pane="create" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Add Task
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium flex items-center gap-2 animate-fade-in">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium flex items-center gap-2 animate-fade-in">
            <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <form action="{{ route('admin.tasks.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Search</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tasks..." class="pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all w-full">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Priority</label>
                <select name="priority" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all min-w-[150px]">
                    <option value="">All Priorities</option>
                    <option value="3" {{ request('priority') == '3' ? 'selected' : '' }}>High</option>
                    <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>Medium</option>
                    <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Status</label>
                <select name="status" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all min-w-[150px]">
                    <option value="">All Status</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Backlog</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>To Do</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>In Progress</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Done</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-all flex items-center gap-2">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('admin.tasks.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Task List with Milestone Hierarchy --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @forelse($groupedTasks as $groupKey => $groupTasks)
            @if($groupKey === 'no_milestone')
                {{-- Tasks without milestone --}}
                @foreach($groupTasks as $task)
                @include('admin.tasks.partials.task-row', ['task' => $task, 'isNested' => false])
                @endforeach
            @else
                {{-- Milestone Header --}}
                @php $milestoneId = str_replace('milestone_', '', $groupKey); @endphp
                @if(isset($milestones[$milestoneId]))
                <div class="sticky top-0 bg-slate-50 border-b border-slate-200 px-4 py-3 flex items-center gap-3 z-10">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="bi bi-flag-fill text-purple-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-slate-800">{{ $milestones[$milestoneId]->name }}</h3>
                        @if(isset($milestones[$milestoneId]->project))
                        <a href="{{ route('admin.projects.show', $milestones[$milestoneId]->project->id) }}" class="text-xs text-slate-500 hover:text-blue-600">{{ $milestones[$milestoneId]->project->name }}</a>
                        @endif
                    </div>
                    <span class="text-xs font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">{{ $groupTasks->count() }} tasks</span>
                </div>
                @endif
                {{-- Tasks under milestone --}}
                @foreach($groupTasks as $task)
                @include('admin.tasks.partials.task-row', ['task' => $task, 'isNested' => true])
                @endforeach
            @endif
        @empty
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-list-check text-slate-300 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 mb-2">No tasks found</h3>
            <p class="text-slate-500 mb-6">Start by creating your first task.</p>
            <a href="{{ route('admin.tasks.create') }}" data-pane="create" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all inline-flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Add Task
            </a>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $tasks->links() }}
    </div>

    </div>
@endsection
