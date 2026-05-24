@extends('layouts.admin')

@section('breadcrumb', 'Item Groups')

@section('main')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Item Groups</h1>
            <p class="text-slate-500 text-sm mt-1">Categorize your items into groups</p>
        </div>
        <a href="{{ route('admin.itemsgroups.create') }}" data-pane="create" data-pane-type="itemsgroup" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <i class="bi bi-plus-lg"></i>
            Add Group
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Group Name</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($groups as $group)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold">
                                        {{ strtoupper(substr($group->name, 0, 1)) }}
                                    </span>
                                    <span data-pane="show" data-pane-type="itemsgroup" data-pane-id="{{ $group->id }}" class="font-medium text-slate-800 hover:text-blue-600 transition-colors cursor-pointer">{{ $group->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.itemsgroups.edit', $group->id) }}" data-pane="edit" data-pane-type="itemsgroup" data-pane-id="{{ $group->id }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.itemsgroups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Delete this group?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-12 text-center text-slate-500">No item groups found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($groups->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $groups->links() }}
            </div>
        @endif
    </div>
</div>
@endsection