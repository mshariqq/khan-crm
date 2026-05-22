<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
.ql-editor { font-size: 14px; padding: 15px; min-height: 200px; }
.ql-toolbar { border: 1px solid #e2e8f0 !important; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background: #f8fafc; }
.ql-container { border: 1px solid #e2e8f0 !important; border-top: none !important; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
</style>
<div class="p-6 space-y-6">
    @yield('pane-content')
</div>
<script>
function formatStatusColor(status) {
    if (!status) return 'bg-gray-100 text-gray-800';
    status = String(status).toLowerCase().trim();
    switch (status) {
        case 'paid': case 'completed': case 'active': case 'accepted': case 'success':
            return 'bg-emerald-100 text-emerald-800';
        case 'unpaid': case 'pending': case 'waiting': case 'open':
            return 'bg-amber-100 text-amber-800';
        case 'overdue': case 'expired': case 'cancelled': case 'rejected': case 'failed':
            return 'bg-red-100 text-red-800';
        case 'draft': case 'sent': case 'in progress': case 'in_progress':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}
function showToast(message, type = 'success') {
    const colors = { success: 'bg-emerald-500', error: 'bg-red-500', warning: 'bg-amber-500', info: 'bg-blue-500' };
    const icons = { success: 'check-circle', error: 'x-circle', warning: 'exclamation-triangle', info: 'info-circle' };
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-[100] px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-y-[-20px] opacity-0 ${colors[type] || colors.success}`;
    toast.innerHTML = `<div class="flex items-center gap-2"><i class="bi bi-${icons[type] || icons.success}"></i><span>${message}</span></div>`;
    document.body.appendChild(toast);
    requestAnimationFrame(() => toast.classList.remove('translate-y-[-20px]', 'opacity-0'));
    setTimeout(() => {
        toast.classList.add('translate-y-[-20px]', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>
@stack('pane-scripts')
