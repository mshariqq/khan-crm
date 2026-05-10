@extends('layouts.admin')

@section('breadcrumb', 'Add New Staff')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Staff Member</h1>
            <p class="text-slate-500 text-sm">Create a new staff account and assign roles and departments.</p>
        </div>
        <a href="{{ route('admin.staff.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.staff.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Basic Information</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="firstname" value="{{ old('firstname') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('firstname') border-red-500 @enderror" placeholder="John" required>
                        @error('firstname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="lastname" value="{{ old('lastname') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('lastname') border-red-500 @enderror" placeholder="Doe" required>
                        @error('lastname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('email') border-red-500 @enderror" placeholder="john.doe@example.com" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('password') border-red-500 @enderror" placeholder="••••••••" required>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Account Settings</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Role <span class="text-red-500">*</span></label>
                        <select name="role" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('role') border-red-500 @enderror" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->roleid }}" {{ old('role') == $role->roleid ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Status <span class="text-red-500">*</span></label>
                        <select name="active" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('active') border-red-500 @enderror" required>
                            <option value="1" {{ old('active') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Departments</label>
                    <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50 rounded-lg border border-slate-200 max-h-40 overflow-y-auto">
                        @foreach($departments as $dept)
                            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                                <input type="checkbox" name="departments[]" value="{{ $dept->departmentid }}" class="rounded text-blue-600 focus:ring-blue-500" {{ is_array(old('departments')) && in_array($dept->departmentid, old('departments')) ? 'checked' : '' }}>
                                {{ $dept->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.staff.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Staff Member</button>
        </div>
    </form>
</div>
@endsection
