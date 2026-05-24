<div id="sidebar" class="-sidebar fixed left-0 top-0 h-screen w-56 bg-slate-900 text-slate-300 overflow-y-auto hidden md:block border-r border-slate-800 z-60">
    <div class="flex items-center justify-between px-3 py-3 border-b border-slate-800">
        <div class="flex items-center gap-2">
            @if(!empty($global_settings['company_logo']))
                <img src="{{ $global_settings['company_logo'] }}" alt="{{ $global_settings['companyname'] }}" class="h-8 w-auto">
            @else
                <div class="w-7 h-7 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                    {{ substr($global_settings['companyname'] ?? 'C', 0, 1) }}
                </div>
            @endif
            <span class="text-sm font-bold tracking-tight text-white">{{ $global_settings['companyname'] ?? 'CRM' }} <span class="text-blue-400">Admin</span></span>
        </div>
    </div>

    <div class="sidebar-menu-outer px-2 py-2 space-y-0.5">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-2 py-2 rounded-md transition-all group {{ request()->is('admin/dashboard') ? 'bg-blue-600/20 text-blue-400 border-l-2 border-blue-500' : 'hover:bg-slate-800 hover:text-white' }}">
            <i class="bi bi-grid-1x2 {{ request()->is('admin/dashboard') ? 'text-blue-400' : 'text-slate-400' }}"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        @can_do('clients_view')
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
                <a href="{{ route('admin.clients.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/clients*') && !request()->is('admin/clients/create') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">All Clients</a>
                <a href="{{ route('admin.contacts.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/contacts*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Contacts</a>
                <a href="{{ route('admin.customer-groups.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/customer-groups*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Groups</a>
            </div>
        </div>
        @endcan_do

        @can_do('projects_view')
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
                <a href="{{ route('admin.projects.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/projects*') && !request()->is('admin/projects/*/dashboard') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">All Projects</a>
                <a href="{{ route('admin.milestones.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/milestones*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Milestones</a>
            </div>
        </div>
        @endcan_do

        @can_do('leads_view')
        @php $isSalesActive = request()->is('admin/leads*') || request()->is('admin/items*') || request()->is('admin/item-groups*') || request()->is('admin/itemsgroups*'); @endphp
        <div class="relative">
            <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isSalesActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-2">
                    <i class="bi bi-megaphone {{ $isSalesActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                    <span class="font-medium">Sales</span>
                </div>
                <i class="bi bi-chevron-right transition-transform duration-200 {{ $isSalesActive ? 'rotate-90' : '' }}"></i>
            </button>
            <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isSalesActive ? '' : 'hidden' }}">
                <a href="{{ route('admin.leads.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/leads*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Leads</a>
                <a href="{{ route('admin.items.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/items*') && !request()->is('admin/itemsgroups*') && !request()->is('admin/item-groups*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Items</a>
                <a href="{{ route('admin.itemsgroups.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/item-groups*') || request()->is('admin/itemsgroups*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Item Groups</a>
            </div>
        </div>
        @endcan_do

        @can_do('tickets_view')
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
                <a href="{{ route('admin.tickets.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/tickets*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">All Tickets</a>
            </div>
        </div>
        @endcan_do

        @can_do('tasks_view')
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
                <a href="{{ route('admin.tasks.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/tasks*') && !request()->is('admin/tasks/kanban') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">All Tasks</a>
                <a href="{{ route('admin.tasks.kanban') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/tasks/kanban') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Kanban</a>
            </div>
        </div>
        @endcan_do

        @can_do('invoices_view')
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
                <a href="{{ route('admin.invoices.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/invoices*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Invoices</a>
                <a href="{{ route('admin.estimates.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/estimates*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Estimates</a>
                <a href="{{ route('admin.proposals.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/proposals*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Proposals</a>
                <a href="{{ route('admin.subscriptions.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/subscriptions*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Subscriptions</a>
                <a href="{{ route('admin.expenses.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/expenses*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Expenses</a>
                <a href="{{ route('admin.creditnotes.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/credit-notes*') || request()->is('admin/creditnotes*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Credit Notes</a>
            </div>
        </div>
        @endcan_do

        @can_do('attendance_view')
        @php $isHRActive = request()->is('admin/attendance*') || request()->is('admin/leaves*') || request()->is('admin/payroll*'); @endphp
        <div class="relative">
            <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isHRActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-2">
                    <i class="bi bi-person-heart {{ $isHRActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                    <span class="font-medium">HR Management</span>
                </div>
                <i class="bi bi-chevron-right transition-transform duration-200 {{ $isHRActive ? 'rotate-90' : '' }}"></i>
            </button>
            <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isHRActive ? '' : 'hidden' }}">
                <a href="{{ route('admin.attendance.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/attendance*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Attendance</a>
                <a href="{{ route('admin.leaves.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/leaves*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Leaves & Holidays</a>
                <a href="{{ route('admin.payroll.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/payroll*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Payroll Management</a>
                <a href="{{ route('admin.payroll.my-payslips') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/payroll/my-payslips') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">My Payslips</a>
            </div>
        </div>
        @endcan_do

        @can_do('knowledge_base_view')
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
                <a href="{{ route('admin.knowledgebase.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/knowledge-base*') && !request()->is('admin/knowledge-base/create') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Articles</a>
                <a href="{{ route('admin.knowledge-base-groups.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/knowledge-base-groups*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Groups</a>
            </div>
        </div>
        @endcan_do
    </div>

    <div class="border-t border-slate-800 px-2 py-2">
        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-2 mb-1">System</p>
        <div class="space-y-0.5">
            @can_do('staff_view')
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
                    <a href="{{ route('admin.staff.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/staff*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Team</a>
                    <a href="{{ route('admin.departments.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/departments*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Departments</a>
                </div>
            </div>
            @endcan_do

            @can_do('settings_view')
            @php $isLookupsActive = request()->is('admin/countries*') || request()->is('admin/currencies*') || request()->is('admin/services*') || request()->is('admin/payment-modes*') || request()->is('admin/paymentmodes*') || request()->is('admin/taxes*') || request()->is('admin/expense-categories*'); @endphp
            <div class="relative">
                <button type="button" class="sidebar-dropdown w-full flex items-center justify-between px-2 py-2 rounded-md transition-all group {{ $isLookupsActive ? 'bg-blue-600/20 text-blue-400' : 'hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-database {{ $isLookupsActive ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium">Lookups</span>
                    </div>
                    <i class="bi bi-chevron-right transition-transform duration-200 {{ $isLookupsActive ? 'rotate-90' : '' }}"></i>
                </button>
                <div class="sidebar-menu pl-3 mt-0.5 overflow-hidden transition-all duration-200 {{ $isLookupsActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.countries.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/countries*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Countries</a>
                    <a href="{{ route('admin.currencies.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/currencies*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Currencies</a>
                    <a href="{{ route('admin.services.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/services*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Services</a>
                    <a href="{{ route('admin.paymentmodes.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/payment-modes*') || request()->is('admin/paymentmodes*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Payment Modes</a>
                    <a href="{{ route('admin.taxes.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/taxes*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Taxes</a>
                    <a href="{{ route('admin.expense-categories.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/expense-categories*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Expense Categories</a>
                    <a href="{{ route('admin.leave-types.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/leave-types*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Leave Types</a>
                </div>
            </div>
            @endcan_do

            @can_do('settings_view')
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
                    <a href="{{ route('admin.settings.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/settings*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">System</a>
                    <a href="{{ route('admin.roles.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/roles*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Roles</a>
                    <a href="{{ route('admin.permissions.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/permissions*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Permissions</a>
                    <a href="{{ route('admin.emailtemplates.index') }}" class="block py-1.5 px-2 text-sm rounded-md hover:bg-slate-800 {{ request()->is('admin/emailtemplates*') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white' }}">Email Templates</a>
                </div>
            </div>
            @endcan_do
</div>
</div>
</div>
