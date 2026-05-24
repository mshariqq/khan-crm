@extends('layouts.admin')

@section('breadcrumb', 'Project Calendar')

@section('main')
<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Project Calendar</h1>
            <p class="text-slate-500 text-sm mt-1">Deadlines, milestones, and tasks</p>
        </div>
    </div>

    @php
        $allItems = [];

        foreach ($projects as $project) {
            if ($project->deadline) {
                $allItems[] = [
                    'type' => 'project',
                    'title' => $project->name . ' Deadline',
                    'date' => $project->deadline,
                    'project_name' => $project->name,
                    'project_id' => $project->id,
                ];
            }
        }

        foreach ($milestones as $milestone) {
            if ($milestone->due_date) {
                $allItems[] = [
                    'type' => 'milestone',
                    'title' => $milestone->name,
                    'date' => $milestone->due_date,
                    'project_name' => $milestone->project->name ?? '—',
                    'project_id' => $milestone->project_id ?? null,
                ];
            }
        }

        foreach ($tasks as $task) {
            if ($task->duedate) {
                $allItems[] = [
                    'type' => 'task',
                    'title' => $task->name,
                    'date' => $task->duedate,
                    'project_name' => $task->project->name ?? '—',
                    'project_id' => $task->rel_id ?? null,
                ];
            }
        }

        usort($allItems, function($a, $b) {
            return $a['date']->timestamp - $b['date']->timestamp;
        });

        $grouped = [];
        foreach ($allItems as $item) {
            $key = $item['date']->format('F Y');
            $grouped[$key][] = $item;
        }
    @endphp

    {{-- Legend --}}
    <div class="flex items-center gap-6 text-xs text-slate-600">
        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Project Deadline</span>
        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-purple-500"></span> Milestone</span>
        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Task</span>
    </div>

    {{-- Calendar List --}}
    @forelse($grouped as $month => $items)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-800">{{ $month }}</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($items as $item)
            @php
                $isPast = $item['date']->isPast() && $item['type'] !== 'milestone';
                $typeStyles = [
                    'project' => 'border-l-blue-500 bg-blue-50',
                    'milestone' => 'border-l-purple-500 bg-purple-50',
                    'task' => 'border-l-emerald-500 bg-emerald-50',
                ];
                $typeIcons = [
                    'project' => 'bi-calendar-check text-blue-600',
                    'milestone' => 'bi-flag text-purple-600',
                    'task' => 'bi-list-check text-emerald-600',
                ];
                $style = $typeStyles[$item['type']] ?? 'border-l-slate-500 bg-slate-50';
                $icon = $typeIcons[$item['type']] ?? 'bi-circle';
            @endphp
            <div class="px-5 py-4 border-l-4 {{ $style }} {{ $isPast ? 'opacity-60' : '' }}">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 text-center">
                        <p class="text-xl font-bold {{ $isPast ? 'text-red-600' : 'text-slate-800' }}">{{ $item['date']->format('d') }}</p>
                        <p class="text-[10px] font-medium text-slate-500 uppercase">{{ $item['date']->format('D') }}</p>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <i class="{{ $icon }}"></i>
                            <span class="text-sm font-semibold {{ $isPast ? 'text-red-600' : 'text-slate-800' }}">{{ $item['title'] }}</span>
                            @if($isPast)
                            <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded">Overdue</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $item['project_name'] }}
                            @if($item['project_id'])
                            &middot; <a href="{{ route('admin.projects.show', $item['project_id']) }}" class="text-blue-600 hover:text-blue-700">View Project</a>
                            @endif
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-xs text-slate-400">
                        {{ $item['date']->format('g:i A') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-calendar text-slate-300 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 mb-2">No upcoming dates</h3>
        <p class="text-slate-500">All deadlines, milestones, and tasks with dates will appear here.</p>
    </div>
    @endforelse
</div>
@endsection
