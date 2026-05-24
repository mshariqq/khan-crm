@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('breadcrumb', 'Dashboard')

@section('main')
{{-- Welcome Header with Live Clock & Quick Actions --}}
<div class="mb-8 animate-fade-in-up">
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 heading">Welcome back, {{ $user->firstname }}!</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-2">
                <span>{{ now()->format('l, F j, Y') }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span x-data="{ time: new Date().toLocaleTimeString() }" x-init="setInterval(() => time = new Date().toLocaleTimeString(), 1000)" x-text="time" class="font-mono text-sm tabular-nums text-slate-400"></span>
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.invoices.create') }}" class="quick-action-btn inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200">
                <i class="bi bi-plus-circle"></i> New Invoice
            </a>
            <a href="{{ route('admin.projects.create') }}" class="quick-action-btn inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm shadow-emerald-200">
                <i class="bi bi-plus-circle"></i> New Project
            </a>
            <a href="{{ route('admin.clients.create') }}" class="quick-action-btn inline-flex items-center gap-1.5 px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition-colors shadow-sm shadow-violet-200">
                <i class="bi bi-plus-circle"></i> New Client
            </a>
            <a href="{{ route('admin.tasks.pane.create') }}" data-pane="create" data-pane-type="task" class="quick-action-btn inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors shadow-sm shadow-amber-200">
                <i class="bi bi-plus-circle"></i> New Task
            </a>
        </div>
    </div>
</div>

{{-- Premium Stat Cards Row --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    {{-- Revenue --}}
    <a href="{{ route('admin.invoices.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 hover-lift relative overflow-hidden animate-fade-in-up animate-fade-in-up-delay-1 block cursor-pointer">
        <div class="absolute top-0 right-0 w-20 h-20 -mr-6 -mt-6 rounded-full bg-blue-50 opacity-60"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Revenue</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="bi bi-currency-dollar text-blue-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-slate-900 stat-value">{{ app_format_money($stats['revenue_this_month']) }}</p>
            @if($baseCurrency)
                <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $baseCurrency->name }} ({{ $baseCurrency->symbol }})</p>
            @endif
            <div class="flex items-center gap-1 mt-1">
                @if($stats['revenue_trend'] > 0)
                    <i class="bi bi-arrow-up-short text-emerald-500 text-sm"></i>
                    <span class="text-xs font-medium text-emerald-600">+{{ $stats['revenue_trend'] }}%</span>
                @elseif($stats['revenue_trend'] < 0)
                    <i class="bi bi-arrow-down-short text-red-500 text-sm"></i>
                    <span class="text-xs font-medium text-red-600">{{ $stats['revenue_trend'] }}%</span>
                @else
                    <span class="text-xs text-slate-400">No change</span>
                @endif
                <span class="text-xs text-slate-400 ml-1">vs last month</span>
            </div>
        </div>
    </a>

    {{-- Clients --}}
    <a href="{{ route('admin.clients.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 hover-lift relative overflow-hidden animate-fade-in-up animate-fade-in-up-delay-2 block cursor-pointer">
        <div class="absolute top-0 right-0 w-20 h-20 -mr-6 -mt-6 rounded-full bg-violet-50 opacity-60"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Clients</span>
                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center">
                    <i class="bi bi-people text-violet-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-slate-900 stat-value">{{ $stats['total_clients'] }}</p>
            <div class="flex items-center gap-1 mt-1">
                @if($stats['clients_trend'] > 0)
                    <i class="bi bi-arrow-up-short text-emerald-500 text-sm"></i>
                    <span class="text-xs font-medium text-emerald-600">+{{ $stats['clients_trend'] }}%</span>
                @elseif($stats['clients_trend'] < 0)
                    <i class="bi bi-arrow-down-short text-red-500 text-sm"></i>
                    <span class="text-xs font-medium text-red-600">{{ $stats['clients_trend'] }}%</span>
                @else
                    <span class="text-xs text-slate-400">No change</span>
                @endif
                <span class="text-xs text-slate-400 ml-1">this month</span>
            </div>
        </div>
    </a>

    {{-- Active Projects --}}
    <a href="{{ route('admin.projects.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 hover-lift relative overflow-hidden animate-fade-in-up animate-fade-in-up-delay-3 block cursor-pointer">
        <div class="absolute top-0 right-0 w-20 h-20 -mr-6 -mt-6 rounded-full bg-emerald-50 opacity-60"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Active Projects</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <i class="bi bi-kanban-fill text-emerald-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-slate-900 stat-value">{{ $stats['active_projects'] }}</p>
            <div class="flex items-center gap-1 mt-1">
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1">
                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ $stats['project_completion_rate'] }}%"></div>
                </div>
                <span class="text-xs text-slate-400 ml-2">{{ $stats['project_completion_rate'] }}% done</span>
            </div>
        </div>
    </a>

    {{-- Pending Tasks --}}
    <a href="{{ route('admin.tasks.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 hover-lift relative overflow-hidden animate-fade-in-up animate-fade-in-up-delay-4 block cursor-pointer">
        <div class="absolute top-0 right-0 w-20 h-20 -mr-6 -mt-6 rounded-full bg-amber-50 opacity-60"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pending Tasks</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <i class="bi bi-list-task text-amber-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-slate-900 stat-value">{{ $stats['pending_tasks'] }}</p>
            <div class="flex items-center gap-1 mt-1">
                <i class="bi bi-arrow-right-short text-slate-400 text-sm"></i>
                <span class="text-xs text-slate-500">{{ $stats['in_progress_tasks'] }} in progress</span>
            </div>
        </div>
    </a>

    {{-- Open Tickets --}}
    <a href="{{ route('admin.tickets.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 hover-lift relative overflow-hidden animate-fade-in-up animate-fade-in-up-delay-5 block cursor-pointer">
        <div class="absolute top-0 right-0 w-20 h-20 -mr-6 -mt-6 rounded-full bg-purple-50 opacity-60"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Open Tickets</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="bi bi-ticket-perforated-fill text-purple-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-slate-900 stat-value">{{ $stats['open_tickets'] }}</p>
            <div class="flex items-center gap-1 mt-1">
                <i class="bi bi-arrow-right-short text-slate-400 text-sm"></i>
                <span class="text-xs text-slate-500">{{ $stats['total_leads'] }} total leads</span>
            </div>
        </div>
    </a>

    {{-- Conversion Rate --}}
    <a href="{{ route('admin.leads.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 hover-lift relative overflow-hidden animate-fade-in-up animate-fade-in-up-delay-5 block cursor-pointer">
        <div class="absolute top-0 right-0 w-20 h-20 -mr-6 -mt-6 rounded-full bg-cyan-50 opacity-60"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Lead Conv.</span>
                <div class="w-8 h-8 rounded-lg bg-cyan-50 flex items-center justify-center">
                    <i class="bi bi-graph-up-arrow text-cyan-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-slate-900 stat-value">{{ $stats['lead_conversion_rate'] }}%</p>
            <div class="flex items-center gap-1 mt-1">
                <i class="bi bi-arrow-right-short text-slate-400 text-sm"></i>
                <span class="text-xs text-slate-500">{{ $stats['converted_leads'] }} converted</span>
            </div>
        </div>
    </a>
</div>

{{-- My Workstation --}}
<div class="flex items-center gap-2 mb-4">
    <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center">
        <i class="bi bi-person-workspace text-indigo-600 text-sm"></i>
    </div>
    <h2 class="text-base font-bold text-slate-900 heading">My Workstation</h2>
</div>
<div class="mb-8">
    @include('admin.partials.attendance_widget')
</div>

{{-- Financial Performance --}}
<div class="flex items-center gap-2 mb-4">
    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
        <i class="bi bi-graph-up-arrow text-blue-600 text-sm"></i>
    </div>
    <h2 class="text-base font-bold text-slate-900 heading">Financial Performance</h2>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Revenue Trend Line Chart --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 hover-lift">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="bi bi-graph-up text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-900 heading text-sm">Revenue Trend</h3>
                    <p class="text-xs text-slate-400">Last 12 months</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Revenue</span>
            </div>
        </div>
        <div class="chart-container" style="height: 240px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Revenue vs Expenses Bar Chart --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 hover-lift">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <i class="bi bi-bar-chart-fill text-emerald-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-900 heading text-sm">Revenue vs Expenses</h3>
                    <p class="text-xs text-slate-400">Monthly comparison</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Revenue</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Expenses</span>
            </div>
        </div>
        <div class="chart-container" style="height: 240px;">
            <canvas id="expenseChart"></canvas>
        </div>
    </div>
</div>

{{-- Operations Overview --}}
<div class="flex items-center gap-2 mb-4">
    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
        <i class="bi bi-grid-3x3-gap-fill text-emerald-600 text-sm"></i>
    </div>
    <h2 class="text-base font-bold text-slate-900 heading">Operations Overview</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Task Status Distribution --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 hover-lift">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                <i class="bi bi-check2-square text-amber-600 text-sm"></i>
            </div>
            <h3 class="font-semibold text-slate-900 heading text-sm">Task Status</h3>
        </div>
        <div class="chart-container" style="height: 180px;">
            <canvas id="taskStatusChart"></canvas>
        </div>
        <div class="flex flex-wrap gap-3 mt-3 justify-center text-xs">
            @foreach($taskStatusDist as $s)
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:{{ $s['color'] }}"></span> {{ $s['label'] }} ({{ $s['value'] }})</span>
            @endforeach
        </div>
    </div>

    {{-- Project Status Distribution --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 hover-lift">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                <i class="bi bi-kanban text-emerald-600 text-sm"></i>
            </div>
            <h3 class="font-semibold text-slate-900 heading text-sm">Project Status</h3>
        </div>
        <div class="chart-container" style="height: 180px;">
            <canvas id="projectStatusChart"></canvas>
        </div>
        <div class="flex flex-wrap gap-3 mt-3 justify-center text-xs">
            @foreach($projectStatusDist as $s)
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:{{ $s['color'] }}"></span> {{ $s['label'] }} ({{ $s['value'] }})</span>
            @endforeach
        </div>
    </div>

    {{-- Lead Pipeline --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 hover-lift">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center">
                <i class="bi bi-funnel text-violet-600 text-sm"></i>
            </div>
            <h3 class="font-semibold text-slate-900 heading text-sm">Lead Pipeline</h3>
        </div>
        <div class="chart-container" style="height: 180px;">
            <canvas id="leadStatusChart"></canvas>
        </div>
        <div class="flex flex-wrap gap-3 mt-3 justify-center text-xs">
            @foreach($leadStatusDist as $s)
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:{{ $s['color'] }}"></span> {{ $s['label'] }} ({{ $s['value'] }})</span>
            @endforeach
        </div>
    </div>
</div>

{{-- Monitoring --}}
<div class="flex items-center gap-2 mb-4">
    <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
        <i class="bi bi-bell text-amber-600 text-sm"></i>
    </div>
    <h2 class="text-base font-bold text-slate-900 heading">Monitoring</h2>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Recent Activity Feed --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover-lift">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                    <i class="bi bi-activity text-slate-600 text-sm"></i>
                </div>
                <h3 class="font-semibold text-slate-900 heading text-sm">Recent Activity</h3>
            </div>
            <span class="text-xs text-slate-400">{{ count($recentActivity) }} entries</span>
        </div>
        <div class="activity-feed-scroll">
            @forelse($recentActivity as $activity)
            <div class="flex items-start gap-3 p-3 border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="bi bi-person text-white text-xs"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs text-slate-700 leading-relaxed">{{ $activity->description }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($activity->date)->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-400 text-sm">
                <i class="bi bi-inbox text-2xl block mb-2"></i>
                No recent activity
            </div>
            @endforelse
        </div>
    </div>

    {{-- Upcoming Deadlines --}}
    <div class="space-y-4">
        {{-- Tasks Due --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover-lift">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <i class="bi bi-alarm text-amber-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 heading text-sm">Tasks Due This Week</h3>
                        <p class="text-[10px] text-slate-400">Next 7 days</p>
                    </div>
                </div>
                <a href="{{ route('admin.tasks.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($upcomingDeadlines['tasks_due'] as $task)
                <a href="{{ route('admin.tasks.show', $task->id) }}" data-pane="show" data-pane-id="{{ $task->id }}" class="flex items-center justify-between p-3 deadline-item hover:bg-slate-50 transition-colors cursor-pointer">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-800 truncate">{{ $task->title }}</p>
                            <p class="text-[10px] text-slate-400">Due {{ \Carbon\Carbon::parse($task->duedate)->format('M d') }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-medium shrink-0">{{ \Carbon\Carbon::parse($task->duedate)->diffForHumans() }}</span>
                </a>
                @empty
                <div class="p-6 text-center text-slate-400 text-xs">
                    <i class="bi bi-check-all text-lg block mb-1"></i>
                    No tasks due this week
                </div>
                @endforelse
            </div>
        </div>

        {{-- Overdue Invoices --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover-lift">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                        <i class="bi bi-exclamation-triangle text-red-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 heading text-sm">Overdue Invoices</h3>
                        <p class="text-[10px] text-slate-400">Requires attention</p>
                    </div>
                </div>
                <a href="{{ route('admin.invoices.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($upcomingDeadlines['overdue_invoices'] as $inv)
                <a href="{{ route('admin.invoices.show', $inv->id) }}" class="flex items-center justify-between p-3 deadline-item hover:bg-slate-50 transition-colors cursor-pointer">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-2 h-2 rounded-full bg-red-400 shrink-0"></div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-800 truncate">{{ $inv->title }}</p>
                            <p class="text-[10px] text-slate-400">{{ app_format_money($inv->total) }} &middot; Due {{ \Carbon\Carbon::parse($inv->duedate)->format('M d') }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-50 text-red-700 font-medium shrink-0">{{ \Carbon\Carbon::parse($inv->duedate)->diffForHumans() }}</span>
                </a>
                @empty
                <div class="p-6 text-center text-slate-400 text-xs">
                    <i class="bi bi-check-circle text-lg block mb-1"></i>
                    No overdue invoices
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Recent Records --}}
<div class="flex items-center gap-2 mb-4">
    <div class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center">
        <i class="bi bi-journal-text text-violet-600 text-sm"></i>
    </div>
    <h2 class="text-base font-bold text-slate-900 heading">Recent Records</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Latest Projects --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover-lift">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="bi bi-kanban text-blue-600 text-xs"></i>
                </div>
                <h3 class="font-semibold text-slate-900 heading text-xs">Latest Projects</h3>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($projects as $project)
            <a href="{{ route('admin.projects.show', $project->id) }}" class="flex items-center justify-between p-3 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center shrink-0">
                        <i class="bi bi-kanban text-blue-600 text-[10px]"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-800 truncate">{{ $project->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ $project->client->company ?? 'No client' }}</p>
                    </div>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full shrink-0 font-medium {{ $project->status == 1 ? 'bg-emerald-100 text-emerald-700' : ($project->status == 2 ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ $project->status == 1 ? 'Active' : ($project->status == 2 ? 'Completed' : 'On Hold') }}
                </span>
            </a>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs">No projects</div>
            @endforelse
        </div>
    </div>

    {{-- Latest Tickets --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover-lift">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="bi bi-ticket text-purple-600 text-xs"></i>
                </div>
                <h3 class="font-semibold text-slate-900 heading text-xs">Latest Tickets</h3>
            </div>
            <a href="{{ route('admin.tickets.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($tickets as $ticket)
            <a href="{{ route('admin.tickets.show', $ticket->ticketid) }}" data-pane="show" data-pane-type="ticket" data-pane-id="{{ $ticket->ticketid }}" class="flex items-center justify-between p-3 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-6 h-6 rounded bg-purple-50 flex items-center justify-center shrink-0">
                        <i class="bi bi-ticket text-purple-600 text-[10px]"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-800 truncate">{{ $ticket->subject }}</p>
                        <p class="text-[10px] text-slate-400">{{ $ticket->statusRel->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <i class="bi bi-chevron-right text-slate-300 text-[10px] shrink-0"></i>
            </a>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs">No tickets</div>
            @endforelse
        </div>
    </div>

    {{-- Latest Tasks --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover-lift">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
                    <i class="bi bi-list-task text-amber-600 text-xs"></i>
                </div>
                <h3 class="font-semibold text-slate-900 heading text-xs">Latest Tasks</h3>
            </div>
            <a href="{{ route('admin.tasks.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($tasks as $task)
            <a href="{{ route('admin.tasks.show', $task->id) }}" data-pane="show" data-pane-id="{{ $task->id }}" class="flex items-center justify-between p-3 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-6 h-6 rounded {{ $task->status == 3 ? 'bg-emerald-50' : ($task->status == 1 ? 'bg-blue-50' : 'bg-amber-50') }} flex items-center justify-center shrink-0">
                        <i class="bi {{ $task->status == 3 ? 'bi-check-circle-fill text-emerald-600' : ($task->status == 1 ? 'bi-arrow-repeat text-blue-600' : 'bi-circle text-amber-600') }} text-[10px]"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-800 truncate">{{ $task->name ?? $task->title }}</p>
                        <p class="text-[10px] text-slate-400">{{ $task->status == 3 ? 'Completed' : ($task->status == 1 ? 'In Progress' : 'To Do') }}</p>
                    </div>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-medium shrink-0 {{ $task->status == 3 ? 'bg-emerald-100 text-emerald-700' : ($task->status == 1 ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ $task->status == 3 ? 'Done' : ($task->status == 1 ? 'Doing' : 'Todo') }}
                </span>
            </a>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs">No tasks</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Financial Records --}}
<div class="flex items-center gap-2 mb-4">
    <div class="w-7 h-7 rounded-lg bg-cyan-50 flex items-center justify-center">
        <i class="bi bi-cash-stack text-cyan-600 text-sm"></i>
    </div>
    <h2 class="text-base font-bold text-slate-900 heading">Financial Records</h2>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Latest Invoices --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover-lift">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-cyan-50 flex items-center justify-center">
                    <i class="bi bi-receipt text-cyan-600 text-sm"></i>
                </div>
                <h3 class="font-semibold text-slate-900 heading text-sm">Latest Invoices</h3>
            </div>
            <a href="{{ route('admin.invoices.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($invoices as $invoice)
            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="flex items-center justify-between p-3 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-cyan-50 flex items-center justify-center shrink-0">
                        <i class="bi bi-receipt text-cyan-600 text-xs"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-800 truncate">{{ $invoice->formatted_number ?? $invoice->number }}</p>
                        <p class="text-[10px] text-slate-400">{{ $invoice->client->company ?? 'N/A' }} &middot; {{ app_format_money($invoice->total) }}</p>
                    </div>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-medium shrink-0 {{ $invoice->status == 3 ? 'bg-emerald-100 text-emerald-700' : ($invoice->status == 1 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ $invoice->status == 3 ? 'Paid' : ($invoice->status == 1 ? 'Unpaid' : 'Other') }}
                </span>
            </a>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs">No invoices</div>
            @endforelse
        </div>
    </div>

    {{-- Top Clients by Revenue --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover-lift">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="bi bi-trophy text-blue-600 text-sm"></i>
                </div>
                <h3 class="font-semibold text-slate-900 heading text-sm">Top Clients</h3>
            </div>
            <span class="text-xs text-slate-400">By revenue</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($topClients as $index => $client)
            <a href="{{ route('admin.clients.show', $client->userid) }}" class="flex items-center justify-between p-3 {{ $index < 3 ? 'bg-gradient-to-r from-amber-50/30 to-transparent' : '' }} hover:bg-slate-50 transition-colors cursor-pointer">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 text-xs font-bold {{ $index == 0 ? 'bg-amber-100 text-amber-700' : ($index == 1 ? 'bg-slate-100 text-slate-600' : ($index == 2 ? 'bg-orange-100 text-orange-700' : 'bg-slate-50 text-slate-400')) }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-800 truncate">{{ $client->company }}</p>
                    </div>
                </div>
                <span class="text-xs font-semibold text-slate-700 tabular-nums">{{ app_format_money($client->total_revenue) }}</span>
            </a>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs">No client revenue data</div>
            @endforelse
        </div>
    </div>
</div>

@can_do('manage_settings')
<div class="flex items-center gap-2 mb-4">
    <div class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center">
        <i class="bi bi-gear text-slate-600 text-sm"></i>
    </div>
    <h2 class="text-base font-bold text-slate-900 heading">System</h2>
</div>
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 text-white hover-lift mb-8">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                    <i class="bi bi-gear text-white text-sm"></i>
                </div>
                <h3 class="text-lg font-semibold heading">System Administration</h3>
            </div>
            <p class="text-blue-100 text-sm">Manage settings, roles, permissions, and more.</p>
        </div>
        <a href="/admin/settings" class="px-4 py-2 bg-white text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors shrink-0">Settings</a>
    </div>
</div>
@endcan_do
@endsection

@push('scripts')
<script>
function initDashboardCharts() {
    const revenueCtx = document.getElementById('revenueChart');
    if (!revenueCtx) return;
    new Chart(revenueCtx.getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($revenueChart, 'month')) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode(array_column($revenueChart, 'revenue')) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                borderWidth: 2.5,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 9 }, color: '#94a3b8', maxTicksLimit: 6 } },
                y: { grid: { color: '#f1f5f9', drawBorder: false }, ticks: { font: { size: 9 }, color: '#94a3b8', callback: v => Number(v).toLocaleString() } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });

    new Chart(document.getElementById('expenseChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($expenseChart, 'month')) !!},
            datasets: [
                { label: 'Revenue', data: {!! json_encode(array_column($revenueChart, 'revenue')) !!}, backgroundColor: 'rgba(16,185,129,0.7)', borderColor: 'rgba(16,185,129,1)', borderWidth: 1, borderRadius: 3 },
                { label: 'Expenses', data: {!! json_encode(array_column($expenseChart, 'expense')) !!}, backgroundColor: 'rgba(244,63,94,0.7)', borderColor: 'rgba(244,63,94,1)', borderWidth: 1, borderRadius: 3 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 9 }, color: '#94a3b8', maxTicksLimit: 6 } },
                y: { grid: { color: '#f1f5f9', drawBorder: false }, ticks: { font: { size: 9 }, color: '#94a3b8', callback: v => Number(v).toLocaleString() } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });

    new Chart(document.getElementById('taskStatusChart').getContext('2d'), {
        type: 'doughnut', data: { labels: {!! json_encode(array_column($taskStatusDist, 'label')) !!}, datasets: [{ data: {!! json_encode(array_column($taskStatusDist, 'value')) !!}, backgroundColor: {!! json_encode(array_column($taskStatusDist, 'color')) !!}, borderWidth: 0, hoverOffset: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed + ' tasks' } } } }
    });

    new Chart(document.getElementById('projectStatusChart').getContext('2d'), {
        type: 'doughnut', data: { labels: {!! json_encode(array_column($projectStatusDist, 'label')) !!}, datasets: [{ data: {!! json_encode(array_column($projectStatusDist, 'value')) !!}, backgroundColor: {!! json_encode(array_column($projectStatusDist, 'color')) !!}, borderWidth: 0, hoverOffset: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed + ' projects' } } } }
    });

    const leadData = {!! json_encode($leadStatusDist) !!};
    const leadEl = document.getElementById('leadStatusChart');
    if (leadData.length === 0) {
        leadEl.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-slate-300"><i class="bi bi-inbox text-3xl"></i></div>';
    } else {
        new Chart(leadEl.getContext('2d'), {
            type: 'doughnut', data: { labels: leadData.map(d => d.label), datasets: [{ data: leadData.map(d => d.value), backgroundColor: leadData.map(d => d.color), borderWidth: 0, hoverOffset: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed + ' leads' } } } }
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
} else {
    initDashboardCharts();
}
</script>
@endpush
