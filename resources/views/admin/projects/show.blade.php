@extends('layouts.admin')

@section('breadcrumb', 'Project Detail')

<style>
.tab-content { display: none; }
.tab-content.active { display: block; }
.vertical-tab.active { background: #eff6ff; color: #2563eb; border-right: 2px solid #2563eb; }
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
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.projects.index') }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition-all">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-800">{{ $project->name }}</h1>
                <p class="text-xs text-slate-400">{{ $project->client->company ?? 'No Client' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($project->status == 1)
            <span class="px-2 py-1 bg-emerald-50 text-emerald-700 rounded text-[10px] font-medium">Active</span>
            @elseif($project->status == 2)
            <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-[10px] font-medium">Completed</span>
            @else
            <span class="px-2 py-1 bg-amber-50 text-amber-700 rounded text-[10px] font-medium">On Hold</span>
            @endif
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="px-3 py-1.5 bg-amber-500 text-white text-xs font-medium rounded-lg hover:bg-amber-600">Edit</a>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-4">
        <div class="lg:w-1/5 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden sticky top-4">
                <div class="py-2">
                    <button onclick="loadTab('overview')" class="vertical-tab active w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-grid"></i> Overview
                    </button>
                    <button onclick="loadTab('tasks')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-list-check"></i> Tasks <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->tasks->count() }}</span>
                    </button>
                    <button onclick="loadTab('milestones')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-flag"></i> Milestones <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->milestones->count() }}</span>
                    </button>
                    <button onclick="loadTab('timesheets')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-clock"></i> Timesheets
                    </button>
                    <button onclick="loadTab('gantt')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-bar-chart-steps"></i> Gantt
                    </button>
                    <button onclick="loadTab('tickets')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-headset"></i> Tickets
                    </button>
                    <button onclick="loadTab('invoices')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-receipt"></i> Invoices <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->invoices->count() }}</span>
                    </button>
                    <button onclick="loadTab('estimates')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-file-earmark-text"></i> Estimates
                    </button>
                    <button onclick="loadTab('proposals')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-file-earmark-ruled"></i> Proposals
                    </button>
                    <button onclick="loadTab('expenses')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-cash-stack"></i> Expenses <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->expenses->count() }}</span>
                    </button>
                    <button onclick="loadTab('contracts')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-file-earmark-text"></i> Contracts
                    </button>
                    <button onclick="loadTab('files')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-folder"></i> Files <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->files->count() }}</span>
                    </button>
                    <button onclick="loadTab('notes')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-journal-text"></i> Notes
                    </button>
                    <button onclick="loadTab('discussions')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-chat-dots"></i> Discussions <span class="ml-auto bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ $project->discussions->count() }}</span>
                    </button>
                    <button onclick="loadTab('activity')" class="vertical-tab w-full px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 text-left flex items-center gap-2 transition-all">
                        <i class="bi bi-activity"></i> Activity
                    </button>
                </div>
            </div>
        </div>

        <div class="lg:w-4/5 flex-1 min-w-0">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 overflow-x-auto">
                <div id="tab-content-container">
                    <div id="tab-overview" class="tab-content active">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                        <div class="p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                            <div class="flex items-center justify-between mb-1">
                                <i class="bi bi-list-check text-blue-500 text-sm"></i>
                            </div>
                            <p class="text-xl font-bold text-blue-700">{{ $project->tasks->count() }}</p>
                            <p class="text-[10px] text-blue-600">Tasks</p>
                        </div>
                        <div class="p-3 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
                            <div class="flex items-center justify-between mb-1">
                                <i class="bi bi-check-circle text-emerald-500 text-sm"></i>
                            </div>
                            <p class="text-xl font-bold text-emerald-700">{{ $project->tasks->where('status', 3)->count() }}</p>
                            <p class="text-[10px] text-emerald-600">Completed</p>
                        </div>
                        <div class="p-3 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                            <div class="flex items-center justify-between mb-1">
                                <i class="bi bi-percent text-purple-500 text-sm"></i>
                            </div>
                            <p class="text-xl font-bold text-purple-700">{{ $project->progress }}%</p>
                            <p class="text-[10px] text-purple-600">Progress</p>
                        </div>
                        <div class="p-3 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl border border-amber-200">
                            <div class="flex items-center justify-between mb-1">
                                <i class="bi bi-cash text-amber-500 text-sm"></i>
                            </div>
                            <p class="text-xl font-bold text-amber-700">{{ number_format($project->project_cost ?? 0, 0) }}</p>
                            <p class="text-[10px] text-amber-600">Budget</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Client Info --}}
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                                    <i class="bi bi-building text-green-600 text-sm"></i>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-800">Client</h3>
                            </div>
                            @if($project->client)
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-full bg-slate-400 flex items-center justify-center text-white text-xs font-semibold">
                                        {{ substr($project->client->company, 0, 1) }}
                                    </span>
                                    <div>
                                        <p class="text-xs font-medium text-slate-700">{{ $project->client->company }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $project->client->email }}</p>
                                    </div>
                                </div>
                                @if($project->client->phonenumber)
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <i class="bi bi-telephone"></i>
                                    {{ $project->client->phonenumber }}
                                </div>
                                @endif
                            </div>
                            @else
                            <p class="text-xs text-slate-400 py-2">No client assigned</p>
                            @endif
                        </div>

                        {{-- Project Details --}}
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <i class="bi bi-info-circle text-blue-600 text-sm"></i>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-800">Project Details</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-2 border-b border-slate-200/50">
                                    <span class="text-xs text-slate-500">Start Date</span>
                                    <span class="text-xs font-medium text-slate-700">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M j, Y') : '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-slate-200/50">
                                    <span class="text-xs text-slate-500">Deadline</span>
                                    <span class="text-xs font-medium {{ $project->deadline && \Carbon\Carbon::parse($project->deadline)->isPast() ? 'text-red-600' : 'text-slate-700' }}">{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M j, Y') : '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-slate-200/50">
                                    <span class="text-xs text-slate-500">Billing Type</span>
                                    <span class="text-xs font-medium text-slate-700">{{ $project->billing_type == 1 ? 'Fixed Price' : ($project->billing_type == 2 ? 'Hourly Rate' : '-') }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-slate-200/50">
                                    <span class="text-xs text-slate-500">Rate/Hour</span>
                                    <span class="text-xs font-medium text-slate-700">${{ number_format($project->project_rate_per_hour ?? 0, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-xs text-slate-500">Estimated Hours</span>
                                    <span class="text-xs font-medium text-slate-700">{{ number_format($project->estimated_hours ?? 0, 1) }}h</span>
                                </div>
                            </div>
                        </div>

                        {{-- Team Members --}}
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <i class="bi bi-people text-purple-600 text-sm"></i>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-800">Team Members</h3>
                                <span class="ml-auto text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">{{ $project->staff->count() }}</span>
                            </div>
                            @if($project->staff->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($project->staff as $member)
                                <div class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg border border-slate-200">
                                    <span class="w-6 h-6 rounded-full bg-slate-400 flex items-center justify-center text-white text-[10px] font-semibold">
                                        {{ substr($member->firstname, 0, 1) }}
                                    </span>
                                    <span class="text-xs text-slate-700">{{ $member->firstname }} {{ $member->lastname }}</span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-xs text-slate-400 py-4 text-center">No team members</p>
                            @endif
                        </div>

                        {{-- Financial Summary --}}
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                    <i class="bi bi-wallet2 text-emerald-600 text-sm"></i>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-800">Financial</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-2 border-b border-slate-200/50">
                                    <span class="text-xs text-slate-500">Project Cost</span>
                                    <span class="text-xs font-bold text-slate-700">${{ number_format($project->project_cost ?? 0, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-slate-200/50">
                                    <span class="text-xs text-slate-500">Total Invoiced</span>
                                    <span class="text-xs font-medium text-slate-700">${{ number_format($project->invoices->sum('total'), 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-slate-200/50">
                                    <span class="text-xs text-slate-500">Total Expenses</span>
                                    <span class="text-xs font-medium text-slate-700">${{ number_format($project->expenses->sum('amount'), 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-xs text-slate-500">Profit Margin</span>
                                    <span class="text-xs font-medium text-emerald-600">
                                        ${{ number_format(($project->project_cost ?? 0) - $project->expenses->sum('amount'), 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Tags & Custom Fields --}}
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                                    <i class="bi bi-tags text-amber-600 text-sm"></i>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-800">Status & Tags</h3>
                            </div>
                            <div class="space-y-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($project->status == 1) bg-emerald-100 text-emerald-700
                                    @elseif($project->status == 2) bg-indigo-100 text-indigo-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    @if($project->status == 1) Active
                                    @elseif($project->status == 2) Completed
                                    @else On Hold @endif
                                </span>
                                @if($project->notify_type)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $project->notify_type == 'email' ? 'Email Notifications' : 'Dashboard' }}
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- Quick Actions --}}
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-cyan-100 flex items-center justify-center">
                                    <i class="bi bi-lightning text-cyan-600 text-sm"></i>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-800">Quick Actions</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('admin.tasks.create') }}?rel_type=project&rel_id={{ $project->id }}" class="px-3 py-2 bg-blue-600 text-white text-xs rounded-lg text-center hover:bg-blue-700">Add Task</a>
                                <a href="{{ route('admin.invoices.create') }}?project_id={{ $project->id }}" class="px-3 py-2 bg-emerald-600 text-white text-xs rounded-lg text-center hover:bg-emerald-700">New Invoice</a>
                                <a href="{{ route('admin.expenses.create') }}?project_id={{ $project->id }}" class="px-3 py-2 bg-amber-600 text-white text-xs rounded-lg text-center hover:bg-amber-700">Add Expense</a>
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="px-3 py-2 bg-slate-600 text-white text-xs rounded-lg text-center hover:bg-slate-700">Edit Project</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dynamic Tab Content --}}
                <div id="tab-dynamic" class="tab-content">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-3"></div>
                            <p class="text-sm text-slate-500">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const projectId = {{ $project->id }};

document.addEventListener('DOMContentLoaded', function() {
    const defaultTab = '{{ $default_tab ?? "overview" }}';
    loadTab(defaultTab);
});

function loadTab(tabName) {
    document.querySelectorAll('.vertical-tab').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));

    const activeTabBtn = document.querySelector(`[onclick="loadTab('${tabName}')"]`);
    if (activeTabBtn) activeTabBtn.classList.add('active');

    const tabContent = document.getElementById('tab-' + tabName);
    if (tabContent) {
        tabContent.classList.add('active');
    } else {
        const newTab = document.createElement('div');
        newTab.id = 'tab-' + tabName;
        newTab.className = 'tab-content active';
        newTab.innerHTML = '<div class="flex items-center justify-center py-20"><div class="text-center"><div class="loading-spinner mx-auto mb-3"></div><p class="text-sm text-slate-500">Loading...</p></div></div>';
        document.getElementById('tab-content-container').appendChild(newTab);
    }

    if (tabName !== 'overview') {
        fetch(`/admin/projects/${projectId}/tab/${tabName}`)
            .then(res => res.json())
            .then(data => {
                const tab = document.getElementById('tab-' + tabName);
                if (tab) {
                    // Extract and execute scripts, then set HTML
                    const scriptBlock = document.createElement('div');
                    scriptBlock.innerHTML = data.html;
                    const scripts = scriptBlock.querySelectorAll('script');
                    const htmlWithoutScripts = data.html.replace(/<script[\s\S]*?<\/script>/gi, '');
                    tab.innerHTML = htmlWithoutScripts;
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        if (oldScript.src) {
                            newScript.src = oldScript.src;
                        } else {
                            newScript.textContent = oldScript.textContent;
                        }
                        document.body.appendChild(newScript);
                        document.body.removeChild(newScript);
                    });
                }
            })
            .catch(err => {
                const tab = document.getElementById('tab-' + tabName);
                if (tab) tab.innerHTML = '<div class="text-center py-12 text-red-500">Failed to load content</div>';
            });
    }
}
</script>

<div id="noteModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between p-4 border-b border-slate-200">
            <h3 id="noteModalTitle" class="text-sm font-semibold text-slate-800">Add Note</h3>
            <button onclick="closeNoteModal()" class="p-1 text-slate-400 hover:text-slate-600 rounded">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div id="noteEditMode" class="p-4">
            <input type="hidden" id="noteId">
            <div class="mb-3">
                <label class="block text-xs font-medium text-slate-600 mb-1">Title</label>
                <input type="text" id="noteTitle" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Note title">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                <textarea id="noteContent" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="5" placeholder="Write your note..."></textarea>
            </div>
        </div>

        <div id="noteViewMode" class="hidden p-4">
            <h4 id="noteViewTitle" class="text-base font-semibold text-slate-800 mb-3"></h4>
            <div id="noteViewContent" class="text-sm text-slate-600 whitespace-pre-wrap"></div>
            <div id="noteViewMeta" class="mt-4 pt-3 border-t border-slate-100">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span id="noteAuthor"></span>
                    <span>•</span>
                    <span id="noteDate"></span>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 p-4 border-t border-slate-200">
            <button onclick="closeNoteModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Close</button>
            <button id="noteSaveBtn" onclick="saveNote()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Save</button>
        </div>
    </div>
</div>

<script>
let noteMode = 'create';

function openNoteModal() {
    noteMode = 'create';
    document.getElementById('noteModalTitle').textContent = 'Add Note';
    document.getElementById('noteEditMode').classList.remove('hidden');
    document.getElementById('noteViewMode').classList.add('hidden');
    document.getElementById('noteId').value = '';
    document.getElementById('noteTitle').value = '';
    document.getElementById('noteContent').value = '';
    document.getElementById('noteSaveBtn').classList.remove('hidden');
    document.getElementById('noteModal').classList.remove('hidden');
}

function viewNote(id, title, description, author, date) {
    noteMode = 'view';
    document.getElementById('noteModalTitle').textContent = title || 'Note';
    document.getElementById('noteEditMode').classList.add('hidden');
    document.getElementById('noteViewMode').classList.remove('hidden');
    document.getElementById('noteViewTitle').textContent = title || '';
    document.getElementById('noteViewContent').textContent = description;
    document.getElementById('noteAuthor').textContent = author;
    document.getElementById('noteDate').textContent = date;
    document.getElementById('noteSaveBtn').classList.add('hidden');
    document.getElementById('noteModal').classList.remove('hidden');
}

function editNote(id, title, description) {
    noteMode = 'edit';
    event.stopPropagation();
    document.getElementById('noteModalTitle').textContent = 'Edit Note';
    document.getElementById('noteEditMode').classList.remove('hidden');
    document.getElementById('noteViewMode').classList.add('hidden');
    document.getElementById('noteId').value = id;
    document.getElementById('noteTitle').value = title;
    document.getElementById('noteContent').value = description;
    document.getElementById('noteSaveBtn').classList.remove('hidden');
    document.getElementById('noteModal').classList.remove('hidden');
}

function closeNoteModal() {
    document.getElementById('noteModal').classList.add('hidden');
    document.getElementById('noteEditMode').classList.remove('hidden');
    document.getElementById('noteViewMode').classList.add('hidden');
    document.getElementById('noteSaveBtn').classList.remove('hidden');
}

function saveNote() {
    const id = document.getElementById('noteId').value;
    const title = document.getElementById('noteTitle').value;
    const content = document.getElementById('noteContent').value;

    if (!content.trim()) {
        alert('Please enter a note');
        return;
    }

    const projectIdVal = {{ $project->id }};
    
    let url, method, formData;
    if (noteMode === 'edit') {
        url = '/admin/projects/' + projectIdVal + '/notes/' + id;
        method = 'POST';
        formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('title', title);
        formData.append('description', content);
        formData.append('rel_type', 'project');
        formData.append('rel_id', projectIdVal);
    } else {
        url = '/admin/projects/' + projectIdVal + '/notes';
        method = 'POST';
        formData = new FormData();
        formData.append('title', title);
        formData.append('description', content);
        formData.append('rel_type', 'project');
        formData.append('rel_id', projectIdVal);
    }

    fetch(url, {
        method: method,
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status, 'URL:', url);
        return response.text();
    })
    .then(text => {
        console.log('Response:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                closeNoteModal();
                loadTab('notes');
            } else {
                alert(data.message || 'Failed to save note');
            }
        } catch (e) {
            alert('Invalid response: ' + text.substring(0, 100));
        }
    })
    .catch(error => {
        alert('Failed to save note: ' + error.message);
    });
}
</script>
@endsection