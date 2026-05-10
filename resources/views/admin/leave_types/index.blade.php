@extends('layouts.admin')

@section('breadcrumb', 'Leave Types')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Leave Types</h1>
            <p class="text-slate-500 text-sm">Manage the types of leaves available in the company.</p>
        </div>
        <a href="{{ route('admin.leave-types.create') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20">
            <i class="bi bi-plus-lg"></i> Add Type
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Type Name</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Default Quota (Days)</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Paid Leave</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($leaveTypes as $type)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                {{ substr($type->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-slate-800">{{ $type->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $type->default_quota }} days
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                            {{ $type->is_paid ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $type->is_paid ? 'Paid' : 'Unpaid' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.leave-types.edit', $type->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="{{ route('admin.leave-types.destroy', $type->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" onclick="return confirm('Are you sure you want to delete this leave type?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-folder-x text-3xl mb-2"></i>
                            <p class="text-sm">No leave types found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="flex justify-center">
        {{ $leaveTypes->links() }}
    </div>
</div>
@endsection
