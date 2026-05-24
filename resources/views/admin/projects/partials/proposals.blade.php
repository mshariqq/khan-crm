<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Proposals ({{ $proposals->count() }})</h3>
    <a href="{{ route('admin.proposals.create') }}?project_id={{ $project->id }}" data-pane="create" data-pane-type="proposal" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Proposal
    </a>
</div>

@if($proposals->count() > 0)
<div class="space-y-2">
    @foreach($proposals as $proposal)
    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center
                @if($proposal->status == 2) bg-emerald-100
                @elseif($proposal->status == 3) bg-red-100
                @elseif($proposal->status == 1) bg-blue-100
                @else bg-slate-200 @endif">
                <i class="bi bi-file-earmark-ruled text-sm
                    @if($proposal->status == 2) text-emerald-600
                    @elseif($proposal->status == 3) text-red-600
                    @elseif($proposal->status == 1) text-blue-600
                    @else text-slate-600 @endif"></i>
            </div>
            <div>
                <a href="{{ route('admin.proposals.show', $proposal->id) }}" data-pane="show" data-pane-type="proposal" data-pane-id="{{ $proposal->id }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">{{ $proposal->subject }}</a>
                <span class="text-[10px] text-slate-400 ml-2">{{ $proposal->datecreated ? \Carbon\Carbon::parse($proposal->datecreated)->format('M j, Y') : '' }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <span class="text-sm font-bold text-slate-700">{{ app_format_money($proposal->total, $proposal->currency) }}</span>
                <span class="text-[10px] block px-2 py-0.5 rounded-full mt-1
                    @if($proposal->status == 2) bg-emerald-100 text-emerald-700
                    @elseif($proposal->status == 3) bg-red-100 text-red-700
                    @elseif($proposal->status == 1) bg-blue-100 text-blue-700
                    @else bg-slate-100 text-slate-600 @endif">
                    @if($proposal->status == 2) Accepted
                    @elseif($proposal->status == 3) Declined
                    @elseif($proposal->status == 1) Sent
                    @else Draft @endif
                </span>
            </div>
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('admin.proposals.show', $proposal->id) }}" data-pane="show" data-pane-type="proposal" data-pane-id="{{ $proposal->id }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="View">
                    <i class="bi bi-eye text-xs"></i>
                </a>
                <a href="{{ route('admin.proposals.edit', $proposal->id) }}" data-pane="edit" data-pane-type="proposal" data-pane-id="{{ $proposal->id }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="Edit">
                    <i class="bi bi-pencil text-xs"></i>
                </a>
                <form action="{{ route('admin.proposals.destroy', $proposal->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this proposal?');">
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
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-file-earmark-ruled text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No proposals yet</p>
    <a href="{{ route('admin.proposals.create') }}?project_id={{ $project->id }}" data-pane="create" data-pane-type="proposal" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Proposal</a>
</div>
@endif