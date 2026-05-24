@if($milestones->count() > 0)
<div class="space-y-2">
    @foreach($milestones as $milestone)
    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group">
        <div class="flex items-center gap-3">
            @php
                $milestoneTasks = $milestone->tasks ?? collect();
                $milestoneTotal = $milestoneTasks->count();
                $milestoneDone = $milestoneTasks->where('status', 3)->count();
                $milestonePct = $milestoneTotal > 0 ? round(($milestoneDone / $milestoneTotal) * 100) : 0;
            @endphp
            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $milestonePct == 100 ? 'bg-emerald-100' : ($milestonePct > 0 ? 'bg-amber-100' : 'bg-slate-100') }}">
                <i class="bi {{ $milestonePct == 100 ? 'bi-check-circle-fill text-emerald-600' : ($milestonePct > 0 ? 'bi-arrow-repeat text-amber-600' : 'bi-circle text-slate-400') }} text-sm"></i>
            </div>
            <div>
                <a href="{{ route('admin.milestones.show', $milestone->id) }}" data-pane="show" data-pane-type="milestone" data-pane-id="{{ $milestone->id }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">{{ $milestone->name }}</a>
                @if($milestone->due_date)
                <span class="text-[10px] ml-2 {{ $milestone->due_date->isPast() ? 'text-red-500' : 'text-slate-400' }}">
                    {{ $milestone->due_date->format('M j, Y') }}
                </span>
                @endif
                @if($milestoneTotal > 0)
                <span class="text-[10px] ml-1 text-slate-400">({{ $milestonePct }}%)</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($milestoneTotal > 0)
            <div class="w-16">
                <div class="flex justify-between text-[9px] text-slate-500 mb-0.5">
                    <span>{{ $milestoneDone }}/{{ $milestoneTotal }}</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-1">
                    <div class="h-1 rounded-full {{ $milestonePct == 100 ? 'bg-emerald-500' : ($milestonePct > 0 ? 'bg-amber-500' : 'bg-slate-300') }}" style="width: {{ $milestonePct }}%"></div>
                </div>
            </div>
            @endif
            @if($milestone->due_date)
            <span class="text-[10px] px-2 py-1 rounded-full {{ $milestone->due_date->isPast() && $milestonePct < 100 ? 'bg-red-100 text-red-700' : ($milestonePct == 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600') }}">
                @if($milestonePct == 100) Complete
                @elseif($milestone->due_date->isPast()) Overdue
                @else {{ $milestone->due_date->diffInDays(now()) }}d left @endif
            </span>
            @endif
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('admin.milestones.edit', $milestone->id) }}" data-pane="edit" data-pane-type="milestone" data-pane-id="{{ $milestone->id }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="Edit">
                    <i class="bi bi-pencil text-xs"></i>
                </a>
                <form action="{{ route('admin.milestones.destroy', $milestone->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Delete Milestone', 'Delete this milestone? This cannot be undone.', '{{ route('admin.milestones.destroy', $milestone->id) }}', 'DELETE');">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete">
                        <i class="bi bi-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-16">
    <div class="w-20 h-20 mx-auto mb-4 bg-slate-50 rounded-full flex items-center justify-center">
        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
    </div>
    <p class="text-sm font-medium text-slate-500 mb-1">No milestones</p>
    <p class="text-xs text-slate-400">Break your project into manageable milestones</p>
</div>
@endif

<div class="mt-4 pt-4 border-t border-slate-200">
    <a href="{{ route('admin.milestones.create') }}?project_id={{ $project->id }}" data-pane="create" data-pane-type="milestone" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
        <i class="bi bi-plus-lg"></i> Add Milestone
    </a>
</div>