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
</script>

@endsection
