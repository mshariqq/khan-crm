@extends('layouts.admin')

@section('breadcrumb', 'Invoice List')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Invoices</h1>
            <p class="text-slate-500 text-sm">Manage your billing, invoices, and payment records.</p>
        </div>
        <a href="{{ route('admin.invoices.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add Invoice
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

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Invoices</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $total }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="bi bi-receipt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Paid</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $paid }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <i class="bi bi-check-circle text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Unpaid</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $unpaid }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="bi bi-exclamation-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Overdue</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $overdue }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i class="bi bi-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('admin.invoices.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all w-48" placeholder="Invoice #...">
            </div>
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
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Unpaid</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Paid</option>
                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-all">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition-all">
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
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Due Date</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Amount</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($invoices as $invoice)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ $invoice->prefix ?? '' }}{{ $invoice->number }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $invoice->client->company ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($invoice->date)->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $invoice->duedate ? \Carbon\Carbon::parse($invoice->duedate)->format('M j, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-800 font-semibold">
                            {{ app_format_money($invoice->total, $invoice->currency) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusMap = [
                                    1 => ['label' => 'Unpaid', 'class' => 'bg-red-100 text-red-700'],
                                    2 => ['label' => 'Partially Paid', 'class' => 'bg-blue-100 text-blue-700'],
                                    3 => ['label' => 'Paid', 'class' => 'bg-emerald-100 text-emerald-700'],
                                    4 => ['label' => 'Cancelled', 'class' => 'bg-gray-100 text-gray-700'],
                                ];
                                $s = $statusMap[$invoice->status] ?? ['label' => 'Unknown', 'class' => 'bg-gray-100 text-gray-700'];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $s['class'] }}">{{ $s['label'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View Invoice">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="p-2 text-slate-400 hover:text-amber-600 transition-colors" title="Edit Invoice">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete Invoice">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <i class="bi bi-receipt text-4xl block mb-2"></i>
                            <p class="text-sm font-medium">No invoices found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($invoices->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection