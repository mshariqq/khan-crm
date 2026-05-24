@extends('layouts.admin')

@section('breadcrumb', 'Estimate List')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Estimates</h1>
            <p class="text-slate-500 text-sm">Manage project estimates and quotations.</p>
        </div>
        <a href="{{ route('admin.estimates.create') }}" data-pane="create" data-pane-type="estimate" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add Estimate
        </a>
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

    <div class="grid grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Total</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $total }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600"><i class="bi bi-receipt text-lg"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Draft</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $draft }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600"><i class="bi bi-file-text text-lg"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Sent</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $sent }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600"><i class="bi bi-send text-lg"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Accepted</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $accepted }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600"><i class="bi bi-check-circle text-lg"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Declined</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $declined }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-600"><i class="bi bi-x-circle text-lg"></i></div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('admin.estimates.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
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
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Draft</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Sent</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Accepted</option>
                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Declined</option>
                    <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>Converted</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-all">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('admin.estimates.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">#</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Client</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Date</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Expiry</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Amount</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($estimates as $estimate)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $estimate->number }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $estimate->client->company ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($estimate->date)->format('M j Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $estimate->expirydate ? \Carbon\Carbon::parse($estimate->expirydate)->format('M j Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 font-medium">
                            {{ app_format_money($estimate->total, $estimate->currency) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusMap = [1 => ['label' => 'Draft', 'class' => 'bg-slate-100 text-slate-700'], 2 => ['label' => 'Sent', 'class' => 'bg-blue-100 text-blue-700'], 3 => ['label' => 'Accepted', 'class' => 'bg-green-100 text-green-700'], 4 => ['label' => 'Declined', 'class' => 'bg-red-100 text-red-700'], 5 => ['label' => 'Converted', 'class' => 'bg-purple-100 text-purple-700']];
                                $s = $statusMap[$estimate->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-700'];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $s['class'] }}">{{ $s['label'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.estimates.show', $estimate->id) }}" data-pane="show" data-pane-type="estimate" data-pane-id="{{ $estimate->id }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            <a href="{{ route('admin.estimates.edit', $estimate->id) }}" data-pane="edit" data-pane-type="estimate" data-pane-id="{{ $estimate->id }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.estimates.destroy', $estimate->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this estimate?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <i class="bi bi-receipt text-4xl block mb-2"></i>
                            <p class="text-sm font-medium">No estimates found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $estimates->links() }}
        </div>
    </div>
</div>
@endsection