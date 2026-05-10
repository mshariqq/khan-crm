@extends('layouts.app')

@section('title', 'CRM System')

@section('content')
<div class="min-h-screen flex flex-col">
    <nav class="bg-white border-b border-gray-200 px-4 py-3 md:px-6 md:py-4 flex flex-col md:flex-row md:justify-between md:items-center gap-2 md:gap-0">
        <div class="flex items-center gap-2">
            @if(!empty($global_settings['company_logo']))
                <img src="{{ $global_settings['company_logo'] }}" alt="{{ $global_settings['companyname'] }}" class="h-8 w-auto">
            @else
                <div class="text-xl md:text-2xl font-bold text-indigo-600 tracking-tight">
                    {{ $global_settings['companyname'] ?? 'CRM System' }}
                </div>
            @endif
        </div>
        <div class="flex items-center gap-3 md:space-x-4">
            @if(($global_settings['use_knowledge_base'] ?? '1') == '1')
            <a href="/knowledge-base" class="text-xs md:text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Knowledge Base</a>
            @endif
            
            @auth('staff')
                <div class="relative" id="staff-dropdown-container">
                    <button onclick="toggleDropdown('staff-dropdown')" class="flex items-center gap-2 text-gray-600 hover:text-gray-800 transition">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm">
                            {{ substr(auth()->user()->firstname ?? 'S', 0, 1) }}
                        </div>
                        <span class="text-xs md:text-sm hidden md:block">{{ auth()->user()->firstname ?? 'Staff' }}</span>
                        <i class="bi bi-chevron-down text-xs"></i>
                    </button>
                    <div id="staff-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 hidden z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->firstname ?? '' }} {{ auth()->user()->lastname ?? '' }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                        </div>
                        <a href="/admin/dashboard" class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-indigo-600">
                            <i class="bi bi-grid mr-2"></i> Dashboard
                        </a>
                        <form action="/admin/logout" method="POST" class="block w-full text-left">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-red-600">
                                <i class="bi bi-box-arrow-right mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @elseauth('client')
                <div class="relative" id="client-dropdown-container">
                    <button onclick="toggleDropdown('client-dropdown')" class="flex items-center gap-2 text-gray-600 hover:text-gray-800 transition">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-semibold text-sm">
                            {{ substr(auth('client')->user()->firstname ?? 'C', 0, 1) }}
                        </div>
                        <span class="text-xs md:text-sm hidden md:block">{{ auth('client')->user()->firstname ?? 'Client' }}</span>
                        <i class="bi bi-chevron-down text-xs"></i>
                    </button>
                    <div id="client-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 hidden z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-800">{{ auth('client')->user()->firstname ?? '' }} {{ auth('client')->user()->lastname ?? '' }}</p>
                            <p class="text-xs text-gray-500">{{ auth('client')->user()->email ?? '' }}</p>
                        </div>
                        <a href="/client/dashboard" class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-indigo-600">
                            <i class="bi bi-grid mr-2"></i> Dashboard
                        </a>
                        <form action="/client/logout" method="POST" class="block w-full text-left">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-red-600">
                                <i class="bi bi-box-arrow-right mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="/login/staff" class="text-xs md:text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Staff</a>
                <a href="/login/client" class="bg-indigo-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-lg text-xs md:text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                    Client Portal
                </a>
            @endauth
        </div>
    </nav>
    
    <main class="flex-grow">
        @yield('main')
    </main>
    
    <footer class="bg-white border-t border-gray-200 py-6 md:py-8 text-center text-gray-500 text-xs md:text-sm">
        <p>&copy; {{ date('Y') }} {{ $global_settings['companyname'] ?? 'CRM System' }}. {{ $global_settings['copyright_text'] ?? 'Built for efficiency.' }}</p>
        @if(!empty($global_settings['company_email']))
            <p class="mt-1"><i class="bi bi-envelope mr-1"></i> {{ $global_settings['company_email'] }}</p>
        @endif
    </footer>
</div>

<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    document.querySelectorAll('[id$="-dropdown"]:not(.hidden)').forEach(function(dropdown) {
        const container = dropdown.id.replace('-dropdown', '-dropdown-container');
        if (!document.getElementById(container).contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>
@endsection