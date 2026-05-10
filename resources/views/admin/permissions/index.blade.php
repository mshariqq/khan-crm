@extends('layouts.admin')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">System Permissions</h1>
            <p class="text-slate-500 text-sm">Manage the granular permissions available in the system.</p>
        </div>
        <a href="{{ route('admin.permissions.create') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Create New Permission
        </a>
    </div>

    @if(session('success'))
        <div class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Permission Name</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Slug</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($permissions as $permission)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $permission->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <code class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-mono">{{ $permission->slug }}</code>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit Permission">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this permission? This might affect roles that use it.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete Permission">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
