@extends('layouts.admin')

@section('breadcrumb', 'Client Groups')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Client Groups</h1>
            <p class="text-slate-500 text-sm">Organize your clients into groups for better management.</p>
        </div>
        <a href="{{ route('admin.customer-groups.create') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add Group
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

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">ID</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Group Name</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($groups as $group)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $group->id }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $group->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.customer-groups.edit', $group->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit Group">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.customer-groups.destroy', $group->id) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Are you sure you want to delete this group?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete Group">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $groups->links() }}
        </div>
    </div>
</div>
@endsection
