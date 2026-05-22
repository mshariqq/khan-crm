<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Edit Task</h1>
        <p class="text-slate-500 text-sm">Update the details of {{ $task->name }}.</p>
    </div>
</div>

<form action="{{ route('admin.tasks.update', $task->id) }}" method="POST" data-pane-submit="1" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
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

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Priority <span class="text-red-500">*</span></label>
                        <select name="priority" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                            <option value="1" {{ old('priority', $task->priority) == 1 ? 'selected' : '' }}>Low</option>
                            <option value="2" {{ old('priority', $task->priority) == 2 ? 'selected' : '' }}>Medium</option>
                            <option value="3" {{ old('priority', $task->priority) == 3 ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                            <option value="0" {{ old('status', $task->status) === '0' ? 'selected' : '' }}>Backlog</option>
                            <option value="1" {{ old('status', $task->status) == 1 ? 'selected' : '' }}>To Do</option>
                            <option value="2" {{ old('status', $task->status) == 2 ? 'selected' : '' }}>In Progress</option>
                            <option value="3" {{ old('status', $task->status) == 3 ? 'selected' : '' }}>Done</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Start Date</label>
                        <input type="date" name="startdate" value="{{ old('startdate', $task->startdate) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Due Date</label>
                        <input type="date" name="duedate" value="{{ old('duedate', $task->duedate) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
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

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Relate To</label>
                    <div class="relative">
                        <input type="hidden" name="rel_type" id="rel_type_input" value="{{ $task->rel_type }}">
                        <input type="hidden" name="rel_id" id="rel_id_input" value="{{ $task->rel_id }}">
                        <button type="button" onclick="toggleRelTypeDropdown()" class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-white text-left text-sm text-slate-700 flex items-center justify-between gap-2 hover:border-slate-300 transition-all">
                            <span id="rel-type-label">{{ $task->rel_type ? ucfirst($task->rel_type) : 'Select type...' }}</span>
                            <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                        </button>
                        <div id="rel-type-dropdown" class="hidden absolute z-10 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden">
                            <div class="py-1">
                                <button type="button" onclick="updateRelTypeSelection('project')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-2"><i class="bi bi-folder text-blue-500"></i> Project</button>
                                <button type="button" onclick="updateRelTypeSelection('invoice')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-2"><i class="bi bi-receipt text-emerald-500"></i> Invoice</button>
                                <button type="button" onclick="updateRelTypeSelection('estimate')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-2"><i class="bi bi-file-text text-amber-500"></i> Estimate</button>
                                <button type="button" onclick="updateRelTypeSelection('proposal')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-2"><i class="bi bi-file-earmark-text text-purple-500"></i> Proposal</button>
                                <button type="button" onclick="updateRelTypeSelection('lead')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-2"><i class="bi bi-person-badge text-yellow-500"></i> Lead</button>
                                <button type="button" onclick="updateRelTypeSelection('ticket')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-2"><i class="bi bi-ticket text-orange-500"></i> Ticket</button>
                                <button type="button" onclick="updateRelTypeSelection('client')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-2"><i class="bi bi-building text-slate-500"></i> Client</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Related Entity</label>
                    <div class="relative">
                        <button type="button" id="rel-entity-btn" disabled onclick="toggleRelEntityDropdown()" class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-white text-left text-sm text-slate-400 flex items-center justify-between gap-2 hover:border-slate-300 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="rel-entity-label">Select...</span>
                            <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                        </button>
                        <div id="rel-entity-dropdown" class="hidden absolute z-10 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden max-h-60 overflow-y-auto">
                            <div id="rel-entity-options" class="py-1"></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Milestone</label>
                        <select name="milestone" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">No milestone</option>
                            @foreach($milestones as $ms)
                            <option value="{{ $ms->id }}" data-project-id="{{ $ms->project_id }}" {{ $task->milestone == $ms->id ? 'selected' : '' }}>{{ $ms->name }}</option>
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
                        <button type="button" onclick="toggleAssigneeDropdown()" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-left text-sm text-slate-600 flex items-center justify-between gap-2 hover:border-blue-400 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <span id="assignee-label">{{ $task->assignedStaff->count() > 0 ? $task->assignedStaff->count() . ' selected' : 'Select team members...' }}</span>
                            <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                        </button>
                        <input type="hidden" name="assigned_staff" id="assigned_staff_input" value="{{ $task->assignedStaff->pluck('staffid')->join(',') }}">
                        <div id="assignee-dropdown" class="hidden absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <div id="assignee-options" class="p-2">
                                <p class="text-xs text-slate-400 p-2">Select a project first to see team members</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Billing & Recurring</h3>

                <div class="grid grid-cols-2 gap-3">
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
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">$</span>
                        <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $task->hourly_rate) }}" class="w-full pl-8 pr-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Recurring</label>
                    <select name="recurring" onchange="toggleRecurring(this.value)" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <option value="0" {{ $task->recurring ? '' : 'selected' }}>No</option>
                        <option value="1" {{ $task->recurring ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>

                <div id="recurring-options" class="{{ $task->recurring ? '' : 'hidden' }} space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Repeat Every</label>
                            <select name="repeat_every" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="1" {{ old('repeat_every', $task->repeat_every ?? 1) == 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ old('repeat_every', $task->repeat_every ?? 1) == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ old('repeat_every', $task->repeat_every ?? 1) == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ old('repeat_every', $task->repeat_every ?? 1) == 4 ? 'selected' : '' }}>4</option>
                                <option value="5" {{ old('repeat_every', $task->repeat_every ?? 1) == 5 ? 'selected' : '' }}>5</option>
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
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Cycles (0=Infinite)</label>
                            <input type="number" name="cycles" value="{{ old('cycles', $task->cycles ?? 0) }}" min="0" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Total Cycles</label>
                            <input type="number" name="total_cycles" value="{{ old('total_cycles', $task->total_cycles ?? 0) }}" min="0" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0">
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
        <button type="button" onclick="parent.closeTaskPane()" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</button>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Task</button>
    </div>
</form>

<script>
const projectList = @json($projects->map(fn($p) => ['id' => $p->id, 'name' => $p->name]));
const invoiceList = @json($invoices->map(fn($i) => ['id' => $i->id, 'label' => '#' . $i->number]));
const estimateList = @json($estimates->map(fn($e) => ['id' => $e->id, 'label' => '#' . $e->number]));
const proposalList = @json($proposals->map(fn($p) => ['id' => $p->id, 'label' => '#' . $p->id]));
const leadList = @json($leads->map(fn($l) => ['id' => $l->id, 'name' => $l->name]));
const ticketList = @json($tickets->map(fn($t) => ['id' => $t->ticketid, 'label' => $t->subject]));
const clientList = @json($clients->map(fn($c) => ['id' => $c->userid, 'name' => $c->company]));
const projectStaff = @json($projects->mapWithKeys(function($p) { return [$p->id => $p->staff->map(fn($s) => ['id' => $s->staffid, 'name' => $s->firstname . ' ' . $s->lastname])]; }));

var paneEditQuill = null;
if (document.getElementById('editor')) {
    if (paneEditQuill) paneEditQuill.destroy();
    paneEditQuill = new Quill('#editor', {
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
}

document.querySelector('form[data-pane-submit]')?.addEventListener('submit', function(e) {
    document.getElementById('description').value = paneEditQuill.root.innerHTML;
});

const assignedStaff = @json($task->assignedStaff->pluck('staffid'));

function updateRelTypeSelection(type) {
    document.getElementById('rel-type-label').textContent = type.charAt(0).toUpperCase() + type.slice(1);
    document.getElementById('rel-type-dropdown').classList.add('hidden');
    document.getElementById('rel_type_input').value = type;
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
    container.innerHTML = items.length
        ? items.map(item => `
            <label class="flex items-center gap-2 p-2 rounded hover:bg-slate-50 cursor-pointer">
                <input type="radio" name="rel_entity" value="${item.id}" class="w-4 h-4 border-slate-300 text-blue-600 focus:ring-blue-500" onchange="updateRelEntitySelection(this)">
                <span class="text-sm text-slate-700">${item.name || item.label}</span>
            </label>`).join('')
        : '<p class="text-xs text-slate-400 p-2">No items found</p>';
}

function updateRelEntitySelection(input) {
    document.getElementById('rel-entity-label').textContent = input.nextElementSibling.textContent;
    document.getElementById('rel_id_input').value = input.value;
    document.getElementById('rel-entity-dropdown').classList.add('hidden');
    filterMilestones();
    loadProjectStaff();
}

function toggleRelTypeDropdown() { document.getElementById('rel-type-dropdown').classList.toggle('hidden'); }

function toggleRelEntityDropdown() {
    const btn = document.getElementById('rel-entity-btn');
    if (btn.disabled) return;
    document.getElementById('rel-entity-dropdown').classList.toggle('hidden');
}

function filterMilestones() {
    const relType = document.getElementById('rel_type_input').value;
    const relId = document.getElementById('rel_id_input').value;
    const sel = document.querySelector('select[name="milestone"]');
    sel.querySelectorAll('option').forEach(opt => {
        if (opt.value === '') return;
        const pid = opt.getAttribute('data-project-id');
        opt.style.display = (relType === 'project' && relId) ? (pid === relId ? '' : 'none') : '';
    });
}

function loadProjectStaff() {
    const relType = document.getElementById('rel_type_input').value;
    const relId = document.getElementById('rel_id_input').value;
    const container = document.getElementById('assignee-options');
    const label = document.getElementById('assignee-label');
    const input = document.getElementById('assigned_staff_input');
    const currentSelected = input.value ? input.value.split(',').map(Number) : [];
    if (relType === 'project' && relId && projectStaff[relId]) {
        const staff = projectStaff[relId];
        container.innerHTML = staff.map(s => `
            <label class="flex items-center gap-2 p-2 rounded hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" value="${s.id}" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" onchange="updateAssigneeSelection()" ${currentSelected.includes(s.id) ? 'checked' : ''}>
                <div class="w-6 h-6 rounded-full bg-slate-400 flex items-center justify-center text-white text-xs font-semibold">${s.name.charAt(0)}</div>
                <span class="text-sm text-slate-700">${s.name}</span>
            </label>`).join('');
        if (staff.length === 0) container.innerHTML = '<p class="text-xs text-slate-400 p-2">No staff members in this project</p>';
    } else {
        container.innerHTML = '<p class="text-xs text-slate-400 p-2">Select a project first to see team members</p>';
    }
    label.textContent = 'Select team members...';
}

function updateAssigneeSelection() {
    const checkboxes = document.querySelectorAll('#assignee-options input[type="checkbox"]:checked');
    const selected = Array.from(checkboxes).map(cb => cb.value);
    const label = document.getElementById('assignee-label');
    document.getElementById('assigned_staff_input').value = selected.join(',');
    label.textContent = !selected.length ? 'Select team members...'
        : selected.length === 1
            ? (document.querySelector('#assignee-options input[type="checkbox"]:checked ~ span')?.textContent.trim() || '1 selected')
            : selected.length + ' selected';
}

function toggleAssigneeDropdown() { document.getElementById('assignee-dropdown').classList.toggle('hidden'); }

document.addEventListener('click', function(e) {
    ['assignee-dropdown', 'rel-type-dropdown', 'rel-entity-dropdown'].forEach(id => {
        const dd = document.getElementById(id);
        if (dd && !dd.contains(e.target)) {
            const btn = document.querySelector(`button[onclick^="toggle"]`);
            if (!btn?.contains(e.target)) dd.classList.add('hidden');
        }
    });
});

function toggleRecurring(value) {
    document.getElementById('recurring-options').classList.toggle('hidden', value !== '1');
}

(function() {
    const relType = '{{ $task->rel_type }}';
    const relId = '{{ $task->rel_id }}';
    if (relType && relId) {
        loadRelEntityOptions(relType);
        loadProjectStaff();
        setTimeout(() => {
            const radio = document.querySelector('#rel-entity-options input[value="' + relId + '"]');
            if (radio) {
                radio.checked = true;
                document.getElementById('rel-entity-label').textContent = radio.nextElementSibling.textContent;
                document.getElementById('rel-entity-btn').disabled = false;
                filterMilestones();
            }
        }, 50);
    }
})();
</script>
