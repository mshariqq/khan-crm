@php use App\Models\Project; @endphp
@extends('layouts.client')

@section('title', $project->name)
@section('breadcrumb', 'Project Details')

<style>
.tab-content { display: none; }
.tab-content.active { display: block; }
.client-tab.active { background: #eff6ff; color: #2563eb; border-right: 2px solid #2563eb; }
.loading-spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #2563eb;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.projects.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $project->name }}</h1>
                <p class="text-sm text-slate-500">Project overview and progress</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @php
                $statusColor = 'bg-slate-100 text-slate-600';
                $statusLabel = 'Unknown';
                if($project->status === Project::STATUS_ACTIVE) {
                    $statusColor = 'bg-emerald-50 text-emerald-700';
                    $statusLabel = 'In Progress';
                } elseif($project->status === Project::STATUS_COMPLETED) {
                    $statusColor = 'bg-blue-50 text-blue-700';
                    $statusLabel = 'Completed';
                } elseif($project->status === Project::STATUS_ON_HOLD) {
                    $statusColor = 'bg-amber-50 text-amber-700';
                    $statusLabel = 'On Hold';
                } elseif($project->status === Project::STATUS_CANCELLED) {
                    $statusColor = 'bg-red-50 text-red-700';
                    $statusLabel = 'Cancelled';
                }
            @endphp
            <span class="px-4 py-1.5 rounded-xl text-xs font-bold {{ $statusColor }}">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-4">
        <div class="lg:w-1/5 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden sticky top-4" style="position:relative;">
                <div class="sidebar-tabs-fade" style="position:relative;">
                <div class="sidebar-tabs-container py-2" style="overflow-x:auto;overflow-y:auto;max-height:calc(100vh - 120px);-webkit-overflow-scrolling:touch;scrollbar-width:none;">
                    <button onclick="loadClientTab('overview')" class="client-tab active w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-grid"></i> Overview
                    </button>
                    <button onclick="loadClientTab('tasks')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-list-check"></i> Tasks <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->tasks->count() }}</span>
                    </button>
                    <button onclick="loadClientTab('milestones')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-flag"></i> Milestones <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->milestones->count() }}</span>
                    </button>
                    <button onclick="loadClientTab('timesheets')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-clock"></i> Timesheets
                    </button>
                    <button onclick="loadClientTab('gantt')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-bar-chart-steps"></i> Gantt
                    </button>
                    <button onclick="loadClientTab('discussions')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-chat-dots"></i> Discussions <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->discussions->count() }}</span>
                    </button>
                    <button onclick="loadClientTab('files')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-folder"></i> Files <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->files->count() }}</span>
                    </button>
                    <button onclick="loadClientTab('invoices')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-receipt"></i> Invoices <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->invoices->count() }}</span>
                    </button>
                    <button onclick="loadClientTab('estimates')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-file-earmark-text"></i> Estimates <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->estimates->count() }}</span>
                    </button>
                    <button onclick="loadClientTab('notes')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-journal-text"></i> Notes
                    </button>
                    <button onclick="loadClientTab('team')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-people"></i> Team
                    </button>
                    <button onclick="loadClientTab('activity')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-activity"></i> Activity
                    </button>
                    <button onclick="loadClientTab('feedback')" class="client-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-star"></i> Feedback
                    </button>
                </div>
                </div>
            </div>
        </div>

        <div class="lg:w-4/5 flex-1 min-w-0">
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <div id="client-tab-overview" class="tab-content active">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 mb-4">Project Overview</h3>
                                <div class="prose prose-slate max-w-none text-slate-600 text-sm">
                                    {!! nl2br(e($project->description)) !!}
                                </div>
                            </div>

                            <div class="overflow-hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-slate-800">Recent Tasks</h3>
                                    <span class="text-xs font-bold text-slate-500">{{ $project->tasks->count() }} Total Tasks</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider font-bold text-slate-400">
                                                <th class="px-6 py-3">Task Name</th>
                                                <th class="px-6 py-3">Status</th>
                                                <th class="px-6 py-3">Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @forelse($project->tasks->take(10) as $task)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <span class="text-sm font-bold text-slate-800">{{ $task->name }}</span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    @php
                                                        $taskStatusColor = 'bg-slate-100 text-slate-600';
                                                        $taskStatusLabel = 'Unknown';
                                                        if($task->status == 1) { $taskStatusColor = 'bg-blue-50 text-blue-600'; $taskStatusLabel = 'Not Started'; }
                                                        elseif($task->status == 2) { $taskStatusColor = 'bg-amber-50 text-amber-600'; $taskStatusLabel = 'In Progress'; }
                                                        elseif($task->status == 3) { $taskStatusColor = 'bg-emerald-50 text-emerald-600'; $taskStatusLabel = 'Completed'; }
                                                    @endphp
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ $taskStatusColor }}">
                                                        {{ $taskStatusLabel }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-xs text-slate-500">
                                                    {{ $task->duedate ? $task->duedate->format('M d, Y') : '-' }}
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-10 text-center text-slate-400 text-sm">No tasks found</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="p-6 bg-slate-50 rounded-xl">
                                <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Progress</h3>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs text-slate-500 font-medium">Completion</span>
                                    <span class="text-sm font-bold text-slate-900">{{ $project->progress }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all duration-1000 bg-blue-600" style="width: {{ $project->progress }}%"></div>
                                </div>
                                
                                <div class="mt-8 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-500">Start Date</span>
                                        <span class="text-xs font-bold text-slate-800">{{ $project->start_date ? $project->start_date->format('M d, Y') : '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-500">Deadline</span>
                                        <span class="text-xs font-bold {{ $project->deadline && $project->deadline->isPast() ? 'text-red-600' : 'text-slate-800' }}">
                                            {{ $project->deadline ? $project->deadline->format('M d, Y') : 'No deadline' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-500">Billing Type</span>
                                        <span class="text-xs font-bold text-slate-800">
                                            @if($project->billing_type == 1) Fixed Rate
                                            @elseif($project->billing_type == 2) Hourly Rate
                                            @else Project Hours @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="client-tab-tasks" class="tab-content">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-slate-800">Tasks</h3>
                            <span class="text-xs font-bold text-slate-500">{{ $project->tasks->count() }} Total Tasks</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] uppercase tracking-wider font-bold text-slate-400">
                                        <th class="px-6 py-3">Task Name</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Assignee</th>
                                        <th class="px-6 py-3">Due Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($project->tasks as $task)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-bold text-slate-800">{{ $task->name }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $taskStatusColor = 'bg-slate-100 text-slate-600';
                                                $taskStatusLabel = 'Unknown';
                                                if($task->status == 0) { $taskStatusColor = 'bg-slate-100 text-slate-600'; $taskStatusLabel = 'Backlog'; }
                                                elseif($task->status == 1) { $taskStatusColor = 'bg-blue-50 text-blue-600'; $taskStatusLabel = 'Not Started'; }
                                                elseif($task->status == 2) { $taskStatusColor = 'bg-amber-50 text-amber-600'; $taskStatusLabel = 'In Progress'; }
                                                elseif($task->status == 3) { $taskStatusColor = 'bg-emerald-50 text-emerald-600'; $taskStatusLabel = 'Completed'; }
                                            @endphp
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ $taskStatusColor }}">
                                                {{ $taskStatusLabel }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500">
                                            @if($task->assignedStaff && $task->assignedStaff->count() > 0)
                                                @foreach($task->assignedStaff as $staff)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 rounded text-[10px]">{{ $staff->firstname }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500">
                                            {{ $task->duedate ? $task->duedate->format('M d, Y') : '-' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-slate-400 text-sm">
                                            <i class="bi bi-list-check text-2xl opacity-50 mb-2"></i>
                                            <p>No tasks found</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="client-tab-milestones" class="tab-content">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-3"></div>
                            <p class="text-sm text-slate-500">Loading milestones...</p>
                        </div>
                    </div>
                </div>

                <div id="client-tab-timesheets" class="tab-content">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-3"></div>
                            <p class="text-sm text-slate-500">Loading timesheets...</p>
                        </div>
                    </div>
                </div>

                <div id="client-tab-gantt" class="tab-content">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-3"></div>
                            <p class="text-sm text-slate-500">Loading Gantt chart...</p>
                        </div>
                    </div>
                </div>

                <div id="client-tab-discussions" class="tab-content">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-3"></div>
                            <p class="text-sm text-slate-500">Loading discussions...</p>
                        </div>
                    </div>
                </div>

                <div id="client-tab-files" class="tab-content">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-3"></div>
                            <p class="text-sm text-slate-500">Loading files...</p>
                        </div>
                    </div>
                </div>

                <div id="client-tab-invoices" class="tab-content">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-3"></div>
                            <p class="text-sm text-slate-500">Loading invoices...</p>
                        </div>
                    </div>
                </div>

                <div id="client-tab-estimates" class="tab-content">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-3"></div>
                            <p class="text-sm text-slate-500">Loading estimates...</p>
                        </div>
                    </div>
                </div>

                <div id="client-tab-notes" class="tab-content">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-3"></div>
                            <p class="text-sm text-slate-500">Loading notes...</p>
                        </div>
                    </div>
                </div>

                <div id="client-tab-team" class="tab-content">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Project Team</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($project->staff as $member)
                            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                                    {{ substr($member->firstname, 0, 1) }}{{ substr($member->lastname, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $member->firstname }} {{ $member->lastname }}</p>
                                    <p class="text-xs text-slate-500">{{ $member->email ?? '' }}</p>
                                    @if($member->phonenumber)
                                    <p class="text-xs text-slate-400">{{ $member->phonenumber }}</p>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full text-center py-12 text-slate-400">
                                <i class="bi bi-people text-3xl opacity-50 mb-4"></i>
                                <p class="text-sm font-medium">No team members assigned</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div id="client-tab-activity" class="tab-content">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-3"></div>
                            <p class="text-sm text-slate-500">Loading activity...</p>
                        </div>
                    </div>
                </div>

                <div id="client-tab-feedback" class="tab-content">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Submit Feedback</h3>
                        <form id="feedbackForm" class="space-y-4 max-w-lg">
                            @csrf
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Rating</label>
                                <div class="flex gap-2" id="ratingStars">
                                    @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setRating({{ $i }})" class="w-10 h-10 rounded-full border-2 border-slate-200 text-slate-400 hover:text-amber-400 hover:border-amber-400 transition-all text-lg" data-rating="{{ $i }}">
                                        <i class="bi bi-star"></i>
                                    </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="ratingValue" value="0">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">NPS Score (0-10)</label>
                                <input type="number" name="nps_score" min="0" max="10" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Comments</label>
                                <textarea name="comments" rows="4" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Share your thoughts about this project..."></textarea>
                            </div>
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all">Submit Feedback</button>
                        </form>
                        <div id="feedbackSuccess" class="hidden mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">
                            <i class="bi bi-check-circle-fill"></i> Thank you! Your feedback has been submitted.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const projectId = {{ $project->id }};

function loadClientTab(tab) {
    document.querySelectorAll('.client-tab').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    
    const btn = document.querySelector(`[onclick="loadClientTab('${tab}')"]`);
    if (btn) btn.classList.add('active');
    
    const content = document.getElementById('client-tab-' + tab);
    if (content) content.classList.add('active');

    if (tab === 'overview' || tab === 'team' || tab === 'feedback' || tab === 'tasks') return;

    const routes = {
        milestones: '/client/projects/' + projectId + '/milestones',
        timesheets: '/client/projects/' + projectId + '/timesheets',
        gantt: '/client/projects/' + projectId + '/gantt',
        discussions: '/client/projects/' + projectId + '/discussions',
        files: '/client/projects/' + projectId + '/files',
        invoices: '/client/projects/' + projectId + '/invoices',
        estimates: '/client/projects/' + projectId + '/estimates',
        notes: '/client/projects/' + projectId + '/notes',
        activity: '/client/projects/' + projectId + '/activity',
    };

    if (routes[tab]) {
        fetch(routes[tab])
            .then(res => res.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(() => {
                content.innerHTML = '<div class="text-center py-12"><div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-3"><i class="bi bi-exclamation-triangle text-red-500 text-xl"></i></div><p class="text-sm text-red-500 mb-3">Failed to load content</p><button onclick="loadClientTab(\'' + tab + '\')" class="px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700"><i class="bi bi-arrow-clockwise"></i> Retry</button></div>';
            });
    }
}

function setRating(val) {
    document.getElementById('ratingValue').value = val;
    document.querySelectorAll('#ratingStars button').forEach((btn, i) => {
        const star = btn.querySelector('i');
        if (i < val) {
            star.className = 'bi bi-star-fill text-amber-400';
            btn.className = 'w-10 h-10 rounded-full border-2 border-amber-400 text-amber-400 text-lg';
        } else {
            star.className = 'bi bi-star text-slate-400';
            btn.className = 'w-10 h-10 rounded-full border-2 border-slate-200 text-slate-400 text-lg';
        }
    });
}

document.getElementById('feedbackForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('/client/projects/' + projectId + '/feedback', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('feedbackForm').classList.add('hidden');
            document.getElementById('feedbackSuccess').classList.remove('hidden');
        }
    })
    .catch(() => alert('Failed to submit feedback'));
});
</script>
@endsection
