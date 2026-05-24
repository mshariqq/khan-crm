@extends('layouts.admin')

@section('breadcrumb', 'Email Template List')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Email Templates</h1>
            <p class="text-slate-500 text-sm">Manage system email templates for automated communications.</p>
        </div>
        <a href="{{ route('admin.emailtemplates.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add Template
        </a>
    </div>

    @if(session('success'))
        <div class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('admin.emailtemplates.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Template Type</label>
                <select name="type" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">All Types</option>
                    <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System</option>
                    <option value="marketing" {{ request('type') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                    <option value="transactional" {{ request('type') == 'transactional' ? 'selected' : '' }}>Transactional</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or subject..." class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-all">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('admin.emailtemplates.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Template Name</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Subject</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Slug</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Type</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($templates as $template)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $template->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $template->subject }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            <code>{{ $template->slug }}</code>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-medium">
                                {{ ucfirst($template->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($template->active)
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
                            <a href="{{ route('admin.emailtemplates.show', $template->emailtemplateid) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View Template">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            <a href="{{ route('admin.emailtemplates.edit', $template->emailtemplateid) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit Template">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.emailtemplates.destroy', $template->emailtemplateid) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Are you sure you want to delete this email template?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete Template">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $templates->links() }}
        </div>
    </div>
</div>
@endsection
