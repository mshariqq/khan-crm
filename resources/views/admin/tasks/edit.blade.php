@extends('layouts.admin')

@section('breadcrumb', 'Edit Task')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-editor { font-size: 14px; padding: 15px; min-height: 200px; }
    .ql-toolbar { border: 1px solid #e2e8f0 !important; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background: #f8fafc; }
    .ql-container { border: 1px solid #e2e8f0 !important; border-top: none !important; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
</style>
@endpush

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Task</h1>
            <p class="text-slate-500 text-sm">Update the details of {{ $task->name }}.</p>
        </div>
        <a href="{{ route('admin.tasks.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Task Details</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Task Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $task->name) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Priority <span class="text-red-500">*</span></label>
                            <select name="priority" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                                <option value="1" {{ old('priority', $task->priority) == '1' ? 'selected' : '' }}>Low</option>
                                <option value="2" {{ old('priority', $task->priority) == '2' ? 'selected' : '' }}>Medium</option>
                                <option value="3" {{ old('priority', $task->priority) == '3' ? 'selected' : '' }}>High</option>
                                <option value="4" {{ old('priority', $task->priority) == '4' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Status <span class="text-red-500">*</span></label>
                            <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                                <option value="0" {{ old('status', $task->status) == '0' ? 'selected' : '' }}>Backlog</option>
                                <option value="1" {{ old('status', $task->status) == '1' ? 'selected' : '' }}>To Do</option>
                                <option value="2" {{ old('status', $task->status) == '2' ? 'selected' : '' }}>In Progress</option>
                                <option value="3" {{ old('status', $task->status) == '3' ? 'selected' : '' }}>Done</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Start Date</label>
                            <input type="date" name="startdate" value="{{ old('startdate', $task->startdate) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Due Date</label>
                            <input type="date" name="duedate" value="{{ old('duedate', $task->duedate) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Date Finished</label>
                            <input type="date" name="datefinished" value="{{ old('datefinished', $task->datefinished) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Visible to Client</label>
                            <select name="visible_to_client" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="0" {{ old('visible_to_client', $task->visible_to_client) == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('visible_to_client', $task->visible_to_client) == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Associations</h3>
                    
<div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Relate To</label>
                        <div class="relative">
                            <button type="button" onclick="toggleRelTypeDropdown()" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-left flex items-center justify-between hover:border-blue-400 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <span id="rel-type-label" class="text-sm text-slate-600">
                                    @if($task->rel_type == 'project') Project
                                    @elseif($task->rel_type == 'invoice') Invoice
                                    @elseif($task->rel_type == 'estimate') Estimate
                                    @elseif($task->rel_type == 'proposal') Proposal
                                    @else None @endif
                                </span>
                                <i class="bi bi-chevron-down text-slate-400"></i>
                            </button>
                            <div id="rel-type-dropdown" class="hidden absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg">
                                <div class="p-2">
                                    @foreach(['project' => 'Project', 'invoice' => 'Invoice', 'estimate' => 'Estimate', 'proposal' => 'Proposal', 'lead' => 'Lead', 'ticket' => 'Ticket', 'client' => 'Client'] as $key => $label)
                                    <label class="flex items-center gap-2 p-2 rounded hover:bg-slate-50 cursor-pointer">
                                        <input type="radio" name="rel_type" value="{{ $key }}" class="w-4 h-4 border-slate-300 text-blue-600 focus:ring-blue-500" {{ $task->rel_type == $key ? 'checked' : '' }} onchange="updateRelTypeSelection(this.value)">
                                        <span class="text-sm text-slate-700">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Related Entity</label>
                        <div class="relative">
                            <button type="button" onclick="toggleRelEntityDropdown()" id="rel-entity-btn" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-left flex items-center justify-between hover:border-blue-400 focus:ring-2 focus:ring-blue-500 outline-none transition-all" {{ $task->rel_type ? '' : 'disabled' }}>
                                <span id="rel-entity-label" class="text-sm text-slate-600">
                                    @if($task->rel_type == 'project')
                                        {{ $task->rel_id && $projects->find($task->rel_id) ? $projects->find($task->rel_id)->name : 'Select...' }}
                                    @elseif($task->rel_type == 'invoice')
                                        {{ $task->rel_id && $invoices->find($task->rel_id) ? '#' . $invoices->find($task->rel_id)->number : 'Select...' }}
                                    @elseif($task->rel_type == 'estimate')
                                        {{ $task->rel_id && $estimates->find($task->rel_id) ? '#' . $estimates->find($task->rel_id)->number : 'Select...' }}
                                    @elseif($task->rel_type == 'proposal')
                                        {{ $task->rel_id && $proposals->find($task->rel_id) ? '#' . $proposals->find($task->rel_id)->id : 'Select...' }}
                                    @elseif($task->rel_type == 'lead')
                                        {{ $task->rel_id && $leads->find($task->rel_id) ? $leads->find($task->rel_id)->name : 'Select...' }}
                                    @elseif($task->rel_type == 'ticket')
                                        {{ $task->rel_id && $tickets->find($task->rel_id) ? $tickets->find($task->rel_id)->subject : 'Select...' }}
                                    @elseif($task->rel_type == 'client')
                                        {{ $task->rel_id && $clients->find($task->rel_id) ? $clients->find($task->rel_id)->company : 'Select...' }}
                                    @else
                                        Select after choosing type
                                    @endif
                                </span>
                                <i class="bi bi-chevron-down text-slate-400"></i>
                            </button>
                            <input type="hidden" name="rel_id" id="rel_id_input" value="{{ $task->rel_id }}">
                            <div id="rel-entity-dropdown" class="hidden absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <div id="rel-entity-options" class="p-2">
                                    <p class="text-xs text-slate-400 p-2">Select a type first</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Milestone</label>
                            <select name="milestone" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" onchange="filterMilestones()">
                                <option value="">No Milestone</option>
                                @foreach($milestones as $m)
                                    <option value="{{ $m->id }}" data-project-id="{{ $m->project_id }}" {{ old('milestone', $task->milestone) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Public</label>
                            <select name="is_public" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="0" {{ old('is_public', $task->is_public) == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_public', $task->is_public) == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Assigned To</label>
                        <div class="relative">
                            <button type="button" onclick="toggleAssigneeDropdown()" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-left flex items-center justify-between hover:border-blue-400 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <span id="assignee-label" class="text-sm text-slate-600">
                                    @if($task->assignedStaff->count() > 0)
                                        {{ $task->assignedStaff->pluck('firstname')->implode(', ') }}
                                    @else
                                        Select team members...
                                    @endif
                                </span>
                                <i class="bi bi-chevron-down text-slate-400"></i>
                            </button>
                            <div id="assignee-dropdown" class="hidden absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <div id="assignee-options" class="p-2">
                                    <p class="text-xs text-slate-400 p-2">Select a project first to see team members</p>
                                </div>
                            </div>
                            <input type="hidden" name="assigned_staff" id="assigned_staff_input" value="{{ $task->assignedStaff->pluck('staffid')->implode(',') }}">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Billing & Recurring</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Billable</label>
                            <select name="billable" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="0" {{ old('billable', $task->billable) == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('billable', $task->billable) == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Billed</label>
                            <select name="billed" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="0" {{ old('billed', $task->billed) == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('billed', $task->billed) == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Hourly Rate</label>
                        <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $task->hourly_rate ?? 0) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Recurring</label>
                        <select name="recurring" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" onchange="toggleRecurring(this.value)">
                            <option value="0" {{ old('recurring', $task->recurring) == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('recurring', $task->recurring) == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>

                    <div id="recurring-options" class="space-y-1 {{ old('recurring', $task->recurring) == '1' ? '' : 'hidden' }}">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-600">Repeat Every</label>
                                <select name="repeat_every" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                    <option value="1" {{ old('repeat_every', $task->repeat_every) == '1' ? 'selected' : '' }}>1</option>
                                    <option value="2" {{ old('repeat_every', $task->repeat_every) == '2' ? 'selected' : '' }}>2</option>
                                    <option value="3" {{ old('repeat_every', $task->repeat_every) == '3' ? 'selected' : '' }}>3</option>
                                    <option value="4" {{ old('repeat_every', $task->repeat_every) == '4' ? 'selected' : '' }}>4</option>
                                    <option value="5" {{ old('repeat_every', $task->repeat_every) == '5' ? 'selected' : '' }}>5</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-600">Recurring Type</label>
                                <select name="recurring_type" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                    <option value="day" {{ old('recurring_type', $task->recurring_type) == 'day' ? 'selected' : '' }}>Day</option>
                                    <option value="week" {{ old('recurring_type', $task->recurring_type) == 'week' ? 'selected' : '' }}>Week</option>
                                    <option value="month" {{ old('recurring_type', $task->recurring_type) == 'month' ? 'selected' : '' }}>Month</option>
                                    <option value="year" {{ old('recurring_type', $task->recurring_type) == 'year' ? 'selected' : '' }}>Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-600">Cycles (0=Infinite)</label>
                                <input type="number" name="cycles" value="{{ old('cycles', $task->cycles ?? 0) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0">
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-600">Total Cycles</label>
                                <input type="number" name="total_cycles" value="{{ old('total_cycles', $task->total_cycles ?? 0) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Description</label>
                <div id="editor" class="mt-1">{!! old('description', $task->description) !!}</div>
                <input type="hidden" name="description" id="description">
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Custom Recurring Note</label>
                <textarea name="custom_recurring" rows="2" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Custom recurring details...">{{ old('custom_recurring', $task->custom_recurring) }}</textarea>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.tasks.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Task</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Task details and requirements...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'clean']
                ]
            }
        });

        const form = document.querySelector('form');
        const descriptionInput = document.querySelector('#description');

        form.onsubmit = function() {
            descriptionInput.value = quill.root.innerHTML;
        };
    });
</script>
<script>
const projectList = @json($projects->map(fn($p) => ['id' => $p->id, 'name' => $p->name]));
const invoiceList = @json($invoices->map(fn($i) => ['id' => $i->id, 'label' => '#' . $i->number]));
const estimateList = @json($estimates->map(fn($e) => ['id' => $e->id, 'label' => '#' . $e->number]));
const proposalList = @json($proposals->map(fn($p) => ['id' => $p->id, 'label' => '#' . $p->id]));
const leadList = @json($leads->map(fn($l) => ['id' => $l->id, 'name' => $l->name]));
const ticketList = @json($tickets->map(fn($t) => ['id' => $t->ticketid, 'label' => $t->subject]));
const clientList = @json($clients->map(fn($c) => ['id' => $c->userid, 'name' => $c->company]));
const projectStaff = @json($projects->mapWithKeys(function($p) { return [$p->id => $p->staff->map(fn($s) => ['id' => $s->staffid, 'name' => $s->firstname . ' ' . $s->lastname])]; }));
const assignedStaff = @json($task->assignedStaff->pluck('staffid'));
const currentRelId = {{ $task->rel_id ?? 'null' }};
const currentRelType = '{{ $task->rel_type }}';

function updateRelTypeSelection(type) {
    document.getElementById('rel-type-label').textContent = type.charAt(0).toUpperCase() + type.slice(1);
    document.getElementById('rel-type-dropdown').classList.add('hidden');
    
    const btn = document.getElementById('rel-entity-btn');
    btn.disabled = !type;
    document.getElementById('rel-entity-label').textContent = type ? 'Select...' : 'Select after choosing type';
    document.getElementById('rel_id_input').value = '';
    
    loadRelEntityOptions(type);
    filterMilestones();
    loadProjectStaff();
}

function loadRelEntityOptions(type) {
    const container = document.getElementById('rel-entity-options');
    let items = [];
    
    if (type === 'project') items = projectList;
    else if (type === 'invoice') items = invoiceList;
    else if (type === 'estimate') items = estimateList;
    else if (type === 'proposal') items = proposalList;
    else if (type === 'lead') items = leadList;
    else if (type === 'ticket') items = ticketList;
    else if (type === 'client') items = clientList;
    
    if (items.length === 0) {
        container.innerHTML = '<p class="text-xs text-slate-400 p-2">No items found</p>';
        return;
    }
    
    container.innerHTML = items.map(item => `
        <label class="flex items-center gap-2 p-2 rounded hover:bg-slate-50 cursor-pointer">
            <input type="radio" name="rel_entity" value="${item.id}" class="w-4 h-4 border-slate-300 text-blue-600 focus:ring-blue-500" onchange="updateRelEntitySelection(this)">
            <span class="text-sm text-slate-700">${item.name || item.label}</span>
        </label>
    `).join('');
}

function updateRelEntitySelection(input) {
    const label = input.nextElementSibling.textContent;
    document.getElementById('rel-entity-label').textContent = label;
    document.getElementById('rel_id_input').value = input.value;
    document.getElementById('rel-entity-dropdown').classList.add('hidden');
    filterMilestones();
    loadProjectStaff();
}

function toggleRelTypeDropdown() {
    document.getElementById('rel-type-dropdown').classList.toggle('hidden');
}

function toggleRelEntityDropdown() {
    const btn = document.getElementById('rel-entity-btn');
    if (btn.disabled) return;
    document.getElementById('rel-entity-dropdown').classList.toggle('hidden');
}

function filterMilestones() {
    const relType = document.querySelector('input[name="rel_type"]:checked')?.value || '';
    const relId = document.getElementById('rel_id_input').value;
    const milestoneSelect = document.querySelector('select[name="milestone"]');
    const options = milestoneSelect.querySelectorAll('option');

    options.forEach(opt => {
        if (opt.value === '') return;
        const projectId = opt.getAttribute('data-project-id');
        opt.style.display = (relType === 'project' && relId) ? (projectId === relId ? '' : 'none') : '';
    });

    if (relType !== 'project' || !relId) {
        milestoneSelect.value = '';
    }
}

function loadProjectStaff() {
    const relType = document.querySelector('input[name="rel_type"]:checked')?.value || '';
    const relId = document.getElementById('rel_id_input').value;
    const container = document.getElementById('assignee-options');
    const label = document.getElementById('assignee-label');
    const input = document.getElementById('assigned_staff_input');

    if (relType === 'project' && relId && projectStaff[relId]) {
        const staff = projectStaff[relId];
        container.innerHTML = staff.map(s => `
            <label class="flex items-center gap-2 p-2 rounded hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" value="${s.id}" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" ${assignedStaff.includes(s.id) ? 'checked' : ''} onchange="updateAssigneeSelection()">
                <div class="w-6 h-6 rounded-full bg-slate-400 flex items-center justify-center text-white text-xs font-semibold">${s.name.charAt(0)}</div>
                <span class="text-sm text-slate-700">${s.name}</span>
            </label>
        `).join('');
        if (staff.length === 0) {
            container.innerHTML = '<p class="text-xs text-slate-400 p-2">No staff members in this project</p>';
        }
    } else {
        container.innerHTML = '<p class="text-xs text-slate-400 p-2">Select a project first to see team members</p>';
    }
}

function updateAssigneeSelection() {
    const checkboxes = document.querySelectorAll('#assignee-options input[type="checkbox"]:checked');
    const selected = Array.from(checkboxes).map(cb => cb.value);
    const label = document.getElementById('assignee-label');
    const input = document.getElementById('assigned_staff_input');
    
    input.value = selected.join(',');
    
    if (selected.length === 0) {
        label.textContent = 'Select team members...';
    } else if (selected.length === 1) {
        const checked = document.querySelector('#assignee-options input[type="checkbox"]:checked ~ span');
        label.textContent = checked ? checked.textContent.trim() : '1 selected';
    } else {
        label.textContent = selected.length + ' selected';
    }
}

function toggleAssigneeDropdown() {
    document.getElementById('assignee-dropdown').classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const assigneeDropdown = document.getElementById('assignee-dropdown');
    const assigneeBtn = document.querySelector('button[onclick="toggleAssigneeDropdown()"]');
    if (assigneeDropdown && !assigneeDropdown.contains(e.target) && !assigneeBtn?.contains(e.target)) {
        assigneeDropdown.classList.add('hidden');
    }
    
    const relTypeDropdown = document.getElementById('rel-type-dropdown');
    const relTypeBtn = document.querySelector('button[onclick="toggleRelTypeDropdown()"]');
    if (relTypeDropdown && !relTypeDropdown.contains(e.target) && !relTypeBtn?.contains(e.target)) {
        relTypeDropdown.classList.add('hidden');
    }
    
    const relEntityDropdown = document.getElementById('rel-entity-dropdown');
    const relEntityBtn = document.getElementById('rel-entity-btn');
    if (relEntityDropdown && !relEntityDropdown.contains(e.target) && !relEntityBtn?.contains(e.target)) {
        relEntityDropdown.classList.add('hidden');
    }
});

function toggleRecurring(value) {
    const options = document.getElementById('recurring-options');
    if (value === '1') {
        options.classList.remove('hidden');
    } else {
        options.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (currentRelType) {
        loadRelEntityOptions(currentRelType);
        loadProjectStaff();
    }
});
</script>
@endsection