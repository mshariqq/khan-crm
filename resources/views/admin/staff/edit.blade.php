@extends('layouts.admin')

@section('breadcrumb', 'Edit Staff')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Staff Member</h1>
            <p class="text-slate-500 text-sm">Update details and permissions for {{ $staff->firstname }} {{ $staff->lastname }}.</p>
        </div>
        <a href="{{ route('admin.staff.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.staff.update', $staff->staffid) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Basic Information</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="firstname" value="{{ old('firstname', $staff->firstname) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('firstname') border-red-500 @enderror" required>
                        @error('firstname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="lastname" value="{{ old('lastname', $staff->lastname) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('lastname') border-red-500 @enderror" required>
                        @error('lastname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $staff->email) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('email') border-red-500 @enderror" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('password') border-red-500 @enderror" placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                @if(!$staff->email_verified_at)
                <div class="mt-4">
                    <a href="{{ route('admin.staff.send_verification', $staff->staffid) }}" class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline" onclick="event.preventDefault(); document.getElementById('send-verify-form').submit();">
                        <i class="bi bi-envelope-check"></i> Send verification email
                    </a>
                    <form id="send-verify-form" action="{{ route('admin.staff.send_verification', $staff->staffid) }}" method="POST" class="hidden">@csrf</form>
                </div>
                @else
                <div class="mt-4 text-sm text-green-600">
                    <i class="bi bi-check-circle"></i> Email verified {{ $staff->email_verified_at->diffForHumans() }}
                </div>
                @endif
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Account Settings</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Role <span class="text-red-500">*</span></label>
                        <select name="role" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('role') border-red-500 @enderror" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->roleid }}" {{ old('role', $staff->role) == $role->roleid ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Status <span class="text-red-500">*</span></label>
                        <select name="active" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('active') border-red-500 @enderror" required>
                            <option value="1" {{ old('active', $staff->active) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('active', $staff->active) == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Departments</label>
                    <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50 rounded-lg border border-slate-200 max-h-40 overflow-y-auto">
                        @foreach($departments as $dept)
                            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                                <input type="checkbox" name="departments[]" value="{{ $dept->departmentid }}" class="rounded text-blue-600 focus:ring-blue-500" {{ (is_array(old('departments')) && in_array($dept->departmentid, old('departments'))) || $staff->departments->contains($dept->departmentid) ? 'checked' : '' }}>
                                {{ $dept->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if($allPermissions->isNotEmpty())
        <div class="border-t border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Permission Overrides</h3>
            <p class="text-sm text-slate-500 mb-4">Check permissions to <strong class="text-red-600">deny</strong> them for this staff member (overrides role-granted permissions). Unchecked = inherited from role.</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach($allPermissions as $perm)
                    @php
                        $isDenied = in_array($perm->slug, $deniedPermissionSlugs);
                        $isRoleGranted = in_array($perm->slug, $rolePermissionSlugs);
                    @endphp
                    <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition-all {{ $isDenied ? 'bg-red-50 border-red-300' : ($isRoleGranted ? 'bg-green-50 border-green-200' : 'bg-slate-50 border-slate-200') }} hover:border-slate-300">
                        <input type="checkbox" name="denied_permissions[]" value="{{ $perm->slug }}" {{ $isDenied ? 'checked' : '' }} class="rounded text-red-600 focus:ring-red-500">
                        <span class="text-xs {{ $isDenied ? 'text-red-700 font-medium' : 'text-slate-600' }}">
                            {{ $perm->name }}
                            @if($isRoleGranted && !$isDenied)
                                <span class="text-green-500 ml-1">(role)</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.staff.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Staff Member</button>
        </div>
    </form>
</div>
@endsection
