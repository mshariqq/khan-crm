@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @keyframes slideDown { from { opacity:0; transform: translateY(-20px); } to { opacity:1; transform: translateY(0); } }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
    </style>
    
    @include('partials.admin.sidebar')
    @include('partials.admin.navbar')
    <!-- Overlay for desktop sidebar toggle -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/30 hidden z-40"></div>

    @if(session('success'))
    <div id="globalFlash" class="fixed top-20 right-6 z-[60] max-w-md animate-slide-down">
        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-lg flex items-start gap-3">
            <i class="bi bi-check-circle-fill text-green-500 text-lg mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-600"><i class="bi bi-x-lg text-xs"></i></button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div id="globalFlash" class="fixed top-20 right-6 z-[60] max-w-md animate-slide-down">
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-lg flex items-start gap-3">
            <i class="bi bi-x-circle-fill text-red-500 text-lg mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600"><i class="bi bi-x-lg text-xs"></i></button>
        </div>
    </div>
    @endif
    @if(session('warning'))
    <div id="globalFlash" class="fixed top-20 right-6 z-[60] max-w-md animate-slide-down">
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 shadow-lg flex items-start gap-3">
            <i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-amber-800">{{ session('warning') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-amber-400 hover:text-amber-600"><i class="bi bi-x-lg text-xs"></i></button>
        </div>
    </div>
    @endif
    @if(session('info'))
    <div id="globalFlash" class="fixed top-20 right-6 z-[60] max-w-md animate-slide-down">
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 shadow-lg flex items-start gap-3">
            <i class="bi bi-info-circle-fill text-blue-500 text-lg mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-blue-400 hover:text-blue-600"><i class="bi bi-x-lg text-xs"></i></button>
        </div>
    </div>
    @endif

    <main id="mainContent" class="ml-0 md:ml-56 pt-16 px-4 md:px-6 transition-all duration-300 min-h-screen">
        @yield('main')
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-sm text-slate-500 ml-0 md:ml-56">
        <p>&copy; {{ date('Y') }} {{ $global_settings['companyname'] ?? 'CRM' }}. {{ $global_settings['copyright_text'] ?? 'All rights reserved' }}</p>
    </footer>

    {{-- Task Pane --}}
    <div id="taskPane" class="fixed inset-0 z-[70] hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/40" onclick="closeTaskPane()"></div>
        <div class="absolute right-0 top-0 h-full w-full max-w-[78%] bg-white shadow-2xl transform transition-transform duration-300 flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 flex-shrink-0">
                <h3 class="text-lg font-semibold text-slate-800" id="taskPaneTitle">Task</h3>
                <button onclick="closeTaskPane()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-all">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div id="taskPaneContent" class="flex-1 overflow-y-auto p-6 space-y-6"></div>
        </div>
    </div>

    <script>
  document.addEventListener('DOMContentLoaded', function() {
    const navSidebarToggle = document.getElementById('navSidebarToggle');

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      sidebar?.classList.toggle('-translate-x-full');
      overlay?.classList.toggle('hidden');
    }

    navSidebarToggle?.addEventListener('click', toggleSidebar);
  });

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

    const globalFlash = document.getElementById('globalFlash');
    if (globalFlash) {
        setTimeout(() => {
            globalFlash.style.transition = 'opacity 0.5s ease';
            globalFlash.style.opacity = '0';
            setTimeout(() => globalFlash.remove(), 500);
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
        @if(session('error')) showToast('{{ session('error') }}', 'error'); @endif
        @if(session('warning')) showToast('{{ session('warning') }}', 'warning'); @endif
        @if(session('info')) showToast('{{ session('info') }}', 'info'); @endif
});

    function openTaskPane(url, title) {
        const pane = document.getElementById('taskPane');
        const paneTitle = document.getElementById('taskPaneTitle');
        if (title) paneTitle.textContent = title;
        const content = document.getElementById('taskPaneContent');
        content.innerHTML = '<div class="flex items-center justify-center h-full"><div class="text-slate-400"><i class="bi bi-arrow-repeat text-4xl animate-spin block mb-2"></i><p class="text-sm">Loading...</p></div></div>';
        pane.classList.remove('hidden');
        pane.querySelector('div:last-child').style.transform = 'translateX(0)';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(r => { if (!r.ok) throw new Error('Failed to load'); return r.text(); })
            .then(html => {
                content.innerHTML = html;
                content.querySelectorAll('script').forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.textContent = oldScript.textContent;
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
                initPaneForms();
            })
            .catch(() => {
                content.innerHTML = '<div class="flex items-center justify-center h-full text-red-500"><p>Failed to load content. Please try again.</p></div>';
            });
    }

    function openTicketPane(url) { openTaskPane(url, 'Ticket'); }
    function openMilestonePane(url) { openTaskPane(url, 'Milestone'); }

    function closeTaskPane() {
        const pane = document.getElementById('taskPane');
        pane.querySelector('div:last-child').style.transform = 'translateX(100%)';
        setTimeout(() => { pane.classList.add('hidden'); document.getElementById('taskPaneContent').innerHTML = ''; }, 300);
    }

    function initPaneForms() {
        document.querySelectorAll('#taskPaneContent form[data-pane-submit]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Saving...';

                fetch(form.action, {
                    method: form.method,
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json().then(data => ({ status: r.status, data })))
                .then(({ status, data }) => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    if (data.success) {
                        showToast(data.message || 'Saved successfully!', 'success');
                        closeTaskPane();
                        const refreshUrl = form.getAttribute('data-pane-refresh');
                        if (refreshUrl) {
                            setTimeout(() => openTaskPane(refreshUrl, 'Task'), 350);
                        }
                    } else {
                        const msg = data.message || data.error || 'An error occurred';
                        showToast(msg, 'error');
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    showToast('Network error. Please try again.', 'error');
                });
            });
        });
    }

    document.addEventListener('click', function(e) {
        const link = e.target.closest('[data-pane]');
        if (!link) return;
        e.preventDefault();
        const action = link.getAttribute('data-pane');
        const type = link.getAttribute('data-pane-type') || 'task';
        const id = link.getAttribute('data-pane-id');
        let url = '';
        if (action === 'create') {
            const params = new URLSearchParams(link.search);
            if (type === 'task') {
                url = '{{ route('admin.tasks.pane.create') }}' + (params.toString() ? '?' + params.toString() : '');
            } else if (type === 'ticket') {
                url = '{{ route('admin.tickets.pane.create') }}' + (params.toString() ? '?' + params.toString() : '');
            } else if (type === 'milestone') {
                url = '{{ route('admin.milestones.pane.create') }}' + (params.toString() ? '?' + params.toString() : '');
            }
        } else if (action === 'edit' && id) {
            if (type === 'task') {
                url = '{{ route('admin.tasks.pane.edit', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'ticket') {
                url = '{{ route('admin.tickets.pane.edit', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'milestone') {
                url = '{{ route('admin.milestones.pane.edit', '__ID__') }}'.replace('__ID__', id);
            }
        } else if (action === 'show' && id) {
            if (type === 'task') {
                url = '{{ route('admin.tasks.pane.show', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'ticket') {
                url = '{{ route('admin.tickets.pane.show', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'milestone') {
                url = '{{ route('admin.milestones.pane.show', '__ID__') }}'.replace('__ID__', id);
            }
        }
        const titles = { task: 'Task', ticket: 'Ticket', milestone: 'Milestone' };
        if (url) openTaskPane(url, titles[type] || 'Task');
    });
    </script>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

@endsection
