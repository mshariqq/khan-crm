@extends('layouts.admin')

@section('breadcrumb', 'Proposal List')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Proposals</h1>
            <p class="text-slate-500 text-sm">Manage your client proposals and quotes.</p>
        </div>
        <a href="{{ route('admin.proposals.create') }}" data-pane="create" data-pane-type="proposal" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add Proposal
        </a>
    </div>

    @if(session('success'))
        <div class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Total</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $total }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600"><i class="bi bi-file-earmark-text text-lg"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Open</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $open }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600"><i class="bi bi-envelope-open text-lg"></i></div>
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
        <form action="{{ route('admin.proposals.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Status</label>
                <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Open</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Accepted</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Declined</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subject or client..." class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all w-64">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-all">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('admin.proposals.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Subject</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">To</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Date</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Open Till</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Total</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($proposals as $proposal)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $proposal->subject }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $proposal->proposal_to }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($proposal->date)->format('M j Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $proposal->open_till ? \Carbon\Carbon::parse($proposal->open_till)->format('M j Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 font-medium">
                            {{ app_format_money($proposal->total, $proposal->currency) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $pStatusMap = [1 => ['label' => 'Open', 'class' => 'bg-blue-100 text-blue-700'], 2 => ['label' => 'Accepted', 'class' => 'bg-green-100 text-green-700'], 3 => ['label' => 'Declined', 'class' => 'bg-red-100 text-red-700']];
                                $ps = $pStatusMap[$proposal->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-700'];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $ps['class'] }}">{{ $ps['label'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.proposals.show', $proposal->id) }}" data-pane="show" data-pane-type="proposal" data-pane-id="{{ $proposal->id }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            <a href="{{ route('admin.proposals.edit', $proposal->id) }}" data-pane="edit" data-pane-type="proposal" data-pane-id="{{ $proposal->id }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.proposals.destroy', $proposal->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this proposal?');">
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
                            <i class="bi bi-file-earmark-text text-4xl block mb-2"></i>
                            <p class="text-sm font-medium">No proposals found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $proposals->links() }}
        </div>
    </div>
</div>
@endsection