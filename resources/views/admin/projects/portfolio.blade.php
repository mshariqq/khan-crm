@extends('layouts.admin')

@section('breadcrumb', 'Portfolio')

@section('main')
<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Project Portfolio</h1>
            <p class="text-slate-500 text-sm mt-1">Health overview of all projects</p>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Projects</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $health->total ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-folder text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">On Track</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $health->on_track ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-check-circle text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Warning</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $health->warning ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-exclamation-triangle text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">At Risk</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">{{ $health->at_risk ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-exclamation-circle text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Critical</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $health->critical ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-x-octagon text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- More Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-slate-500">Health Distribution</p>
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-pie-chart text-purple-600"></i>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach(\App\Models\Project::healthLabels() as $key => $label)
                    @php
                        $count = $health->$key ?? 0;
                        $color = \App\Models\Project::healthColors()[$key] ?? 'bg-slate-100 text-slate-600';
                    @endphp
                    @if($count > 0)
                    <span class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $color }}">{{ $label }}: {{ $count }}</span>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-slate-500">Budget Utilization</p>
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-cash-stack text-blue-600"></i>
                </div>
            </div>
            @php $budgetUtil = $health->budget_utilization ?? 0; @endphp
            <p class="text-2xl font-bold text-slate-800">{{ number_format($budgetUtil, 1) }}%</p>
            <div class="mt-2 w-full bg-slate-200 rounded-full h-2">
                <div class="h-2 rounded-full {{ $budgetUtil > 80 ? 'bg-red-500' : ($budgetUtil > 60 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min($budgetUtil, 100) }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-slate-500">Task Completion</p>
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-list-check text-indigo-600"></i>
                </div>
            </div>
            @php $taskPct = $health->task_completion ?? 0; @endphp
            <p class="text-2xl font-bold text-slate-800">{{ number_format($taskPct, 1) }}%</p>
            <div class="mt-2 w-full bg-slate-200 rounded-full h-2">
                <div class="h-2 rounded-full bg-indigo-500" style="width: {{ min($taskPct, 100) }}%"></div>
            </div>
        </div>
    </div>

    {{-- Projects Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-800">All Projects</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs text-slate-500 border-b border-slate-100">
                        <th class="text-left px-5 py-3 font-semibold">Project</th>
                        <th class="text-left px-5 py-3 font-semibold">Client</th>
                        <th class="text-left px-5 py-3 font-semibold">Health</th>
                        <th class="text-right px-5 py-3 font-semibold">Progress</th>
                        <th class="text-right px-5 py-3 font-semibold">Deadline</th>
                        <th class="text-right px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($projects as $project)
                    @php
                        $healthKey = $project->healthStatus();
                        $dotColor = \App\Models\Project::healthDotColors()[$healthKey] ?? 'bg-slate-500';
                        $badgeColor = \App\Models\Project::healthColors()[$healthKey] ?? 'bg-slate-100 text-slate-600';
                        $label = \App\Models\Project::healthLabels()[$healthKey] ?? 'Unknown';
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                                <a href="{{ route('admin.projects.show', $project->id) }}" class="text-sm font-semibold text-slate-800 hover:text-blue-600">{{ $project->name }}</a>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $project->client->company ?? '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $badgeColor }}">{{ $label }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <span class="text-sm font-semibold text-slate-700">{{ $project->progress }}%</span>
                                <div class="w-20 bg-slate-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $project->progress == 100 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $project->progress }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right text-sm {{ $project->deadline?->isPast() ? 'text-red-600 font-medium' : 'text-slate-600' }}">
                            {{ $project->deadline?->format('M j, Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.projects.show', $project->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.projects.edit', $project->id) }}" class="p-1.5 text-slate-400 hover:text-amber-600 transition-colors" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                            <i class="bi bi-folder text-2xl mb-2 block"></i>
                            <p class="text-sm">No projects found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
