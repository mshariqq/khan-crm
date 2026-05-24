@extends('layouts.client')

@section('title', 'Milestones - ' . $project->name)
@section('breadcrumb', 'Project Milestones')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.projects.show', $project) }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Milestones</h1>
                <p class="text-sm text-slate-500">{{ $project->name }}</p>
            </div>
        </div>
    </div>

    @if($milestones->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($milestones as $milestone)
        @php
            $isOverdue = $milestone->due_date && \Carbon\Carbon::parse($milestone->due_date)->isPast();
            $taskCount = $project->tasks->where('milestone', $milestone->id)->count();
            $completedTasks = $project->tasks->where('milestone', $milestone->id)->where('status', 3)->count();
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-all">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg {{ $isOverdue ? 'bg-red-100' : 'bg-indigo-100' }} flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-flag text-{{ $isOverdue ? 'red' : 'indigo' }}-600 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-bold text-slate-800 truncate">{{ $milestone->name }}</h3>
                    @if($milestone->due_date)
                    <p class="text-xs {{ $isOverdue ? 'text-red-500' : 'text-slate-400' }} mt-0.5">
                        <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                    </p>
                    @endif
                </div>
            </div>

            @if($milestone->description)
            <p class="text-xs text-slate-600 leading-relaxed line-clamp-2 mb-3">{{ $milestone->description }}</p>
            @endif

            @if($taskCount > 0)
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span>{{ $completedTasks }}/{{ $taskCount }} tasks completed</span>
                <span>{{ $taskCount }} tasks</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full bg-indigo-500" style="width: {{ $taskCount > 0 ? round(($completedTasks / $taskCount) * 100) : 0 }}%"></div>
            </div>
            @else
            <p class="text-xs text-slate-400">No tasks assigned</p>
            @endif

            @if($isOverdue)
            <div class="mt-3">
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-600">
                    Overdue by {{ \Carbon\Carbon::parse($milestone->due_date)->diffInDays(now()) }} days
                </span>
            </div>
            @elseif($milestone->due_date)
            <div class="mt-3">
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                    {{ \Carbon\Carbon::parse($milestone->due_date)->diffInDays(now()) }} days left
                </span>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-dashed border-slate-300">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
            <i class="bi bi-flag text-3xl text-slate-300"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800">No milestones yet</h3>
        <p class="text-slate-500">No milestones have been created for this project yet.</p>
    </div>
    @endif
</div>
@endsection
