@extends('layouts.admin')

@section('breadcrumb', 'Department List')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Departments</h1>
            <p class="text-slate-500 text-sm">Manage your company departments and email configurations.</p>
        </div>
        <a href="{{ route('admin.departments.create') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add Department
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
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Department Name</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Email</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Client Visibility</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($departments as $dept)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $dept->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $dept->email ?? 'No email' }}
                        </td>
                        <td class="px-6 py-4">
                            @if(!$dept->hidefromclient)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Visible</span>
                            @else
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">Hidden</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.departments.edit', $dept->departmentid) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit Department">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.departments.destroy', $dept->departmentid) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete Department">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $departments->links() }}
    </div>
</div>
@endsection
