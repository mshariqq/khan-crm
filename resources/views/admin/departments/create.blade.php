@extends('layouts.admin')

@section('breadcrumb', 'Add Department')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Department</h1>
            <p class="text-slate-500 text-sm">Define a new department for ticket routing and staff organization.</p>
        </div>
        <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.departments.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Department Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('name') border-red-500 @enderror" placeholder="e.g. Support, Sales" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Department Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('email') border-red-500 @enderror" placeholder="support@example.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                <input type="checkbox" name="hidefromclient" id="hidefromclient" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" {{ old('hidefromclient') ? 'checked' : '' }}>
                <label for="hidefromclient" class="text-sm font-medium text-slate-600 cursor-pointer">
                    Hide this department from clients
                </label>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.departments.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Department</button>
        </div>
    </form>
</div>
@endsection
