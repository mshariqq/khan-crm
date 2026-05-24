@extends('layouts.admin')

@section('breadcrumb', 'Lead Details')

@section('main')
<div class="p-4 space-y-4">
    {{-- Compact Header Bar --}}
    <div class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-400 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                {{ strtoupper(substr($lead->name ?? 'L', 0, 2)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $lead->name }}</h1>
                <p class="text-sm text-slate-500">{{ $lead->company ?? 'No company' }} • {{ $lead->email ?? 'No email' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($lead->lead_value)
            <div class="text-right px-4 py-2 bg-emerald-50 rounded-lg border border-emerald-200">
                <p class="text-xs text-emerald-600 font-medium">Lead Value</p>
                <p class="text-lg font-bold text-emerald-700">{{ app_format_money($lead->lead_value) }}</p>
            </div>
            @endif
            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold
                {{ optional($lead->statusRel)->id == 1 ? 'bg-emerald-100 text-emerald-700' : '' }}
                {{ optional($lead->statusRel)->id == 2 ? 'bg-amber-100 text-amber-700' : '' }}
                {{ optional($lead->statusRel)->id == 3 ? 'bg-blue-100 text-blue-700' : '' }}
                {{ !optional($lead->statusRel)->id || !in_array(optional($lead->statusRel)->id, [1,2,3]) ? 'bg-slate-100 text-slate-600' : '' }}">
                {{ optional($lead->statusRel)->name ?? 'Unknown' }}
            </span>
            <a href="{{ route('admin.leads.edit', $lead->id) }}" class="p-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-all">
                <i class="bi bi-pencil"></i>
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $lead->activityLogs()->count() }}</p>
            <p class="text-xs text-slate-500">Activities</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $lead->notes()->count() }}</p>
            <p class="text-xs text-slate-500">Notes</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $lead->reminders()->count() }}</p>
            <p class="text-xs text-slate-500">Reminders</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $lead->integrationEmails()->count() }}</p>
            <p class="text-xs text-slate-500">Emails</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        {{-- Left Column --}}
        <div class="lg:col-span-3 space-y-4">
            {{-- Tabs --}}
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="flex border-b border-slate-200 overflow-x-auto">
                    <button class="tab-btn active px-4 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600 whitespace-nowrap" data-tab="overview">Overview</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="notes">Notes ({{ $lead->notes()->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="reminders">Reminders ({{ $lead->reminders()->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="tasks">Tasks ({{ $lead->tasks()->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="proposals">Proposals ({{ $lead->proposals()->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="activities">Activities</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="emails">Emails</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="gdpr">GDPR</button>
                </div>

                <div class="p-4">
                    {{-- Overview Tab --}}
                    <div id="tab-overview" class="tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Contact Info</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Email</span>
                                        <span class="text-slate-800">{{ $lead->email ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Phone</span>
                                        <span class="text-slate-800">{{ $lead->phonenumber ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Website</span>
                                        <span class="text-slate-800">{{ $lead->website ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Address</span>
                                        <span class="text-slate-800 text-right">{{ $lead->address ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Assignment</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Source</span>
                                        <span class="text-slate-800">{{ optional($lead->sourceRel)->name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Assigned To</span>
                                        <span class="text-slate-800">
                                            @if($lead->assigned)
                                                {{ optional($lead->assignedStaff)->firstname ?? '' }} {{ optional($lead->assignedStaff)->lastname ?? '' }}
                                            @else
                                                <span class="text-slate-400">Unassigned</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Country</span>
                                        <span class="text-slate-800">{{ optional($lead->countryRel)->long_name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Created</span>
                                        <span class="text-slate-800">{{ $lead->dateadded ? \Carbon\Carbon::parse($lead->dateadded)->format('M d, Y') : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($lead->description)
                        <div class="mt-4 bg-slate-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-slate-700 mb-2">Description</h3>
                            <p class="text-sm text-slate-600">{{ $lead->description }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Notes Tab --}}
                    <div id="tab-notes" class="tab-content hidden">
                        <form action="{{ route('admin.leads.notes.store', $lead->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="description" class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Add a note..." required>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Add</button>
                            </div>
                        </form>
                        @if($lead->notes()->count() > 0)
                            <div class="space-y-2">
                                @foreach($lead->notes()->orderBy('dateadded', 'desc')->get() as $note)
                                    <div class="bg-slate-50 rounded-lg p-3 flex justify-between items-start">
                                        <div>
                                            <p class="text-sm text-slate-700">{{ $note->description }}</p>
                                            <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($note->dateadded)->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-400 text-center py-4">No notes yet</p>
                        @endif
                    </div>

                    {{-- Reminders Tab --}}
                    <div id="tab-reminders" class="tab-content hidden">
                        <form action="{{ route('admin.leads.reminders.store', $lead->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="description" class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 outline-none" placeholder="Reminder..." required>
                                <input type="date" name="date" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 outline-none" required>
                                <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm hover:bg-amber-600">Add</button>
                            </div>
                        </form>
                        @if($lead->reminders()->count() > 0)
                            <div class="space-y-2">
                                @foreach($lead->reminders()->orderBy('date', 'asc')->get() as $reminder)
                                    <div class="bg-amber-50 rounded-lg p-3 flex justify-between items-center border-l-3 border-amber-400">
                                        <div>
                                            <p class="text-sm text-slate-700">{{ $reminder->description }}</p>
                                            <p class="text-xs text-amber-600">{{ \Carbon\Carbon::parse($reminder->date)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-400 text-center py-4">No reminders set</p>
                        @endif
                    </div>

                    {{-- Tasks Tab --}}
                    <div id="tab-tasks" class="tab-content hidden">
                        @if($lead->tasks()->count() > 0)
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-slate-500">{{ $lead->tasks()->count() }} tasks</span>
                                <a href="{{ route('admin.tasks.create') }}?rel_type=lead&rel_id={{ $lead->id }}" data-pane="create" data-pane-type="task" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-all">
                                    <i class="bi bi-plus-lg mr-1"></i> New Task
                                </a>
                            </div>
                            <div class="space-y-2">
                                @foreach($lead->tasks()->orderBy('dateadded', 'desc')->get() as $task)
                                    <div class="bg-slate-50 rounded-lg p-3 flex justify-between items-center">
                                        <div class="flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full {{ $task->status == 5 ? 'bg-emerald-500' : ($task->status == 4 ? 'bg-blue-500' : 'bg-amber-500') }}"></span>
                                            <div>
                                                <a href="{{ route('admin.tasks.show', $task->id) }}" data-pane="show" data-pane-type="task" data-pane-id="{{ $task->id }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">{{ $task->name }}</a>
                                                <p class="text-xs text-slate-400">Due: {{ $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('M d, Y') : 'No due date' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-sm text-slate-400 mb-2">No tasks linked to this lead</p>
                                <a href="{{ route('admin.tasks.create') }}?rel_type=lead&rel_id={{ $lead->id }}" data-pane="create" data-pane-type="task" class="text-blue-600 text-sm hover:underline">+ Create Task</a>
                            </div>
                        @endif
                    </div>

                    {{-- Proposals Tab --}}
                    <div id="tab-proposals" class="tab-content hidden">
                        @if($lead->proposals()->count() > 0)
                            <div class="space-y-2">
                                @foreach($lead->proposals()->orderBy('datecreated', 'desc')->get() as $proposal)
                                    <div class="bg-slate-50 rounded-lg p-3 flex justify-between items-center">
                                        <div>
                                            <a href="{{ route('admin.proposals.pane.show', $proposal->id) }}" data-pane="show" data-pane-id="{{ $proposal->id }}" data-pane-type="proposal" class="text-sm font-medium text-slate-700 hover:text-blue-600">{{ $proposal->subject }}</a>
                                            <p class="text-xs text-slate-400">{{ app_format_money($proposal->total) }} • {{ \Carbon\Carbon::parse($proposal->datecreated)->format('M d, Y') }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded
                                            {{ $proposal->status == 1 ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $proposal->status == 2 ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $proposal->status == 3 ? 'bg-emerald-100 text-emerald-700' : '' }}
                                            {{ $proposal->status == 4 ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $proposal->status == 5 ? 'bg-slate-100 text-slate-700' : '' }}">
                                            {{ $proposal->status == 1 ? 'Draft' : ($proposal->status == 2 ? 'Sent' : ($proposal->status == 3 ? 'Accepted' : ($proposal->status == 4 ? 'Declined' : 'Expired'))) }}
                                        </span>
                                        <a href="{{ route('admin.proposals.pane.show', $proposal->id) }}" data-pane="show" data-pane-id="{{ $proposal->id }}" data-pane-type="proposal" class="text-blue-600 text-sm hover:underline">View</a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-sm text-slate-400 mb-2">No proposals linked to this lead</p>
                                <a href="{{ route('admin.proposals.pane.create') }}?rel_type=lead&rel_id={{ $lead->id }}" data-pane="create" data-pane-type="proposal" class="text-blue-600 text-sm hover:underline">+ Create Proposal</a>
                            </div>
                        @endif
                    </div>

                    {{-- Activities Tab --}}
                    <div id="tab-activities" class="tab-content hidden">
                        @if($lead->activityLogs()->count() > 0)
                            <div class="space-y-2">
                                @foreach($lead->activityLogs()->orderBy('date', 'desc')->get() as $activity)
                                    <div class="bg-slate-50 rounded-lg p-3 flex gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <i class="bi bi-activity text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-700">{{ $activity->description }}</p>
                                            <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($activity->date)->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-400 text-center py-4">No activities yet</p>
                        @endif
                    </div>

                    {{-- Emails Tab --}}
                    <div id="tab-emails" class="tab-content hidden">
                        @if($lead->integrationEmails()->count() > 0)
                            <div class="space-y-2">
                                @foreach($lead->integrationEmails()->orderBy('date', 'desc')->get() as $email)
                                    <div class="bg-blue-50 rounded-lg p-3 border-l-3 border-blue-400">
                                        <div class="flex justify-between items-start">
                                            <p class="text-sm font-medium text-slate-700">{{ $email->subject }}</p>
                                            <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($email->date)->format('M d, Y') }}</span>
                                        </div>
                                        <p class="text-sm text-slate-600 mt-1">{{ Str::limit($email->body, 150) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-400 text-center py-4">No emails yet</p>
                        @endif
                    </div>

                    {{-- GDPR Tab --}}
                    <div id="tab-gdpr" class="tab-content hidden">
                        <form action="{{ route('admin.leads.gdpr.store', $lead->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="flex gap-2">
                                <select name="type" class="px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                                    <option value="">Select GDPR Request</option>
                                    <option value="access">Data Access Request</option>
                                    <option value="export">Data Export</option>
                                    <option value="delete">Data Deletion</option>
                                    <option value="rectification">Data Rectification</option>
                                </select>
                                <input type="text" name="description" class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Description...">
                                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700">Submit</button>
                            </div>
                        </form>
                        @if($lead->gdprRequests()->count() > 0)
                            <div class="space-y-2">
                                @foreach($lead->gdprRequests()->orderBy('created_at', 'desc')->get() as $gdpr)
                                    <div class="bg-purple-50 rounded-lg p-3 flex justify-between items-center border-l-3 border-purple-400">
                                        <div>
                                            <p class="text-sm font-medium text-slate-700 uppercase">{{ $gdpr->request_type }}</p>
                                            <p class="text-xs text-slate-500">{{ $gdpr->description }}</p>
                                        </div>
                                        <span class="px-2 py-1 bg-{{ $gdpr->status == 'pending' ? 'amber' : 'emerald' }}-100 text-{{ $gdpr->status == 'pending' ? 'amber' : 'emerald' }}-700 text-xs rounded">{{ $gdpr->status }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-400 text-center py-4">No GDPR requests</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-4">
            {{-- Actions --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Actions</h3>
                <div class="space-y-2">
                    @if(!$lead->client_id && !$lead->lost && !$lead->junk)
                    <button onclick="document.getElementById('convertModal').showModal()" class="w-full px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 text-left flex items-center gap-2">
                        <i class="bi bi-person-check"></i> Convert to Client
                    </button>
                    @endif
                    @if(!$lead->lost)
                    <form action="{{ route('admin.leads.mark_lost', $lead->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="event.preventDefault(); confirmDelete('Confirm Action', 'Mark as lost?', this.form.action, (this.form.querySelector('[name=_method]')?.value || 'POST'));" class="w-full px-3 py-2 bg-amber-500 text-white rounded-lg text-sm hover:bg-amber-600 text-left flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> Mark as Lost
                        </button>
                    </form>
                    @endif
                    @if(!$lead->junk)
                    <form action="{{ route('admin.leads.mark_junk', $lead->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="event.preventDefault(); confirmDelete('Confirm Action', 'Mark as junk?', this.form.action, (this.form.querySelector('[name=_method]')?.value || 'POST'));" class="w-full px-3 py-2 bg-slate-500 text-white rounded-lg text-sm hover:bg-slate-600 text-left flex items-center gap-2">
                            <i class="bi bi-trash"></i> Mark as Junk
                        </button>
                    </form>
                    @endif
                    @if($lead->client_id)
                    <a href="{{ route('admin.clients.show', $lead->client_id) }}" class="block px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200 text-left flex items-center gap-2">
                        <i class="bi bi-building"></i> View Client
                    </a>
                    @endif
                    <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this lead?', this.form.action, (this.form.querySelector('[name=_method]')?.value || 'POST'));" class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm hover:bg-red-100 text-left flex items-center gap-2">
                            <i class="bi bi-trash"></i> Delete Lead
                        </button>
                    </form>
                </div>
            </div>

            {{-- Convert Modal --}}
            <dialog id="convertModal" class="modal rounded-xl p-6 bg-white shadow-xl">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Convert to Client</h3>
                <form action="{{ route('admin.leads.convert', $lead->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500">Company</label>
                        <input type="text" name="company" value="{{ $lead->company }}" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Email</label>
                        <input type="email" name="email" value="{{ $lead->email }}" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('convertModal').close()" class="flex-1 px-3 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm">Cancel</button>
                        <button type="submit" class="flex-1 px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm">Convert</button>
                    </div>
                </form>
            </dialog>

            {{-- Info --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Details</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">ID</span>
                        <span class="text-slate-800">#{{ $lead->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Added by</span>
                        <span class="text-slate-800">{{ optional($lead->creator)->firstname ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Public</span>
                        <span class="text-slate-800">{{ $lead->is_public ? 'Yes' : 'No' }}</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.leads.index') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-600 rounded-lg text-sm hover:bg-slate-200 transition-all">
                <i class="bi bi-arrow-left"></i> Back to Leads
            </a>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'border-blue-500', 'text-blue-600');
                b.classList.add('text-slate-500', 'border-transparent');
            });
            btn.classList.add('active', 'border-blue-500', 'text-blue-600');
            btn.classList.remove('text-slate-500', 'border-transparent');
            
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
        });
    });
</script>
@endsection