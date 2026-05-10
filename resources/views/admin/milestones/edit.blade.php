@extends('layouts.admin')

@section('breadcrumb', 'Edit Milestone')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Milestone</h1>
            <p class="text-slate-500 text-sm">Update milestone details.</p>
        </div>
        <a href="{{ route('admin.milestones.show', $milestone) }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to Detail
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.milestones.update', $milestone) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf @method('PUT')
        <div class="p-6 space-y-6">
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $milestone->name) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Project</label>
                <select name="project_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500" required>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ $milestone->project_id == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date', $milestone->due_date) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500">{{ old('description', $milestone->description) }}</textarea>
            </div>
        </div>
        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.milestones.show', $milestone) }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-all">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">Update Milestone</button>
        </div>
    </form>
</div>
@endsection