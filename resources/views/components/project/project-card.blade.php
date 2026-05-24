@props(['project',
        'showClient' => true, 
        'showProgress' => true,
        'showActions' => false,
        'view' => 'grid'])

@php
    $statusColors = [
        0 => ['bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'text' => 'text-slate-800', 'badge' => 'bg-slate-100 text-slate-800'],
        1 => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'badge' => 'bg-blue-100 text-blue-800'],
        2 => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'badge' => 'bg-green-100 text-green-800'],
        3 => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'badge' => 'bg-amber-100 text-amber-800'],
        4 => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-800', 'badge' => 'bg-gray-100 text-gray-800']
    ];
    
    $statusLabels = [0 => 'Not Started', 1 => 'Planning', 2 => 'In Progress', 3 => 'On Hold', 4 => 'Completed'];
    
    $status = $project->status ?? 0;
    $colors = $statusColors[$status] ?? $statusColors[0];
    
    // Calculate completion based on tasks
    $totalTasks = $project->tasks->count();
    $completedTasks = $project->tasks->filter(function($task) {
        return $task->status == 3; // Done
    })->count();
    
    $completionPercentage = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
@endphp

<div class="group relative bg-white {{ $colors['bg'] }} {{ $colors['border'] }} border rounded-xl overflow-hidden hover:shadow-lg transition-all project-card-{{ $project->id }}"
     @if($view === 'grid') onclick="window.location='{{ route('admin.projects.show', $project) }}'" style="cursor: pointer;" @endif>
    
    {{-- Header Color Bar --}}
    <div class="h-1 bg-gradient-to-r from-blue-600 to-blue-700"></div>
    
    <div class="p-4">
        {{-- Project Title and Status --}}
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-semibold {{ $colors['text'] }} leading-tight mb-1 group-hover:text-blue-600 transition-colors">
                    {{ $project->name }}
                </h3>
                
                @if($showClient && $project->client)
                    <p class="text-sm text-slate-600 font-medium">
                        <i class="bi bi-person-circle text-slate-500 mr-1"></i> {{ $project->client->company ?? $project->client->firstname . ' ' . $project->client->lastname }}
                    </p>
                @endif
            </div>
            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $colors['badge'] }} ml-2 uppercase">
                {{ $statusLabels[$status] ?? 'Unknown' }}
            </span>
        </div>
        
        @if($showProgress && $totalTasks > 0)
            {{-- Progress Section --}}
            <div class="mb-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-500 font-medium">Progress</span>
                    <span class="text-xs font-bold {{ $colors['text'] }}">{{ round($completionPercentage) }}%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500 {{ $colors['border'] . ' bg-gradient-to-r from-blue-600 to-blue-700' }}" 
                         style="width: {{ $completionPercentage }}%"></div>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-xs text-slate-500">{{ $completedTasks }}/{{ $totalTasks }} tasks</span>
                    @if($project->deadline)
                        <span class="text-xs {{ $project->deadline->isPast() ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                            Due {{ $project->deadline->format('M d') }}
                        </span>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- Project Stats --}}
        <div class="grid grid-cols-3 gap-4 mb-3 pt-3 border-t border-slate-100">
            <div class="text-center">
                <div class="text-lg font-bold {{ $colors['text'] }}">{{ $project->tasks->count() }}</div>
                <div class="text-xs text-slate-500">Tasks</div>
            </div>
            <div class="text-center">
                <div class="text-lg font-bold {{ $colors['text'] }}">{{ $project->milestones->count() }}</div>
                <div class="text-xs text-slate-500">Milestones</div>
            </div>
            @if($project->project_cost)
                <div class="text-center">
                    <div class="text-lg font-bold {{ $colors['text'] }}">${{ number_format($project->project_cost, 0) }}</div>
                    <div class="text-xs text-slate-500">Budget</div>
                </div>
            @else
                <div class="text-center">
                    <div class="text-lg font-bold {{ $colors['text'] }}">{{ $project->staff->count() }}</div>
                    <div class="text-xs text-slate-500">Team</div>
                </div>
            @endif
        </div>
        
        {{-- Team Members --}}
        @if($project->staff->isNotEmpty())
            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    @foreach($project->staff->take(3) as $member)
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 border-2 border-white shadow-sm" 
                             title="{{ $member->full_name }}">
                            {{ substr($member->full_name, 0, 1) }}
                        </div>
                    @endforeach
                    @if($project->staff->count() > 3)
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-center border-2 border-white shadow-sm">
                            +{{ $project->staff->count() - 3 }}
                        </div>
                    @endif
                </div>
                <div class="text-xs text-slate-500">
                    {{ ($project->project_created ?? $project->created_at)->diffForHumans() }}
                </div>
            </div>
        @endif
    </div>
    
    {{-- Hover Actions --}}
    @if($showActions)
        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-all">
            <div class="flex items-center gap-1 bg-white rounded-lg border border-slate-200 shadow-sm p-1">
                <a href="{{ route('admin.projects.edit', $project) }}" 
                   class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                   title="Edit Project">
                    <i class="bi bi-pencil text-sm"></i>
                </a>
                <button type="button" 
                        onclick="if(confirm('Are you sure you want to delete this project?')) { document.getElementById('delete-project-{{ $project->id }}').submit(); }"
                        class="p-1.5 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                        title="Delete Project">
                    <i class="bi bi-trash text-sm"></i>
                </button>
                <form id="delete-project-{{ $project->id }}" 
                      action="{{ route('admin.projects.destroy', $project) }}" 
                      method="POST" 
                      class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .project-card-{{ $project->id }} {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .project-card-{{ $project->id }}:hover {
        transform: translateY(-4px);
    }
</style>
@endpush