@extends('layouts.admin')

@section('breadcrumb', 'Expense List')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Expenses</h1>
            <p class="text-slate-500 text-sm">Manage company expenditures and billable expenses.</p>
        </div>
        <a href="{{ route('admin.expenses.create') }}" data-pane="create" data-pane-type="expense" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add Expense
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

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Expenses</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $total }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-receipt text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Billable</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $billableCount }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-check-circle text-green-600 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Non-Billable</p>
                    <p class="text-2xl font-bold text-slate-600 mt-1">{{ $nonBillableCount }}</p>
                </div>
                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-x-circle text-slate-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('admin.expenses.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
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
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Project</label>
                <select name="project" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Category</label>
                <select name="category" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-all">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('admin.expenses.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Expense Name</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Client</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Project</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Amount</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Date</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Category</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Billable</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($expenses as $expense)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.expenses.show', $expense->id) }}" data-pane="show" data-pane-type="expense" data-pane-id="{{ $expense->id }}" class="font-medium text-slate-800 hover:text-blue-600 transition-colors">{{ $expense->expense_name }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            @if($expense->client)
                            <a href="{{ route('admin.clients.show', $expense->client->userid) }}" class="hover:text-blue-600">{{ $expense->client->company }}</a>
                            @else
                            N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            @if($expense->project)
                            <a href="{{ route('admin.projects.show', $expense->project->id) }}" class="hover:text-blue-600">{{ $expense->project->name }}</a>
                            @else
                            N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 font-medium">
                            {{ app_format_money($expense->amount, $expense->currency) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($expense->date)->format('M j Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $expense->categoryRel->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($expense->billable)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Yes</span>
                            @else
                                <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-medium">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.expenses.show', $expense->id) }}" data-pane="show" data-pane-type="expense" data-pane-id="{{ $expense->id }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View Expense">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            <a href="{{ route('admin.expenses.edit', $expense->id) }}" data-pane="edit" data-pane-type="expense" data-pane-id="{{ $expense->id }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit Expense">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Are you sure you want to delete this expense?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete Expense">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $expenses->links() }}
        </div>
    </div>
</div>
@endsection