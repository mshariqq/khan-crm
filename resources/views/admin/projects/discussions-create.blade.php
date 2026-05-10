@extends('layouts.admin')

@section('breadcrumb', 'Start Discussion')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Start New Discussion</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $project->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.projects.discussions', $project) }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to Discussions
            </a>
            <a href="{{ route('admin.projects.show', $project) }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2">
                <i class="bi bi-folder2"></i> Project Overview
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('admin.projects.discussions.store', $project) }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Discussion Subject</label>
                    <input type="text" name="subject" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter discussion subject..." required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Description</label>
                    <textarea name="description" rows="6" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" placeholder="Describe what this discussion is about..."></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all">
                        <i class="bi bi-send"></i> Start Discussion
                    </button>
                    <button type="button" class="px-6 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection