<nav class="fixed top-0 right-0 left-0 md:left-14 lg:left-56 h-16 bg-white/80 backdrop-blur-md border-b border-gray-200 px-4 flex items-center justify-between z-40 transition-all duration-300">
    <div class="flex items-center gap-3">
        <button id="navSidebarToggle" class="md:hidden flex items-center justify-center w-8 h-8 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="bi bi-list text-xl"></i>
        </button>
        
        @php
            $path = request()->path();
            $segments = array_filter(explode('/', $path));
            $breadcrumbs = [];
            $url = '';
            
            foreach ($segments as $segment) {
                $url .= '/' . $segment;
                $label = ucfirst(str_replace(['-', '_'], ' ', $segment));
                if (is_numeric($segment)) {
                    $label = 'Details';
                }
                $breadcrumbs[] = ['url' => $url, 'label' => $label];
            }
        @endphp
        
        <nav class="hidden md:flex items-center gap-1 text-sm" aria-label="Breadcrumb">
            <a href="/admin/dashboard" class="text-gray-500 hover:text-purple-600 transition-colors flex items-center gap-1" title="Home">
                <i class="bi bi-house-door text-xs"></i>
            </a>
            @foreach($breadcrumbs as $i => $crumb)
                <i class="bi bi-chevron-right text-xs text-gray-400 mx-1"></i>
                @if($i === count($breadcrumbs) - 1)
                    <span class="font-medium text-gray-800" aria-current="page">{{ $crumb['label'] }}</span>
                @else
                    <a href="{{ $crumb['url'] }}" class="text-gray-500 hover:text-purple-600 transition-colors">{{ $crumb['label'] }}</a>
                @endif
            @endforeach
        </nav>
    </div>

    <div class="flex items-center gap-3 md:gap-6">
        <div class="relative hidden sm:block">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="globalSearch" name="q" placeholder="Search..." autocomplete="off" class="pl-10 pr-4 py-2 w-64 bg-gray-100 border-transparent focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent rounded-lg text-sm transition-all outline-none">
            </div>
            <div id="searchResults" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-y-auto" style="max-height: 60vh;"></div>
        </div>

        <div class="relative group">
            <button id="notifDropdownBtn" class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                <i class="bi bi-bell text-xl"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
            </button>
            <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50 transition-all animate-in fade-in slide-in-from-top-2">
                <div class="px-4 py-2 border-b border-gray-50 font-semibold text-sm text-gray-700">Notifications</div>
                <div class="max-h-64 overflow-y-auto">
                    <a href="#" class="flex gap-3 p-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-800">New client registered: <span class="font-semibold">Acme Corp</span></p>
                            <p class="text-[10px] text-gray-400 mt-1">2 mins ago</p>
                        </div>
                    </a>
                    <a href="#" class="flex gap-3 p-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                        <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-800">Project <span class="font-semibold">Website Redesign</span> completed</p>
                            <p class="text-[10px] text-gray-400 mt-1">1 hour ago</p>
                        </div>
                    </a>
                </div>
                <a href="#" class="block text-center py-2 text-xs text-purple-600 font-medium hover:bg-gray-50 transition-colors">View All Notifications</a>
            </div>
        </div>

        <div class="relative group">
            <button id="profileDropdownBtn" class="flex items-center gap-2 p-1 pr-3 hover:bg-gray-100 rounded-full transition-colors">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->firstname . ' ' . auth()->user()->lastname) }}&background=6366f1&color=fff" 
                     alt="Profile" class="w-8 h-8 rounded-full border-2 border-purple-500">
                <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</span>
                <i class="bi bi-chevron-down text-xs text-gray-400"></i>
            </button>
            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50 transition-all animate-in fade-in slide-in-from-top-2">
                <div class="px-4 py-2 border-b border-gray-50 mb-1">
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</p>
                    <p class="text-[10px] text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
                <a href="/admin/profile" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    <i class="bi bi-person"></i> Profile
                </a>
                <a href="/admin/settings" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    <i class="bi bi-gear"></i> Settings
                </a>
                <div class="border-t border-gray-50 mt-1 pt-1">
                    <form action="{{ route('staff.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<div id="sidebarMobile" class="fixed inset-0 z-50 hidden">
    <div id="sidebarMobileContent" class="fixed left-0 top-0 h-full w-64 bg-slate-900 text-slate-300 overflow-y-auto -translate-x-full transition-transform duration-300 z-60">
        <div class="flex items-center justify-between px-3 py-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">C</div>
                <span class="text-sm font-bold tracking-tight text-white">CRM <span class="text-blue-400">Admin</span></span>
            </div>
            <button id="sidebarClose" class="p-1 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>
        
        <div class="sidebar-menu-outer px-2 py-2 space-y-0.5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-2 py-2 rounded-md transition-all group {{ request()->is('admin/dashboard') ? 'bg-blue-600/20 text-blue-400 border-l-2 border-blue-500' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="bi bi-grid-1x2 {{ request()->is('admin/dashboard') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            @php $isClientsActive = request()->is('admin/clients*') || request()->is('admin/contacts*') || request()->is('admin/customer-groups*'); @endphp
            <div class="relative">
                <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isClientsActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-people {{ $isClientsActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium">Clients</span>
                    </div>
                    <i class="bi bi-chevron-right transition-transform duration-200 {{ $isClientsActive ? 'rotate-90' : '' }}"></i>
                </button>
                <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isClientsActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.clients.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">All Clients</a>
                    <a href="{{ route('admin.contacts.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Contacts</a>
                    <a href="{{ route('admin.customer-groups.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Groups</a>
                </div>
            </div>

            @php $isProjectsActive = request()->is('admin/projects*') || request()->is('admin/milestones*'); @endphp
            <div class="relative">
                <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isProjectsActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-kanban {{ $isProjectsActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium">Projects</span>
                    </div>
                    <i class="bi bi-chevron-right transition-transform duration-200 {{ $isProjectsActive ? 'rotate-90' : '' }}"></i>
                </button>
                <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isProjectsActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.projects.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">All Projects</a>
                    <a href="{{ route('admin.milestones.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Milestones</a>
                </div>
            </div>

            @php $isSalesActive = request()->is('admin/leads*'); @endphp
            <div class="relative">
                <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isSalesActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-megaphone {{ $isSalesActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium">Sales</span>
                    </div>
                    <i class="bi bi-chevron-right transition-transform duration-200 {{ $isSalesActive ? 'rotate-90' : '' }}"></i>
                </button>
                <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isSalesActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.leads.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Leads</a>
                </div>
            </div>

            @php $isTicketsActive = request()->is('admin/tickets*'); @endphp
            <div class="relative">
                <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isTicketsActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-ticket-perforated {{ $isTicketsActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium">Tickets</span>
                    </div>
                    <i class="bi bi-chevron-right transition-transform duration-200 {{ $isTicketsActive ? 'rotate-90' : '' }}"></i>
                </button>
                <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isTicketsActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.tickets.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">All Tickets</a>
                </div>
            </div>

            @php $isTasksActive = request()->is('admin/tasks*'); @endphp
            <div class="relative">
                <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isTasksActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-check2-square {{ $isTasksActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium">Tasks</span>
                    </div>
                    <i class="bi bi-chevron-right transition-transform duration-200 {{ $isTasksActive ? 'rotate-90' : '' }}"></i>
                </button>
                <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isTasksActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.tasks.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">All Tasks</a>
                    <a href="{{ route('admin.tasks.kanban') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Kanban</a>
                </div>
            </div>

            @php $isFinanceActive = request()->is('admin/invoices*') || request()->is('admin/subscriptions*') || request()->is('admin/estimates*') || request()->is('admin/expenses*') || request()->is('admin/credit-notes*') || request()->is('admin/creditnotes*') || request()->is('admin/proposals*'); @endphp
            <div class="relative">
                <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isFinanceActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-receipt {{ $isFinanceActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium">Finance</span>
                    </div>
                    <i class="bi bi-chevron-right transition-transform duration-200 {{ $isFinanceActive ? 'rotate-90' : '' }}"></i>
                </button>
                <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isFinanceActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.invoices.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Invoices</a>
                    <a href="{{ route('admin.estimates.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Estimates</a>
                    <a href="{{ route('admin.proposals.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Proposals</a>
                    <a href="{{ route('admin.subscriptions.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Subscriptions</a>
                    <a href="{{ route('admin.expenses.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Expenses</a>
                    <a href="{{ route('admin.creditnotes.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Credit Notes</a>
                </div>
            </div>

            @php $isKBActive = request()->is('admin/knowledge-base*') || request()->is('admin/knowledge-base-groups*'); @endphp
            <div class="relative">
                <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isKBActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-book {{ $isKBActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium">Knowledge</span>
                    </div>
                    <i class="bi bi-chevron-right transition-transform duration-200 {{ $isKBActive ? 'rotate-90' : '' }}"></i>
                </button>
                <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isKBActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.knowledgebase.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Articles</a>
                    <a href="{{ route('admin.knowledge-base-groups.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Groups</a>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-800 px-2 py-2">
            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-2 mb-1">System</p>
            <div class="space-y-0.5">
                @php $isStaffActive = request()->is('admin/staff*') || request()->is('admin/departments*'); @endphp
                <div class="relative">
                    <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isStaffActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-person-badge {{ $isStaffActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                            <span class="font-medium">Staff</span>
                        </div>
                        <i class="bi bi-chevron-right transition-transform duration-200 {{ $isStaffActive ? 'rotate-90' : '' }}"></i>
                    </button>
                    <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isStaffActive ? '' : 'hidden' }}">
                        <a href="{{ route('admin.staff.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Team</a>
                        <a href="{{ route('admin.departments.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Departments</a>
                    </div>
                </div>

                @php $isLookupsActive = request()->is('admin/countries*') || request()->is('admin/currencies*') || request()->is('admin/services*') || request()->is('admin/payment-modes*') || request()->is('admin/taxes*') || request()->is('admin/item-groups*') || request()->is('admin/items*'); @endphp
                <div class="relative">
                    <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isLookupsActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-database {{ $isLookupsActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                            <span class="font-medium">Lookups</span>
                        </div>
                        <i class="bi bi-chevron-right transition-transform duration-200 {{ $isLookupsActive ? 'rotate-90' : '' }}"></i>
                    </button>
                    <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isLookupsActive ? '' : 'hidden' }}">
                        <a href="{{ route('admin.items.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Items</a>
                        <a href="{{ route('admin.countries.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Countries</a>
                        <a href="{{ route('admin.services.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Services</a>
                        <a href="{{ route('admin.taxes.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Taxes</a>
                    </div>
                </div>

                @php $isSettingsActive = request()->is('admin/settings*') || request()->is('admin/roles*') || request()->is('admin/permissions*') || request()->is('admin/emailtemplates*'); @endphp
                <div class="relative">
                    <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isSettingsActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-gear {{ $isSettingsActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                            <span class="font-medium">Settings</span>
                        </div>
                        <i class="bi bi-chevron-right transition-transform duration-200 {{ $isSettingsActive ? 'rotate-90' : '' }}"></i>
                    </button>
                    <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isSettingsActive ? '' : 'hidden' }}">
                        <a href="{{ route('admin.settings.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">System</a>
                        <a href="{{ route('admin.roles.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Roles</a>
                        <a href="{{ route('admin.permissions.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800">Permissions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="mobileOverlay" class="fixed inset-0 bg-black/60 hidden z-40"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Set up notification and profile dropdowns
  function setupDropdown(btnId, dropdownId) {
    const btn = document.getElementById(btnId);
    const dropdown = document.getElementById(dropdownId);
    if (!btn || !dropdown) return;
    
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      dropdown.classList.toggle('hidden');
    });
  }
  
  setupDropdown('notifDropdownBtn', 'notifDropdown');
  setupDropdown('profileDropdownBtn', 'profileDropdown');
  
  // Global click handler to close dropdowns when clicking outside
  document.addEventListener('click', function() {
    document.getElementById('notifDropdown')?.classList.add('hidden');
    document.getElementById('profileDropdown')?.classList.add('hidden');
  });
  
  // Use event delegation for sidebar dropdowns to handle both desktop and mobile
  // This ensures it works even for dynamically created elements
  document.addEventListener('click', function(e) {
    if (e.target.closest('.sidebar-dropdown')) {
      e.preventDefault();
      var btn = e.target.closest('.sidebar-dropdown');
      var menu = btn.nextElementSibling;
      var icon = btn.querySelector('.bi-chevron-right');
      
      // Only proceed if we found both elements
      if (!menu || !icon) return;
      
      // Toggle the visibility
      if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
        icon.classList.add('rotate-90');
      } else {
        menu.classList.add('hidden');
        icon.classList.remove('rotate-90');
      }
    }
  });

  var navToggle = document.getElementById('navSidebarToggle');
  var closeBtn = document.getElementById('sidebarClose');
  var mobileSidebar = document.getElementById('sidebarMobile');
  var mobileContent = document.getElementById('sidebarMobileContent');
  var overlay = document.getElementById('mobileOverlay');

  if (navToggle && mobileSidebar) {
    navToggle.addEventListener('click', function() {
      mobileSidebar.classList.remove('hidden');
      mobileContent.classList.remove('-translate-x-full');
      overlay?.classList.remove('hidden');
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', function() {
        mobileSidebar.classList.add('hidden');
        mobileContent.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
      });
    }

    if (overlay) {
      overlay.addEventListener('click', function() {
        mobileSidebar.classList.add('hidden');
        mobileContent.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
      });
    }
  }

    var searchInput = document.getElementById('globalSearch');
    var searchResults = document.getElementById('searchResults');
    var searchTimeout;

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            var query = this.value.trim();
            
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(function() {
                fetch('{{ route("admin.search") }}?q=' + encodeURIComponent(query))
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        if (!data || data.length === 0) {
                            searchResults.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">No results found</div>';
                        } else {
                            var html = '';
                            data.forEach(function(item) {
                                html += '<a href="' + item.url + '" class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">';
                                html += '<div class="w-8 h-8 rounded-lg flex items-center justify-center ' + item.icon_bg + '">';
                                html += '<i class="bi ' + item.icon + ' text-sm"></i>';
                                html += '</div>';
                                html += '<div class="flex-1 min-w-0">';
                                html += '<p class="text-sm font-medium text-gray-900 truncate">' + item.title + '</p>';
                                html += '<p class="text-xs text-gray-500 truncate">' + item.subtitle + '</p>';
                                html += '</div>';
                                html += '<span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 shrink-0">' + item.type + '</span>';
                                html += '</a>';
                            });
                            searchResults.innerHTML = html;
                        }
                        searchResults.classList.remove('hidden');
                    });
            }, 300);
        });

        searchInput.addEventListener('blur', function() {
            setTimeout(function() {
                searchResults.classList.add('hidden');
            }, 200);
        });

        searchInput.addEventListener('focus', function() {
            if (searchInput.value.trim().length >= 2) {
                searchResults.classList.remove('hidden');
            }
        });
    }
});
</script>