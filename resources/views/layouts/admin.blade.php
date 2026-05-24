@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <style>
        @keyframes slideDown { from { opacity:0; transform: translateY(-20px); } to { opacity:1; transform: translateY(0); } }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
        @keyframes fadeInUp { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        .animate-fade-in-up-delay-1 { animation-delay: 0.1s; opacity: 0; }
        .animate-fade-in-up-delay-2 { animation-delay: 0.2s; opacity: 0; }
        .animate-fade-in-up-delay-3 { animation-delay: 0.3s; opacity: 0; }
        .animate-fade-in-up-delay-4 { animation-delay: 0.4s; opacity: 0; }
        .animate-fade-in-up-delay-5 { animation-delay: 0.5s; opacity: 0; }
        @keyframes pulse-soft { 0%,100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .stat-card-gradient-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card-gradient-2 { background: linear-gradient(135deg, #f093fb 0%, #f5577b 100%); }
        .stat-card-gradient-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card-gradient-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-card-gradient-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .stat-card-gradient-6 { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); }
        .glass-card { background: rgba(255,255,255,0.85); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
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
        <div class="absolute right-0 top-0 h-full w-full max-w-[95%] md:max-w-[78%] lg:max-w-[65%] xl:max-w-[50%] bg-white shadow-2xl transform transition-transform duration-300 flex flex-col">
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

    function showToast(message, type = 'success', undoCallback = null) {
        const colors = { success: 'bg-emerald-500', error: 'bg-red-500', warning: 'bg-amber-500', info: 'bg-blue-500' };
        const icons = { success: 'check-circle', error: 'x-circle', warning: 'exclamation-triangle', info: 'info-circle' };
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-[100] px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-y-[-20px] opacity-0 ${colors[type] || colors.success}`;
        toast.innerHTML = `<div class="flex items-center gap-2"><i class="bi bi-${icons[type] || icons.success}"></i><span>${message}</span></div>`;

        if (undoCallback) {
            const undoBtn = document.createElement('button');
            undoBtn.className = 'ml-3 px-2 py-1 bg-white/20 text-white text-xs font-bold rounded hover:bg-white/30 transition-all';
            undoBtn.textContent = 'Undo';
            undoBtn.onclick = function() {
                undoCallback();
                toast.remove();
            };
            toast.querySelector('.flex.items-center.gap-2').appendChild(undoBtn);
        }

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

    let paneHistory = [];
    let _paneBacking = false;

    function openTaskPane(url, title) {
        const pane = document.getElementById('taskPane');
        const paneTitle = document.getElementById('taskPaneTitle');
        const content = document.getElementById('taskPaneContent');

        if (!_paneBacking) {
            const currentUrl = content.getAttribute('data-pane-url');
            if (currentUrl && content.innerHTML && !content.innerHTML.includes('animate-pulse')) {
                paneHistory.push({
                    url: currentUrl,
                    title: paneTitle.textContent
                });
            }
        }
        _paneBacking = false;

        if (title) paneTitle.textContent = title;
        content.setAttribute('data-pane-url', url);

        content.innerHTML = '<div class="p-6 space-y-6 animate-pulse"><div class="flex items-start gap-4"><div class="w-12 h-12 rounded-2xl bg-slate-200"></div><div class="space-y-2 flex-1"><div class="h-5 bg-slate-200 rounded w-1/3"></div><div class="h-3 bg-slate-200 rounded w-1/4"></div></div></div><div class="grid grid-cols-5 gap-5"><div class="col-span-3 space-y-5"><div class="h-32 bg-slate-200 rounded-xl"></div><div class="h-48 bg-slate-200 rounded-xl"></div><div class="h-64 bg-slate-200 rounded-xl"></div></div><div class="col-span-2 space-y-4"><div class="h-48 bg-slate-200 rounded-xl"></div><div class="h-32 bg-slate-200 rounded-xl"></div><div class="h-40 bg-slate-200 rounded-xl"></div></div></div></div>';
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
                updatePaneBackButton();
            })
            .catch(() => {
                content.innerHTML = '<div class="flex items-center justify-center h-full text-red-500"><p>Failed to load content. Please try again.</p></div>';
            });

        updatePaneBackButton();
    }

    function goBackInPane() {
        if (paneHistory.length > 0) {
            const prev = paneHistory.pop();
            if (prev.url) {
                _paneBacking = true;
                openTaskPane(prev.url, prev.title);
            }
        }
        updatePaneBackButton();
    }

    function updatePaneBackButton() {
        let backBtn = document.getElementById('paneBackBtn');
        if (paneHistory.length > 0) {
            if (!backBtn) {
                backBtn = document.createElement('button');
                backBtn.id = 'paneBackBtn';
                backBtn.innerHTML = '<i class="bi bi-arrow-left"></i>';
                backBtn.className = 'p-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-all';
                backBtn.onclick = goBackInPane;
                document.querySelector('#taskPane .flex.items-center.justify-between').prepend(backBtn);
            }
            backBtn.classList.remove('hidden');
        } else if (backBtn) {
            backBtn.classList.add('hidden');
        }
    }

    function openTicketPane(url) { openTaskPane(url, 'Ticket'); }
    function openMilestonePane(url) { openTaskPane(url, 'Milestone'); }
    function openEstimatePane(url) { openTaskPane(url, 'Estimate'); }
    function openProposalPane(url) { openTaskPane(url, 'Proposal'); }
    function openContactPane(url) { openTaskPane(url, 'Contact'); }
    function openExpensePane(url) { openTaskPane(url, 'Expense'); }
    function openItemPane(url) { openTaskPane(url, 'Item'); }
    function openItemsGroupPane(url) { openTaskPane(url, 'Item Group'); }

    function closeTaskPane() {
        paneHistory = [];
        let backBtn = document.getElementById('paneBackBtn');
        if (backBtn) backBtn.classList.add('hidden');
        const pane = document.getElementById('taskPane');
        pane.querySelector('div:last-child').style.transform = 'translateX(100%)';
        setTimeout(() => { pane.classList.add('hidden'); document.getElementById('taskPaneContent').innerHTML = ''; }, 300);
    }

    // CSS tooltips
    document.addEventListener('mouseover', function(e) {
        const el = e.target.closest('[title]');
        if (el && !el.dataset.tooltipInit) {
            el.dataset.tooltipInit = '1';
            el.style.position = 'relative';
            el.addEventListener('mouseenter', function() {
                const existing = this.querySelector('.css-tooltip');
                if (existing) return;
                const tip = document.createElement('span');
                tip.className = 'css-tooltip';
                tip.textContent = this.getAttribute('title');
                tip.style.cssText = 'position:absolute;bottom:calc(100% + 4px);left:50%;transform:translateX(-50%);background:#1e293b;color:#fff;font-size:11px;padding:2px 8px;border-radius:4px;white-space:nowrap;z-index:100;pointer-events:none';
                const arrow = document.createElement('span');
                arrow.style.cssText = 'position:absolute;top:100%;left:50%;transform:translateX(-50%);border:4px solid transparent;border-top-color:#1e293b';
                tip.appendChild(arrow);
                this.appendChild(tip);
            });
            el.addEventListener('mouseleave', function() {
                const tip = this.querySelector('.css-tooltip');
                if (tip) tip.remove();
            });
            el.removeAttribute('title');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const pane = document.getElementById('taskPane');
            if (!pane.classList.contains('hidden')) {
                closeTaskPane();
            }
        }
    });

    function initPaneForms() {
        document.querySelectorAll('#taskPaneContent form[data-pane-submit]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                document.querySelectorAll('.text-red-500.text-xs').forEach(el => el.remove());
                form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'ring-red-500'));

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
                        const currentTitle = document.getElementById('taskPaneTitle').textContent;
                        closeTaskPane();
                        const refreshUrl = form.getAttribute('data-pane-refresh') || data.pane_refresh_url;
                        if (refreshUrl) {
                            setTimeout(() => openTaskPane(refreshUrl, currentTitle), 350);
                        } else if (data.reload) {
                            setTimeout(() => window.location.reload(), 400);
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
            } else if (type === 'estimate') {
                url = '{{ route('admin.estimates.pane.create') }}' + (params.toString() ? '?' + params.toString() : '');
            } else if (type === 'proposal') {
                url = '{{ route('admin.proposals.pane.create') }}' + (params.toString() ? '?' + params.toString() : '');
            } else if (type === 'contact') {
                url = '{{ route('admin.contacts.pane.create') }}' + (params.toString() ? '?' + params.toString() : '');
            } else if (type === 'expense') {
                url = '{{ route('admin.expenses.pane.create') }}' + (params.toString() ? '?' + params.toString() : '');
            } else if (type === 'item') {
                url = '{{ route('admin.items.pane.create') }}' + (params.toString() ? '?' + params.toString() : '');
            } else if (type === 'itemsgroup') {
                url = '{{ route('admin.itemsgroups.pane.create') }}' + (params.toString() ? '?' + params.toString() : '');
            }
        } else if (action === 'edit' && id) {
            if (type === 'task') {
                url = '{{ route('admin.tasks.pane.edit', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'ticket') {
                url = '{{ route('admin.tickets.pane.edit', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'milestone') {
                url = '{{ route('admin.milestones.pane.edit', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'estimate') {
                url = '{{ route('admin.estimates.pane.edit', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'proposal') {
                url = '{{ route('admin.proposals.pane.edit', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'contact') {
                url = '{{ route('admin.contacts.pane.edit', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'expense') {
                url = '{{ route('admin.expenses.pane.edit', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'item') {
                url = '{{ route('admin.items.pane.edit', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'itemsgroup') {
                url = '{{ route('admin.itemsgroups.pane.edit', '__ID__') }}'.replace('__ID__', id);
            }
        } else if (action === 'show' && id) {
            if (type === 'task') {
                url = '{{ route('admin.tasks.pane.show', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'ticket') {
                url = '{{ route('admin.tickets.pane.show', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'milestone') {
                url = '{{ route('admin.milestones.pane.show', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'estimate') {
                url = '{{ route('admin.estimates.pane.show', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'proposal') {
                url = '{{ route('admin.proposals.pane.show', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'contact') {
                url = '{{ route('admin.contacts.pane.show', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'expense') {
                url = '{{ route('admin.expenses.pane.show', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'item') {
                url = '{{ route('admin.items.pane.show', '__ID__') }}'.replace('__ID__', id);
            } else if (type === 'itemsgroup') {
                url = '{{ route('admin.itemsgroups.pane.show', '__ID__') }}'.replace('__ID__', id);
            }
        }
            const titles = { task: 'Task', ticket: 'Ticket', milestone: 'Milestone', estimate: 'Estimate', proposal: 'Proposal', contact: 'Contact', expense: 'Expense', item: 'Item', itemsgroup: 'Item Group' };
        if (url) openTaskPane(url, titles[type] || 'Task');
    });
    </script>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

@include('admin.partials.delete-modal')

{{-- Real-time Notifications via Pusher --}}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
@auth('staff')
@php $pusherKey = config('broadcasting.connections.pusher.key'); @endphp
@if($pusherKey && config('broadcasting.default') === 'pusher')
(function() {
    var pusher = new Pusher('{{ $pusherKey }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}',
        encrypted: true
    });
    var channel = pusher.subscribe('user.{{ auth()->id() }}');
    channel.bind('new-notification', function(data) {
        // Update unread count badge
        var badge = document.getElementById('notifBadge');
        if (badge) {
            badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
            if (data.unread_count > 99) badge.textContent = '99+';
            badge.className = 'absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center text-[9px] font-bold text-white bg-red-500 border-2 border-white rounded-full px-1';
        }
        // Play sound
        try {
            var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = audioCtx.createOscillator();
            var gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.value = 800;
            osc.type = 'sine';
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.15);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.15);
        } catch(e) {}
        // Update favicon badge
        var link = document.querySelector('link[rel="icon"]');
        if (link && data.unread_count > 0) {
            var canvas = document.createElement('canvas');
            canvas.width = 32; canvas.height = 32;
            var ctx = canvas.getContext('2d');
            ctx.fillStyle = '#6366f1';
            ctx.beginPath(); ctx.arc(16, 16, 16, 0, 2 * Math.PI); ctx.fill();
            ctx.fillStyle = '#fff';
            ctx.font = 'bold 18px sans-serif';
            ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
            ctx.fillText(data.unread_count > 9 ? '9+' : data.unread_count, 16, 17);
            link.href = canvas.toDataURL();
        }
    });
})();
@endif
@endauth
</script>

@endsection
