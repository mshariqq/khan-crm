@extends('layouts.admin')

@section('breadcrumb', 'Client Tasks')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-check2-square text-indigo-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Tasks</h2>
                    <p class="text-xs text-slate-500">{{ $data->total() }} records</p>
                </div>
            </div>
            <a href="{{ route('admin.tasks.create', ['rel_id' => $client->userid, 'rel_type' => 'client']) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> New Task
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Task Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @php
                        $taskStatusMap = [
                            0 => ['label' => 'Backlog', 'class' => 'bg-slate-100 text-slate-700'],
                            1 => ['label' => 'To Do', 'class' => 'bg-blue-100 text-blue-700'],
                            2 => ['label' => 'In Progress', 'class' => 'bg-amber-100 text-amber-700'],
                            3 => ['label' => 'Done', 'class' => 'bg-emerald-100 text-emerald-700'],
                        ];
                        $taskPriorityMap = [
                            1 => ['label' => 'Low', 'class' => 'bg-slate-100 text-slate-700'],
                            2 => ['label' => 'Medium', 'class' => 'bg-blue-100 text-blue-700'],
                            3 => ['label' => 'High', 'class' => 'bg-amber-100 text-amber-700'],
                            4 => ['label' => 'Urgent', 'class' => 'bg-red-100 text-red-700'],
                        ];
                    @endphp
                    @forelse($data as $task)
                        @php
                            $ts = $taskStatusMap[$task->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-700'];
                            $tp = $taskPriorityMap[$task->priority] ?? ['label' => 'Normal', 'class' => 'bg-slate-100 text-slate-700'];
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.tasks.show', $task->id) }}" class="text-sm font-semibold text-slate-800 hover:text-indigo-600">{{ $task->name }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $tp['class'] }}">{{ $tp['label'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm {{ $task->duedate && \Carbon\Carbon::parse($task->duedate)->isPast() && !in_array($task->status, [3, 0]) ? 'text-red-600 font-medium' : 'text-slate-600' }}">
                                {{ $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('M j, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $ts['class'] }}">{{ $ts['label'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.tasks.show', $task->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View Task">
                                    <i class="bi bi-eye text-lg"></i>
                                </a>
                                <a href="{{ route('admin.tasks.edit', $task->id) }}" class="p-2 text-slate-400 hover:text-amber-600 transition-colors" title="Edit Task">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-check2-square text-4xl block mb-2"></i>
                                <p class="text-sm font-medium">No tasks found</p>
                                <p class="text-xs mt-1">Create a task to track work items for this client.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($data->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $data->appends(['tab' => 'tasks'])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
