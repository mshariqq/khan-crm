<div id="deleteConfirmModal" class="fixed inset-0 bg-black/50 hidden z-[60] flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6 text-center">
        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-exclamation-triangle text-red-500 text-xl"></i>
        </div>
        <h3 class="text-base font-bold text-slate-800 mb-2" id="deleteModalTitle">Confirm Delete</h3>
        <p class="text-sm text-slate-500 mb-6" id="deleteModalMessage">Are you sure you want to delete this item? This action cannot be undone.</p>
        <div class="flex justify-center gap-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-all">Cancel</button>
            <button id="deleteConfirmBtn" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all shadow-lg shadow-red-500/20">Delete</button>
        </div>
        <input type="hidden" id="deleteFormAction" value="">
        <input type="hidden" id="deleteFormMethod" value="POST">
    </div>
</div>

<script>
function confirmDelete(title, message, actionUrl, method) {
    document.getElementById('deleteModalTitle').textContent = title || 'Confirm Delete';
    document.getElementById('deleteModalMessage').textContent = message || 'Are you sure you want to delete this item?';
    document.getElementById('deleteFormAction').value = actionUrl || '';
    document.getElementById('deleteFormMethod').value = method || 'POST';
    document.getElementById('deleteConfirmModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteConfirmModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('deleteConfirmBtn')?.addEventListener('click', function() {
        const action = document.getElementById('deleteFormAction').value;
        const method = document.getElementById('deleteFormMethod').value;
        if (action) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="' + method + '">';
            document.body.appendChild(form);
            form.submit();
        }
        closeDeleteModal();
    });
});
</script>
