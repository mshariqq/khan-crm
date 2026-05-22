@extends('layouts.admin')

@section('breadcrumb', 'Milestones')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Milestones</h1>
        <a href="{{ route('admin.milestones.create') }}" data-pane="create" data-pane-type="milestone" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all">
            <i class="bi bi-plus-lg"></i> New Milestone
        </a>
    </div>
    @if(session('success'))
        <div class="mt-4 px-4 py-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-4">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Name</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Project</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Deadline</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($milestones as $ms)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4"><a href="{{ route('admin.milestones.show', $ms) }}" data-pane="show" data-pane-type="milestone" data-pane-id="{{ $ms->id }}" class="text-blue-600 hover:underline">{{ $ms->name }}</a></td>
                    <td class="px-6 py-4"><a href="{{ route('admin.projects.show', $ms->project->id) }}" class="text-blue-600 hover:underline">{{ $ms->project->name ?? 'N/A' }}</a></td>
                    <td class="px-6 py-4">{{ $ms->due_date ? \Carbon\Carbon::parse($ms->due_date)->format('M j, Y') : '-' }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.milestones.edit', $ms) }}" data-pane="edit" data-pane-type="milestone" data-pane-id="{{ $ms->id }}" class="text-slate-500 hover:text-blue-600 mr-2"><i class="bi bi-pencil-square"></i></a>
                        <form action="{{ route('admin.milestones.destroy', $ms) }}" method="POST" class="inline" onsubmit="return confirm('Delete milestone?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-4 text-center text-slate-500">No milestones found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">{{ $milestones->links() }}</div>
    </div>
</div>
@endsection