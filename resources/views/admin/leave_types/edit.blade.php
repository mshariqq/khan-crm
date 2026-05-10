@extends('layouts.admin')

@section('breadcrumb', 'Edit Leave Type')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Leave Type</h1>
            <p class="text-slate-500 text-sm">Update the details of the leave type.</p>
        </div>
        <a href="{{ route('admin.leave-types.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.leave-types.update', $leaveType->id) }}" method="POST" class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Leave Type Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $leaveType->name) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('name') border-red-500 @enderror" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Default Annual Quota (Days) <span class="text-red-500">*</span></label>
                <input type="number" name="default_quota" value="{{ old('default_quota', $leaveType->default_quota) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('default_quota') border-red-500 @enderror" required>
                @error('default_quota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_paid" value="0">
                <input type="checkbox" name="is_paid" value="1" id="is_paid" {{ old('is_paid', $leaveType->is_paid) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="is_paid" class="text-sm font-medium text-slate-600">This is a paid leave</label>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.leave-types.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Type</button>
        </div>
    </form>
</div>
@endsection
