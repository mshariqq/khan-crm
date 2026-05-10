<div class="space-y-6">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 flex flex-col md:flex-row items-center gap-6">
            <div class="w-24 h-24 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-blue-500/20">
                {{ substr($client->company, 0, 1) }}
            </div>
            <div class="flex-1 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start gap-3 mb-1">
                    <h1 class="text-3xl font-bold text-slate-800">{{ $client->company }}</h1>
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                        {{ $client->active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-slate-500 flex items-center justify-center md:justify-start gap-4">
                    <span><i class="bi bi-geo-alt text-blue-500 mr-1"></i> {{ $client->city }}, {{ $client->country->name ?? 'N/A' }}</span>
                    <span><i class="bi bi-telephone text-blue-500 mr-1"></i> {{ $client->phonenumber ?? 'N/A' }}</span>
                    <span><i class="bi bi-globe text-blue-500 mr-1"></i> {{ $client->website ?? 'N/A' }}</span>
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.clients.edit', $client->userid) }}" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-lg hover:bg-slate-200 transition-all flex items-center gap-2">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-9 gap-4">
        @php
            $statsLabels = [
                'projects' => 'Projects',
                'tickets' => 'Tickets',
                'invoices' => 'Invoices',
                'estimates' => 'Estimates',
                'expenses' => 'Expenses',
                'tasks' => 'Tasks',
                'reminders' => 'Reminders',
                'notes' => 'Notes',
                'contacts' => 'Contacts',
            ];
        @endphp
        @foreach($statsLabels as $key => $label)
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">{{ $label }}</p>
                <p class="text-2xl font-bold text-slate-800">{{ $stats[$key] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Navigation Tabs --}}
    <div class="border-b border-slate-200 overflow-x-auto scrollbar-slim">
        <ul class="flex flex-nowrap gap-2 pb-1">
            @php
                $tabs = [
                    'overview' => ['label' => 'Overview', 'icon' => 'bi-house', 'route' => route('admin.clients.show', $client->userid)],
                    'contacts' => ['label' => 'Contacts', 'icon' => 'bi-person-lines-fill', 'route' => route('admin.clients.contacts', $client->userid)],
                    'projects' => ['label' => 'Projects', 'icon' => 'bi-kanban', 'route' => route('admin.clients.projects', $client->userid)],
                    'tickets' => ['label' => 'Tickets', 'icon' => 'bi-ticket-perforated', 'route' => route('admin.clients.tickets', $client->userid)],
                    'invoices' => ['label' => 'Invoices', 'icon' => 'bi-receipt', 'route' => route('admin.clients.invoices', $client->userid)],
                    'estimates' => ['label' => 'Estimates', 'icon' => 'bi-file-earmark-text', 'route' => route('admin.clients.estimates', $client->userid)],
                    'expenses' => ['label' => 'Expenses', 'icon' => 'bi-cash-stack', 'route' => route('admin.clients.expenses', $client->userid)],
                    'tasks' => ['label' => 'Tasks', 'icon' => 'bi-check2-square', 'route' => route('admin.clients.tasks', $client->userid)],
                    'reminders' => ['label' => 'Reminders', 'icon' => 'bi-bell', 'route' => route('admin.clients.reminders', $client->userid)],
                    'notes' => ['label' => 'Notes', 'icon' => 'bi-sticky', 'route' => route('admin.clients.notes', $client->userid)],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <li>
<a href="{{ $tab['route'] }}" 
                   class="px-4 py-2 text-sm font-medium rounded-t-lg transition-all whitespace-nowrap {{ request()->routeIs('admin.clients.' . ($key === 'overview' ? 'show' : $key)) ? 'bg-white text-blue-600 border-t-2 border-l-2 border-r-2 border-blue-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100' }}">
                        <i class="bi {{ $tab['icon'] }} mr-2"></i> {{ $tab['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
