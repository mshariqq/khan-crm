@extends('layouts.admin')

@section('breadcrumb', 'Project Dashboard')

@push('styles')
<style>
    /* Kanban Board Styles */
    .kanban-column {
        min-height: 200px;
        transition: all 0.2s;
    }
    .kanban-column:hover {
        background-color: rgba(248, 250, 252, 0.8);
    }
    .kanban-card {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .priority-high { border-left: 3px solid #ef4444; }
    .priority-medium { border-left: 3px solid #f59e0b; }
    .priority-low { border-left: 3px solid #10b981; }

    /* Timeline Styles */
    .timeline-item {
        position: relative;
        padding-left: 32px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 32px;
        bottom: -16px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item:last-child::before {
        display: none;
    }
    .timeline-dot {
        position: absolute;
        left: 0;
        top: 4px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #cbd5e1;
    }
    .timeline-dot.completed {
        background: #10b981;
        box-shadow: 0 0 0 2px #10b981;
    }
    .timeline-dot.in-progress {
        background: #3b82f6;
        box-shadow: 0 0 0 2px #3b82f6;
    }
    .timeline-dot.pending {
        background: #f59e0b;
        box-shadow: 0 0 0 2px #f59e0b;
    }

    /* Activity Feed */
    .activity-item {
        transition: all 0.2s;
    }
    .activity-item:hover {
        background-color: #f8fafc;
    }

    /* Chart Bars */
    .chart-bar {
        transition: all 0.5s ease-out;
    }
    .chart-bar:hover {
        opacity: 0.8;
    }

    /* Tab Navigation */
    .dashboard-tab {
        position: relative;
        transition: all 0.2s;
    }
    .dashboard-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background: #3b82f6;
    }
    .dashboard-tab.active {
        color: #3b82f6;
    }

    /* Smooth Sections */
    .dashboard-section {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('main')
<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.projects.index') }}" class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-800">{{ $project->name }}</h1>
                    @if($project->status == 1)
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-200">Active</span>
                    @elseif($project->status == 2)
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-200">Completed</span>
                    @else
                        <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-200">On Hold</span>
                    @endif
                </div>
                <p class="text-slate-500 text-sm mt-0.5 flex items-center gap-2">
                    <i class="bi bi-building"></i> @if($project->client)<a href="{{ route('admin.clients.show', $project->client->userid) }}" class="hover:text-blue-600">{{ $project->client->company }}</a>@else No Client @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.tasks.create') }}?rel_type=project&rel_id={{ $project->id }}&referer={{ url()->current() }}" data-pane="create" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Add Task
            </a>
        </div>
    </div>

    {{-- Dashboard Tabs --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-2">
        <div class="flex items-center gap-1 overflow-x-auto">
            <button onclick="showSection('overview')" id="tab-overview" class="dashboard-tab active px-4 py-3 text-sm font-medium text-slate-600 hover:text-slate-800 whitespace-nowrap">Overview</button>
            <button onclick="showSection('kanban')" id="tab-kanban" class="dashboard-tab px-4 py-3 text-sm font-medium text-slate-600 hover:text-slate-800 whitespace-nowrap">Kanban Board</button>
            <button onclick="showSection('milestones')" id="tab-milestones" class="dashboard-tab px-4 py-3 text-sm font-medium text-slate-600 hover:text-slate-800 whitespace-nowrap">Milestones</button>
            <button onclick="showSection('activity')" id="tab-activity" class="dashboard-tab px-4 py-3 text-sm font-medium text-slate-600 hover:text-slate-800 whitespace-nowrap">Activity</button>
            <button onclick="showSection('team')" id="tab-team" class="dashboard-tab px-4 py-3 text-sm font-medium text-slate-600 hover:text-slate-800 whitespace-nowrap">Team</button>
        </div>
    </div>

    {{-- Overview Section --}}
    <div id="section-overview" class="dashboard-section space-y-6">
        {{-- Metrics Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Tasks</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ $project->tasks->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center">
                        <i class="bi bi-list-check text-indigo-600 text-xl"></i>
                    </div>
                </div>
                @php
                    $completedTasks = $project->tasks->where('status', 3)->count();
                    $totalTasks = $project->tasks->count();
                    $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                @endphp
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                        <span>{{ $completedTasks }}/{{ $totalTasks }} done</span>
                        <span>{{ $taskProgress }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5">
                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all" style="width: {{ $taskProgress }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Invoices</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ $project->invoices->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i class="bi bi-receipt text-blue-600 text-xl"></i>
                    </div>
                </div>
                @php
                    $paidInvoices = $project->invoices->where('status', 'paid')->sum('total');
                    $totalInvoices = $project->invoices->sum('total');
                @endphp
                <div class="mt-3 text-xs text-slate-500">
                    <span class="font-medium text-slate-700">{{ number_format($paidInvoices, 2) }}</span> / {{ number_format($totalInvoices, 2) }} paid
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Expenses</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ $project->expenses->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">
                        <i class="bi bi-cash-stack text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-500">
                    Total: <span class="font-medium text-slate-700">{{ number_format($project->expenses->sum('amount'), 2) }}</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Milestones</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ $project->milestones->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                        <i class="bi bi-flag text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-500">
                    @php
                        $passedMilestones = $project->milestones->filter(function($m) {
                            return $m->due_date && \Carbon\Carbon::parse($m->due_date)->isPast();
                        })->count();
                    @endphp
                    <span class="font-medium text-slate-700">{{ $passedMilestones }}</span> passed
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Files</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ $project->files->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                        <i class="bi bi-file-earmark-text text-emerald-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-500">
                    <span class="font-medium text-slate-700">{{ $project->files->where('visible_to_customer', 1)->count() }}</span> client-visible
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Project Details & Quick Actions --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Project Details --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="bi bi-info-circle text-blue-600"></i> Project Details
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                                <span class="text-sm text-slate-500">Start Date</span>
                                <span class="text-sm font-semibold text-slate-800">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M j, Y') : '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                                <span class="text-sm text-slate-500">Deadline</span>
                                <span class="text-sm font-semibold text-slate-800 {{ $project->deadline && \Carbon\Carbon::parse($project->deadline)->isPast() ? 'text-red-600' : '' }}">
                                    {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M j, Y') : '—' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                                <span class="text-sm text-slate-500">Billing Type</span>
                                <span class="text-sm font-semibold text-slate-800">{{ $project->billing_type == 1 ? 'Fixed Price' : ($project->billing_type == 2 ? 'Hourly' : '—') }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                                <span class="text-sm text-slate-500">Project Cost</span>
                                <span class="text-sm font-semibold text-slate-800">{{ number_format($project->project_cost ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                                <span class="text-sm text-slate-500">Rate/Hour</span>
                                <span class="text-sm font-semibold text-slate-800">{{ number_format($project->project_rate_per_hour ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                                <span class="text-sm text-slate-500">Estimated Hours</span>
                                <span class="text-sm font-semibold text-slate-800">{{ number_format($project->estimated_hours ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-slate-500">Progress</span>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="flex-1 bg-slate-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $project->progress }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-slate-800">{{ $project->progress }}%</span>
                        </div>
                    </div>
                </div>

                {{-- Recent Tasks --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-6 pb-3 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-list-check text-indigo-600"></i> Recent Tasks
                        </h2>
                        <a href="{{ route('admin.tasks.index') }}?project_id={{ $project->id }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($project->tasks->take(5) as $task)
                        <div class="p-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                @if($task->priority == 'high') bg-red-50 text-red-600
                                @elseif($task->priority == 'medium') bg-amber-50 text-amber-600
                                @else bg-emerald-50 text-emerald-600 @endif">
                                <i class="bi bi-circle-fill text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('admin.tasks.show', $task) }}" data-pane="show" data-pane-id="{{ $task->id }}" class="text-sm font-semibold text-slate-800 hover:text-blue-600 truncate block">{{ $task->name }}</a>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-slate-500">Due {{ $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('M j') : '—' }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        @if($task->status == 3) bg-emerald-50 text-emerald-700
                                        @elseif($task->status == 2) bg-blue-50 text-blue-700
                                        @else bg-slate-50 text-slate-700 @endif">
                                        {{ $task->status == 3 ? 'Done' : ($task->status == 2 ? 'In Progress' : 'To Do') }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex -space-x-1.5 flex-shrink-0">
                                @foreach($task->assignedStaff->take(3) as $assignee)
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white flex items-center justify-center text-[10px] font-bold text-white" title="{{ $assignee->firstname }} {{ $assignee->lastname }}">
                                        {{ substr($assignee->firstname, 0, 1) }}{{ substr($assignee->lastname, 0, 1) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center">
                            <p class="text-slate-500 text-sm">No tasks yet. <a href="{{ route('admin.tasks.create') }}?rel_type=project&rel_id={{ $project->id }}&referer={{ url()->current() }}" data-pane="create" class="text-blue-600 font-medium">Create one</a></p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right Column: Team & Quick Stats --}}
            <div class="space-y-6">
                {{-- Team Members --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="bi bi-people text-purple-600"></i> Team
                    </h2>
                    <div class="space-y-3">
                        @forelse($project->staff as $member)
                        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-sm font-bold text-white">
                                {{ substr($member->firstname, 0, 1) }}{{ substr($member->lastname, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ $member->firstname }} {{ $member->lastname }}</p>
                                <p class="text-xs text-slate-500">{{ $member->email ?? 'Member' }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-slate-500 text-sm">No team members assigned.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">Quick Actions</h2>
                    <div class="space-y-2">
                        <a href="{{ route('admin.invoices.create') }}?project_id={{ $project->id }}&referer={{ url()->current() }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center"><i class="bi bi-receipt text-blue-600"></i></div>
                            <span class="text-sm font-medium text-slate-700">Create Invoice</span>
                        </a>
                        <a href="{{ route('admin.expenses.create') }}?project_id={{ $project->id }}&referer={{ url()->current() }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center"><i class="bi bi-cash-stack text-red-600"></i></div>
                            <span class="text-sm font-medium text-slate-700">Add Expense</span>
                        </a>
                        <a href="{{ route('admin.milestones.create') }}?project_id={{ $project->id }}" data-pane="create" data-pane-type="milestone" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center"><i class="bi bi-flag text-purple-600"></i></div>
                            <span class="text-sm font-medium text-slate-700">Add Milestone</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center"><i class="bi bi-file-earmark-text text-emerald-600"></i></div>
                            <span class="text-sm font-medium text-slate-700">Upload File</span>
                        </a>
                    </div>
                </div>

                {{-- Upcoming Deadlines --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="bi bi-calendar-event text-amber-600"></i> Upcoming
                    </h2>
                    <div class="space-y-3">
                        @forelse($project->tasks->where('status', '!=', 3)->sortBy('duedate')->take(5) as $task)
                        <div class="flex items-start gap-3 p-2 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0
                                @if($task->duedate && \Carbon\Carbon::parse($task->duedate)->isPast()) bg-red-500
                                @elseif($task->duedate && \Carbon\Carbon::parse($task->duedate)->diffInDays() <= 3) bg-amber-500
                                @else bg-emerald-500 @endif">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $task->name }}</p>
                                <p class="text-xs text-slate-500">
                                    @if($task->duedate)
                                        {{ \Carbon\Carbon::parse($task->duedate)->diffForHumans() }}
                                    @else
                                        No deadline
                                    @endif
                                </p>
                            </div>
                        </div>
                        @empty
                        <p class="text-slate-500 text-sm text-center py-4">All tasks completed!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kanban Board Section --}}
    <div id="section-kanban" class="dashboard-section hidden space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">Task Board</h2>
            <a href="{{ route('admin.tasks.create') }}?rel_type=project&rel_id={{ $project->id }}&referer={{ url()->current() }}" data-pane="create" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Add Task
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @php
                $kanbanColumns = [
                    0 => ['label' => 'To Do', 'color' => 'bg-slate-100', 'border' => 'border-slate-200', 'badge' => 'bg-slate-200 text-slate-700'],
                    1 => ['label' => 'In Progress', 'color' => 'bg-blue-50', 'border' => 'border-blue-200', 'badge' => 'bg-blue-200 text-blue-700'],
                    2 => ['label' => 'In Review', 'color' => 'bg-amber-50', 'border' => 'border-amber-200', 'badge' => 'bg-amber-200 text-amber-700'],
                    3 => ['label' => 'Done', 'color' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'badge' => 'bg-emerald-200 text-emerald-700'],
                ];
                $taskStatuses = [0, 1, 2, 3];
            @endphp
            @foreach($taskStatuses as $statusId)
            <div class="kanban-column {{ $kanbanColumns[$statusId]['color'] }} rounded-2xl border {{ $kanbanColumns[$statusId]['border'] }} p-3">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-sm text-slate-700">{{ $kanbanColumns[$statusId]['label'] }}</h3>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $kanbanColumns[$statusId]['badge'] }}">
                            {{ $project->tasks->where('status', $statusId)->count() }}
                        </span>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($project->tasks->where('status', $statusId) as $task)
                    <div class="kanban-card bg-white rounded-xl p-3 border border-slate-200 shadow-sm priority-{{ $task->priority }}">
                        <div class="flex items-start justify-between mb-2">
                            <a href="{{ route('admin.tasks.show', $task) }}" data-pane="show" data-pane-id="{{ $task->id }}" class="text-sm font-semibold text-slate-800 hover:text-blue-600 leading-tight">{{ $task->name }}</a>
                            @if($task->priority == 'high')
                                <span class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0 mt-1"></span>
                            @elseif($task->priority == 'medium')
                                <span class="w-2 h-2 bg-amber-500 rounded-full flex-shrink-0 mt-1"></span>
                            @else
                                <span class="w-2 h-2 bg-emerald-500 rounded-full flex-shrink-0 mt-1"></span>
                            @endif
                        </div>
                        @if($task->duedate)
                        <div class="flex items-center gap-1 text-xs text-slate-500 mb-2">
                            <i class="bi bi-calendar-event text-slate-400"></i>
                            <span class="{{ \Carbon\Carbon::parse($task->duedate)->isPast() ? 'text-red-600 font-medium' : '' }}">
                                {{ \Carbon\Carbon::parse($task->duedate)->format('M j') }}
                            </span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <div class="flex -space-x-1.5">
                                @foreach($task->assignedStaff->take(3) as $assignee)
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border border-white flex items-center justify-center text-[9px] font-bold text-white" title="{{ $assignee->firstname }} {{ $assignee->lastname }}">
                                        {{ substr($assignee->firstname, 0, 1) }}
                                    </div>
                                @endforeach
                            </div>
                            @if($task->comments->count() > 0)
                                <span class="text-xs text-slate-400 flex items-center gap-1">
                                    <i class="bi bi-chat"></i> {{ $task->comments->count() }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <p class="text-slate-400 text-xs">No tasks</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Milestones Section --}}
    <div id="section-milestones" class="dashboard-section hidden space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">Milestones & Timeline</h2>
            <a href="{{ route('admin.milestones.create') }}?project_id={{ $project->id }}" data-pane="create" data-pane-type="milestone" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Add Milestone
            </a>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            @if($project->milestones->count() > 0)
            <div class="space-y-0">
                @foreach($project->milestones->sortBy('due_date') as $milestone)
                    @php
                        $mTasks = $project->tasks->where('milestone', $milestone->id);
                        $mCompleted = $mTasks->where('status', 3)->count();
                        $mTotal = $mTasks->count();
                        $mProgress = $mTotal > 0 ? round(($mCompleted / $mTotal) * 100) : 0;
                        $isPast = $milestone->due_date && \Carbon\Carbon::parse($milestone->due_date)->isPast();
                        $isNear = $milestone->due_date && !$isPast && \Carbon\Carbon::parse($milestone->due_date)->diffInDays() <= 7;
                    @endphp
                    <div class="timeline-item py-4">
                        <div class="timeline-dot {{ $mProgress == 100 ? 'completed' : ($mProgress > 0 ? 'in-progress' : 'pending') }}"></div>
                        <div class="flex flex-col md:flex-row md:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.milestones.show', $milestone->id) }}" data-pane="show" data-pane-type="milestone" data-pane-id="{{ $milestone->id }}" class="text-base font-bold text-slate-800 hover:text-blue-600">{{ $milestone->name }}</a>
                                    @if($isPast && $mProgress < 100)
                                        <span class="px-2 py-0.5 bg-red-50 text-red-700 text-xs font-bold rounded-full border border-red-200">Overdue</span>
                                    @elseif($isNear)
                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-full border border-amber-200">Due Soon</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-500 mt-1">{{ Str::limit($milestone->description, 100) }}</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-xs text-slate-500"><i class="bi bi-calendar-event mr-1"></i>{{ $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('M j, Y') : 'No date' }}</span>
                                    <span class="text-xs text-slate-500"><i class="bi bi-list-check mr-1"></i>{{ $mCompleted }}/{{ $mTotal }} tasks</span>
                                </div>
                            </div>
                            <div class="w-full md:w-48 flex-shrink-0">
                                <div class="flex justify-between text-xs text-slate-500 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $mProgress }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $mProgress }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-flag text-slate-300 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 mb-2">No milestones yet</h3>
                <p class="text-slate-500 mb-6">Break your project into milestones to track progress.</p>
                <a href="{{ route('admin.milestones.create') }}?project_id={{ $project->id }}" data-pane="create" data-pane-type="milestone" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all inline-flex items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Add Milestone
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Activity Section --}}
    <div id="section-activity" class="dashboard-section hidden space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">Activity Feed</h2>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            @if($project->activities->count() > 0)
            <div class="space-y-0">
                @foreach($project->activities->take(20) as $activity)
                <div class="activity-item flex items-start gap-4 py-4 border-b border-slate-100 last:border-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                        {{ substr($activity->staff->firstname ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-800">
                            <span class="font-semibold">{{ $activity->staff->firstname ?? 'Someone' }} {{ $activity->staff->lastname ?? '' }}</span>
                            {{ $activity->description_key }}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">{{ \Carbon\Carbon::parse($activity->dateadded)->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-clock-history text-slate-300 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 mb-2">No activity yet</h3>
                <p class="text-slate-500">Activity will appear here as team members work on the project.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Team Section --}}
    <div id="section-team" class="dashboard-section hidden space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">Project Team</h2>
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-person-plus"></i> Manage Team
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($project->staff as $member)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-lg font-bold text-white flex-shrink-0">
                    {{ substr($member->firstname, 0, 1) }}{{ substr($member->lastname, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-slate-800">{{ $member->firstname }} {{ $member->lastname }}</h3>
                    <p class="text-sm text-slate-500">{{ $member->email ?? 'Team Member' }}</p>
                    @php
                        $memberTasks = $project->tasks->filter(function($t) use ($member) {
                            return $t->assignedStaff->contains('id', $member->id);
                        });
                    @endphp
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-xs text-slate-500"><i class="bi bi-list-check mr-1"></i>{{ $memberTasks->count() }} tasks</span>
                        <span class="text-xs text-slate-500"><i class="bi bi-check2-all mr-1"></i>{{ $memberTasks->where('status', 3)->count() }} done</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-people text-slate-300 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 mb-2">No team members</h3>
                <p class="text-slate-500 mb-6">Add team members to collaborate on this project.</p>
                <a href="{{ route('admin.projects.edit', $project->id) }}" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all inline-flex items-center gap-2">
                    <i class="bi bi-person-plus"></i> Add Team Members
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function showSection(section) {
        // Hide all sections
        document.querySelectorAll('.dashboard-section').forEach(el => {
            el.classList.add('hidden');
        });
        // Remove active from tabs
        document.querySelectorAll('.dashboard-tab').forEach(el => {
            el.classList.remove('active');
        });
        // Show selected section
        document.getElementById('section-' + section).classList.remove('hidden');
        document.getElementById('tab-' + section).classList.add('active');

        // Store preference
        localStorage.setItem('projectDashboardTab', section);
    }

    // Restore last active tab
    document.addEventListener('DOMContentLoaded', function() {
        const lastTab = localStorage.getItem('projectDashboardTab');
        if (lastTab && document.getElementById('section-' + lastTab)) {
            showSection(lastTab);
        }
    });
</script>
@endsection
