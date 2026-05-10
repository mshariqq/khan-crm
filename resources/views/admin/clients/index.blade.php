@extends('layouts.admin')

@section('breadcrumb', 'Client List')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Clients</h1>
            <p class="text-slate-500 text-sm">Manage your client companies and account details.</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.clients.export') }}" method="GET" class="inline">
                @if(request('country')) <input type="hidden" name="country" value="{{ request('country') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
                @if(request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-all shadow-lg shadow-green-500/20 flex items-center gap-2">
                    <i class="bi bi-download"></i> Export CSV
                </button>
            </form>
            <a href="{{ route('admin.clients.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Add Client
            </a>
            <div class="relative w-64">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="bi bi-search text-slate-400"></i>
                    </span>
                    <input type="text" id="clientSearch" placeholder="Quick search..." class="w-full pl-10 pr-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <div id="searchDropdown" class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                    <!-- Results will be injected here -->
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium animate-fade-in">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium animate-fade-in">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('admin.clients.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Country</label>
                <select name="country" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->country_id }}" {{ request('country') == $country->country_id ? 'selected' : '' }}>{{ $country->long_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Status</label>
                <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-all">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('admin.clients.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>
 
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Company</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Country</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Currency</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Group</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Added Date</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Invoice Total</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($clients as $client)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $client->company }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $client->country->long_name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $client->currency->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-medium">
                                {{ $client->group->name ?? 'No Group' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($client->datecreated)->format('M j Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 font-medium">
                            {{ number_format($client->invoices_sum_total ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($client->active)
                                <span class="flex items-center gap-1.5 text-green-600 text-xs font-bold">
                                    <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span> Active
                                </span>
                            @else
                                <span class="flex items-center gap-1.5 text-red-600 text-xs font-bold">
                                    <span class="w-1.5 h-1.5 bg-red-600 rounded-full"></span> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.clients.show', $client->userid) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View Profile">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            <a href="{{ route('admin.clients.edit', $client->userid) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit Client">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.clients.destroy', $client->userid) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this client?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete Client">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $clients->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('clientSearch');
    const searchDropdown = document.getElementById('searchDropdown');

    searchInput.addEventListener('input', async function() {
        const query = this.value.trim();
        
        if (query.length < 3) {
            searchDropdown.classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(`{{ route('admin.clients.search') }}?q=${encodeURIComponent(query)}`);
            const clients = await response.json();

            if (clients.length === 0) {
                searchDropdown.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500 text-center">No clients found</div>';
            } else {
                searchDropdown.innerHTML = clients.map(client => `
                    <a href="{{ route('admin.clients.show', ':id') }}" data-id="${client.userid}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-b-0 transition-colors">
                        <div class="font-medium text-slate-800 text-sm">${client.company}</div>
                        <div class="text-xs text-slate-500 truncate">
                            ${client.phonenumber || ''} ${client.phonenumber && client.address ? ' • ' : ''} ${client.address || ''}
                        </div>
                    </a>
                `).join('');

                // Replace :id placeholder with actual userid for each link
                searchDropdown.querySelectorAll('a').forEach(link => {
                    const id = link.getAttribute('data-id');
                    link.href = link.href.replace(':id', id);
                });
            }
            searchDropdown.classList.remove('hidden');
        } catch (error) {
            console.error('Search error:', error);
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.classList.add('hidden');
        }
    });
});
</script>
@endpush
