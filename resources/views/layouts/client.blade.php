<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Client Portal') - {{ $global_settings['companyname'] ?? 'CRM' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { background: rgba(59, 130, 246, 0.1); color: #2563eb; border-right: 4px solid #2563eb; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-slate-200 hidden lg:flex flex-col sticky top-0 h-screen">
            <div class="p-6 border-b border-slate-100">
                @if(!empty($global_settings['company_logo']))
                    <img src="{{ $global_settings['company_logo'] }}" alt="Logo" class="h-8 w-auto">
                @else
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">ClientPortal</span>
                @endif
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="{{ route('client.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> <span class="text-sm font-semibold">Dashboard</span>
                </a>
                <a href="{{ route('client.projects.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all {{ request()->routeIs('client.projects.*') ? 'active' : '' }}">
                    <i class="bi bi-kanban"></i> <span class="text-sm font-semibold">Projects</span>
                </a>
                <a href="{{ route('client.invoices.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all {{ request()->routeIs('client.invoices.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> <span class="text-sm font-semibold">Invoices</span>
                </a>
                <a href="{{ route('client.proposals.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all {{ request()->routeIs('client.proposals.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> <span class="text-sm font-semibold">Proposals</span>
                </a>
                <a href="{{ route('client.estimates.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all {{ request()->routeIs('client.estimates.*') ? 'active' : '' }}">
                    <i class="bi bi-calculator"></i> <span class="text-sm font-semibold">Estimates</span>
                </a>
                <a href="{{ route('client.tickets.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all {{ request()->routeIs('client.tickets.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated"></i> <span class="text-sm font-semibold">Support Tickets</span>
                </a>
                <div class="border-t border-slate-100 my-2"></div>
                <a href="{{ route('client.profile.edit') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all {{ request()->routeIs('client.profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> <span class="text-sm font-semibold">My Profile</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-100">
                <form action="{{ route('client.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-all">
                        <i class="bi bi-box-arrow-left"></i> <span class="text-sm font-semibold">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-30 px-6 flex items-center justify-between">
                <div class="flex items-center gap-4 lg:hidden">
                    <button class="text-slate-500"><i class="bi bi-list text-2xl"></i></button>
                    <span class="font-bold text-blue-600">Portal</span>
                </div>
                
                <div class="flex-1 px-4 hidden lg:block">
                    <h2 class="text-sm font-semibold text-slate-400">@yield('breadcrumb', 'Welcome Back')</h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::guard('client')->user()->firstname }} {{ Auth::guard('client')->user()->lastname }}</p>
                        <p class="text-[10px] text-slate-400">{{ Auth::guard('client')->user()->client->company }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border-2 border-white shadow-sm">
                        {{ substr(Auth::guard('client')->user()->firstname, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 animate-fade-in">
                        <i class="bi bi-check-circle-fill"></i>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                @yield('main')
            </div>
        </main>
    </div>

    <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-sm text-slate-500">
        <p>&copy; {{ date('Y') }} {{ $global_settings['companyname'] ?? 'CRM' }}. {{ $global_settings['copyright_text'] ?? 'All rights reserved' }}</p>
    </footer>

    @stack('scripts')
</body>
</html>
