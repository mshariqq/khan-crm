@extends('layouts.admin')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Role</h1>
            <p class="text-slate-500 text-sm">Modify the name and permissions for this role.</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('admin.roles.update', $role->roleid) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-700">Role Name</label>
                <input type="text" name="name" value="{{ $role->name }}" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700 mb-4 block">Permissions</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php
                        $currentPermissions = $role->getPermissionSlugs();
                    @endphp
                    @foreach($availablePermissions as $permission)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:bg-slate-50 cursor-pointer transition-all group">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->slug }}" {{ in_array($permission->slug, $currentPermissions) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-slate-600 group-hover:text-slate-900">{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-6 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                    <i class="bi bi-save"></i> Update Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
