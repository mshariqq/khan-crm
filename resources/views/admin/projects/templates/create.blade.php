@extends('layouts.admin')

@section('breadcrumb', 'Create Project Template')

@section('main')
<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Create Project Template</h1>
            <p class="text-slate-500 text-sm mt-1">Define a reusable project structure</p>
        </div>
        <a href="{{ route('admin.projects.templates.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.projects.templates.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Basic Details --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-2">Basic Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('name') border-red-500 @enderror" placeholder="e.g. Website Development" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Category</label>
                    <input type="text" name="category" value="{{ old('category') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="e.g. Web, Mobile, Design">
                    @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('description') border-red-500 @enderror" placeholder="Describe what this template is for...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Estimated Budget</label>
                <input type="number" step="0.01" name="estimated_budget" value="{{ old('estimated_budget') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                @error('estimated_budget') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Milestones --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-3">
            <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-2">Milestones</h3>
            <p class="text-xs text-slate-500">Enter one milestone per line: <code class="text-blue-600 bg-blue-50 px-1 rounded">Name | DaysFromStart</code></p>
            <textarea name="milestones" rows="4" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm" placeholder="Kickoff | 0&#10;Design Phase Complete | 14&#10;Development Complete | 42&#10;Launch | 60">{{ old('milestones') }}</textarea>
        </div>

        {{-- Tasks --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-3">
            <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-2">Tasks</h3>
            <p class="text-xs text-slate-500">Enter one task per line: <code class="text-blue-600 bg-blue-50 px-1 rounded">Name | Priority (low/medium/high) | EstimatedHours</code></p>
            <textarea name="tasks" rows="5" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm" placeholder="Set up repository | high | 4&#10;Design homepage | medium | 16&#10;Setup CI/CD | medium | 8&#10;Write tests | high | 12">{{ old('tasks') }}</textarea>
        </div>

        {{-- Team Roles --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-3">
            <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-2">Team Roles</h3>
            <p class="text-xs text-slate-500">Enter one role per line: <code class="text-blue-600 bg-blue-50 px-1 rounded">RoleName | Count</code></p>
            <textarea name="team_roles" rows="4" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm" placeholder="Project Manager | 1&#10;Developer | 3&#10;Designer | 1&#10;QA Engineer | 1">{{ old('team_roles') }}</textarea>
        </div>

        {{-- Checklists --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-3">
            <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-2">Checklists</h3>
            <p class="text-xs text-slate-500">Enter one checklist item per line. Group with <code class="text-blue-600 bg-blue-50 px-1 rounded">[Section Name]</code> headers.</p>
            <textarea name="checklists" rows="4" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm" placeholder="[Setup]&#10;Define project scope&#10;Set up hosting environment&#10;Configure domain&#10;&#10;[Launch]&#10;Final QA review&#10;Deploy to production&#10;Client sign-off">{{ old('checklists') }}</textarea>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-3">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm font-medium text-slate-600">Make this template publicly available</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.projects.templates.index') }}" class="px-6 py-2.5 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Template</button>
        </div>
    </form>
</div>
@endsection
