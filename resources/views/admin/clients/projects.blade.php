@extends('layouts.admin')

@section('breadcrumb', 'Client Projects')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-violet-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-kanban text-violet-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Projects</h2>
                    <p class="text-xs text-slate-500">{{ $data->total() }} records</p>
                </div>
            </div>
            <a href="{{ route('admin.projects.create', ['client_id' => $client->userid]) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> New Project
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Project Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Deadline</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @php
                        $projectStatusMap = [
                            1 => ['label' => 'Active', 'class' => 'bg-emerald-100 text-emerald-700'],
                            2 => ['label' => 'Completed', 'class' => 'bg-indigo-100 text-indigo-700'],
                            3 => ['label' => 'On Hold', 'class' => 'bg-amber-100 text-amber-700'],
                        ];
                    @endphp
                    @forelse($data as $project)
                        @php $ps = $projectStatusMap[$project->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-700']; @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.projects.show', $project->id) }}" class="text-sm font-semibold text-slate-800 hover:text-blue-600">{{ $project->name }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $ps['class'] }}">{{ $ps['label'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M j, Y') : '—' }}</td>
                            <td class="px-6 py-4 text-sm {{ $project->deadline && \Carbon\Carbon::parse($project->deadline)->isPast() && $project->status == 1 ? 'text-red-600 font-medium' : 'text-slate-600' }}">
                                {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M j, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 max-w-[100px] bg-slate-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full @if($project->progress == 100) bg-emerald-500 @elseif($project->progress >= 50) bg-blue-500 @else bg-amber-500 @endif" style="width: {{ $project->progress }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-slate-600">{{ $project->progress }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.projects.show', $project->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View Project">
                                    <i class="bi bi-eye text-lg"></i>
                                </a>
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="p-2 text-slate-400 hover:text-amber-600 transition-colors" title="Edit Project">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-kanban text-4xl block mb-2"></i>
                                <p class="text-sm font-medium">No projects found</p>
                                <p class="text-xs mt-1">Create a project to track work for this client.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($data->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $data->appends(['tab' => 'projects'])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
