@extends('layouts.admin')

@section('breadcrumb', 'Staff List')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Staff Members</h1>
            <p class="text-slate-500 text-sm">Manage your team and their access levels.</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add Staff
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
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Staff Name</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Email</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Role</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($staff as $member)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->firstname . ' ' . $member->lastname) }}&background=random" 
                                     alt="Profile" class="w-8 h-8 rounded-full">
                                <span class="font-medium text-slate-800">{{ $member->firstname }} {{ $member->lastname }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $member->email }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">
                                {{ $member->staffRole->name ?? 'No Role' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($member->active)
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
                            <a href="{{ route('admin.staff.edit', $member->staffid) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit Staff">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.staff.destroy', $member->staffid) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Are you sure you want to delete this staff member?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete Staff">
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
        {{ $staff->links() }}
    </div>
</div>
@endsection
