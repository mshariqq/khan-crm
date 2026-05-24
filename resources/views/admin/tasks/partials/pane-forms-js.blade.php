function updateRelTypeSelection(type) {
    document.getElementById('rel-type-label').textContent = type.charAt(0).toUpperCase() + type.slice(1);
    const dd = document.getElementById('rel-type-dropdown');
    if (dd) dd.classList.add('hidden');
    const input = document.getElementById('rel_type_input');
    if (input) input.value = type;

    const btn = document.getElementById('rel-entity-btn');
    if (btn) {
        btn.disabled = !type;
        document.getElementById('rel-entity-label').textContent = type ? 'Select...' : 'Select after choosing type';
    }
    document.getElementById('rel_id_input').value = '';

    loadRelEntityOptions(type);
    filterMilestones();
    loadProjectStaff();
}

function loadRelEntityOptions(type) {
    const container = document.getElementById('rel-entity-options');
    if (!container) return;
    let items = [];
    if (type === 'project' && typeof projectList !== 'undefined') items = projectList;
    else if (type === 'invoice' && typeof invoiceList !== 'undefined') items = invoiceList;
    else if (type === 'estimate' && typeof estimateList !== 'undefined') items = estimateList;
    else if (type === 'proposal' && typeof proposalList !== 'undefined') items = proposalList;
    else if (type === 'lead' && typeof leadList !== 'undefined') items = leadList;
    else if (type === 'ticket' && typeof ticketList !== 'undefined') items = ticketList;
    else if (type === 'client' && typeof clientList !== 'undefined') items = clientList;
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
    const dd = document.getElementById('rel-entity-dropdown');
    if (dd) dd.classList.add('hidden');
    filterMilestones();
    loadProjectStaff();
}

function toggleRelTypeDropdown() {
    const dd = document.getElementById('rel-type-dropdown');
    if (dd) dd.classList.toggle('hidden');
}

function toggleRelEntityDropdown() {
    const btn = document.getElementById('rel-entity-btn');
    if (btn && btn.disabled) return;
    const dd = document.getElementById('rel-entity-dropdown');
    if (dd) dd.classList.toggle('hidden');
}

function filterMilestones() {
    const relTypeInput = document.getElementById('rel_type_input') || document.querySelector('input[name="rel_type"]:checked');
    const relType = relTypeInput ? (relTypeInput.value || relTypeInput.value) : '';
    const relId = document.getElementById('rel_id_input').value;
    const milestoneSelect = document.querySelector('select[name="milestone"]');
    if (!milestoneSelect) return;
    milestoneSelect.querySelectorAll('option').forEach(opt => {
        if (opt.value === '') return;
        const pid = opt.getAttribute('data-project-id');
        opt.style.display = (relType === 'project' && relId) ? (pid === relId ? '' : 'none') : '';
    });
    if (relType !== 'project' || !relId) milestoneSelect.value = '';
}

function loadProjectStaff() {
    const relTypeInput = document.getElementById('rel_type_input') || document.querySelector('input[name="rel_type"]:checked');
    const relType = relTypeInput ? (relTypeInput.value || relTypeInput.value) : '';
    const relId = document.getElementById('rel_id_input').value;
    const select = document.getElementById('assignee-select');
    if (!select) return;
    let staffList = typeof allStaff !== 'undefined' ? allStaff : [];
    if (relType === 'project' && relId && typeof projectStaff !== 'undefined' && projectStaff[relId]) {
        staffList = projectStaff[relId];
    }
    select.innerHTML = staffList.length
        ? staffList.map(s => `<option value="${s.id}">${s.name}</option>`).join('')
        : '<option disabled>No staff available</option>';
}

function toggleRecurring(value) {
    const opts = document.getElementById('recurring-options');
    if (opts) opts.classList.toggle('hidden', value !== '1');
}

document.addEventListener('click', function(e) {
    ['rel-type-dropdown', 'rel-entity-dropdown'].forEach(id => {
        const dd = document.getElementById(id);
        if (!dd || dd.classList.contains('hidden')) return;
        const btn = dd.previousElementSibling;
        if (!dd.contains(e.target) && (!btn || !btn.contains(e.target))) {
            dd.classList.add('hidden');
        }
    });
});
