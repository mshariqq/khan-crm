@extends('layouts.admin')

@section('breadcrumb', 'Subscriptions')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Subscriptions</h1>
            <p class="text-slate-500 text-sm">Manage recurring subscriptions for your clients.</p>
        </div>
        @can('subscriptions_create')
        <a href="{{ route('admin.subscriptions.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add Subscription
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium animate-fade-in">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium animate-fade-in">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-50 rounded-xl"><i class="bi bi-currency-exchange text-blue-600 text-xl"></i></div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Total Subscriptions</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $total }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-green-50 rounded-xl"><i class="bi bi-check-circle text-green-600 text-xl"></i></div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Active</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $activeCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-purple-50 rounded-xl"><i class="bi bi-cash-stack text-purple-600 text-xl"></i></div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Total Revenue</p>
                    <p class="text-2xl font-bold text-slate-800">{{ app_format_money($totalRevenue) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('admin.subscriptions.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Client</label>
                <select name="client" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->userid }}" {{ request('client') == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
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
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subscriptions..." class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-all">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition-all">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Name</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Client</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Next Billing</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Amount</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($subscriptions as $subscription)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="font-medium text-slate-800 hover:text-blue-600 transition-colors">{{ $subscription->name }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $subscription->client->company ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if($subscription->status)
                                <span class="flex items-center gap-1.5 text-green-600 text-xs font-bold">
                                    <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span> Active
                                </span>
                            @else
                                <span class="flex items-center gap-1.5 text-red-600 text-xs font-bold">
                                    <span class="w-1.5 h-1.5 bg-red-600 rounded-full"></span> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $subscription->next_billing_cycle ? \Carbon\Carbon::parse($subscription->next_billing_cycle)->format('M j, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-800">
                            {{ app_format_money($subscription->invoices->sum('total'), $subscription->currency) }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View"><i class="bi bi-eye text-lg"></i></a>
                            @can('subscriptions_edit')
                            <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit"><i class="bi bi-pencil-square text-lg"></i></a>
                            @endcan
                            @can('subscriptions_delete')
                            <form action="{{ route('admin.subscriptions.destroy', $subscription->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this subscription?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete"><i class="bi bi-trash text-lg"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-slate-500">No subscriptions found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">{{ $subscriptions->links() }}</div>
    </div>
</div>
@endSection