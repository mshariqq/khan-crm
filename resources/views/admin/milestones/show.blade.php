@extends('layouts.admin')

@section('breadcrumb', 'Milestone Detail')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">{{ $milestone->name }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.milestones.edit', $milestone) }}" class="px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-lg hover:bg-amber-600">Edit</a>
            <form action="{{ route('admin.milestones.destroy', $milestone) }}" method="POST" onsubmit="return confirm('Delete milestone?');" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-lg hover:bg-red-600">Delete</button>
            </form>
            <a href="{{ route('admin.milestones.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300">Back</a>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <p class="text-slate-600"><strong>Project:</strong> {{ $milestone->project->name ?? 'N/A' }}</p>
        <p class="text-slate-600"><strong>Deadline:</strong> {{ $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('M j, Y') : '-' }}</p>
        <p class="text-slate-600 mt-4"><strong>Description:</strong></p>
        <div class="mt-2 text-slate-700 whitespace-pre-wrap">{{ $milestone->description ?? 'No description.' }}</div>
    </div>
</div>
@endsection