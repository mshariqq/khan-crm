@extends('layouts.admin')

@section('breadcrumb', 'Project Gantt Chart')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $project->name }} - Gantt Chart</h1>
            <p class="text-slate-500 text-sm mt-1">Visual timeline of project tasks and milestones</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.projects.show', $project) }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
            <button class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-download"></i> Export
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="bi bi-diagram-3 text-slate-300 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-semibold text-slate-800 mb-4">Interactive Gantt Chart</h3>
            <p class="text-slate-600 mb-6 max-w-2xl mx-auto">
                A fully interactive Gantt chart showing project timelines, task dependencies, and milestone markers would be displayed here. 
                This would include drag-and-drop scheduling, critical path analysis, and resource allocation visualization.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto mb-8">
                <div class="bg-slate-50 rounded-xl p-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-calendar3 text-blue-600 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-slate-800 mb-1">Timeline View</h4>
                    <p class="text-sm text-slate-600">Visual representation of project schedule</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-arrow-left-right text-indigo-600 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-slate-800 mb-1">Dependencies</h4>
                    <p class="text-sm text-slate-600">Track task relationships and dependencies</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-person-badge text-purple-600 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-slate-800 mb-1">Resource Allocation</h4>
                    <p class="text-sm text-slate-600">See team member workload and assignments</p>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 max-w-2xl mx-auto">
                <h4 class="text-lg font-semibold text-blue-900 mb-3">📊 Gantt Chart Features</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-check-circle-fill text-blue-500"></i>
                            <span>Drag & drop scheduling</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-check-circle-fill text-blue-500"></i>
                            <span>Task dependencies</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-check-circle-fill text-blue-500"></i>
                            <span>Progress tracking</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-check-circle-fill text-blue-500"></i>
                            <span>Critical path analysis</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-check-circle-fill text-blue-500"></i>
                            <span>Milestone markers</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-check-circle-fill text-blue-500"></i>
                            <span>Export capabilities</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($project->tasks->count() > 0)
            <div class="mt-8 text-left max-w-4xl mx-auto">
                <h4 class="text-lg font-semibold text-slate-800 mb-4">Project Data Summary</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <div class="text-2xl font-bold text-slate-800">{{ $project->tasks->count() }}</div>
                        <div class="text-sm text-slate-600">Total Tasks</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <div class="text-2xl font-bold text-slate-800">{{ $project->milestones->count() }}</div>
                        <div class="text-sm text-slate-600">Milestones</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <div class="text-2xl font-bold text-slate-800">
                            @php
                                $completed = $project->tasks->where('status', 3)->count();
                                $total = $project->tasks->count();
                                echo $total > 0 ? round(($completed / $total) * 100) : 0;
                            @endphp%
                        </div>
                        <div class="text-sm text-slate-600">Complete</div>
                    </div>
                </div>
            </div>
            @endif

            <div class="mt-8">
                <a href="{{ route('admin.projects.timesheets', $project) }}" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all inline-flex items-center gap-2">
                    <i class="bi bi-clock-history"></i> View Detailed Timesheets
                </a>
            </div>
        </div>
    </div>
</div>
@endsection