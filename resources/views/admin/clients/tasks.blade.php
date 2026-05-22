@extends('layouts.admin')

@section('breadcrumb', 'Client Tasks')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="space-y-4">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Task Name</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Due Date</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $task)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-slate-800"><a href="{{ route('admin.tasks.show', $task->id) }}" data-pane="show" data-pane-id="{{ $task->id }}" class="hover:text-blue-600">{{ $task->name }}</a></td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $task->duedate ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">Status {{ $task->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="/admin/tasks/{{ $task->id }}" class="text-blue-600 hover:underline text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="bi bi-check2-square text-3xl"></i>
                                    <p>No tasks found yet</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
                <div class="mt-4">
                    {{ $data->appends(['tab' => 'tasks'])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
