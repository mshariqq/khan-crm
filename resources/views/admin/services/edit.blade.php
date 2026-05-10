@extends('layouts.admin')

@section('breadcrumb', 'Edit Service')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Service</h1>
            <p class="text-slate-500 text-sm">Update the details of {{ $service->name }}.</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.services.update', $service->serviceid) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Service Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $service->name) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('name') border-red-500 @enderror" placeholder="Web Development" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.services.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Service</button>
        </div>
    </form>
</div>
@endsection
