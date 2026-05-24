<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Closure Checklist</h3>
    <button onclick="document.getElementById('inlineChecklistForm').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Item
    </button>
</div>

<form id="inlineChecklistForm" action="{{ route('admin.projects.closure-checklist.store', $project->id) }}" method="POST" class="hidden mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Item Name</label>
            <input type="text" name="name" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
            <select name="category" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="documentation">Documentation</option>
                <option value="financial">Financial</option>
                <option value="deliverables">Deliverables</option>
                <option value="resources">Resources</option>
                <option value="legal">Legal & Compliance</option>
                <option value="handover">Handover</option>
                <option value="other">Other</option>
            </select>
        </div>
    </div>
    <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('inlineChecklistForm').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800">Cancel</button>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Item</button>
    </div>
</form>

@php
    $items = $project->closureChecklist ?? collect();
    $totalItems = $items->count();
    $completedItems = $items->where('is_completed', true)->count();
    $progress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
    $categories = $items->groupBy('category');
@endphp

{{-- Progress Bar --}}
<div class="mb-4 p-4 bg-slate-50 rounded-lg">
    <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-slate-700">Completion Progress</span>
        <span class="text-xs font-bold {{ $progress == 100 ? 'text-emerald-600' : 'text-blue-600' }}">{{ $completedItems }}/{{ $totalItems }} ({{ $progress }}%)</span>
    </div>
    <div class="w-full h-3 bg-slate-200 rounded-full overflow-hidden">
        <div class="h-full rounded-full transition-all duration-500 {{ $progress == 100 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $progress }}%"></div>
    </div>
</div>

@if($items->count() > 0)
<div class="space-y-4">
    @foreach(['documentation', 'financial', 'deliverables', 'resources', 'legal', 'handover', 'other'] as $cat)
        @if(isset($categories[$cat]) && $categories[$cat]->count() > 0)
        <div>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 rounded-md bg-indigo-100 flex items-center justify-center">
                    <i class="bi 
                        @if($cat == 'documentation') bi-files
                        @elseif($cat == 'financial') bi-cash-stack
                        @elseif($cat == 'deliverables') bi-box-seam
                        @elseif($cat == 'resources') bi-people
                        @elseif($cat == 'legal') bi-shield-check
                        @elseif($cat == 'handover') bi-send
                        @else bi-three-dots
                        @endif text-indigo-600 text-xs"></i>
                </div>
                <span class="text-xs font-semibold text-slate-700 capitalize">{{ $cat }}</span>
                <span class="text-[10px] text-slate-400">{{ $categories[$cat]->where('is_completed', true)->count() }}/{{ $categories[$cat]->count() }}</span>
            </div>
            <div class="space-y-1">
                @foreach($categories[$cat] as $item)
                <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group">
                    <form action="{{ route('admin.projects.closure-checklist.toggle', [$project->id, $item->id]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition-colors {{ $item->is_completed ? 'bg-emerald-500 border-emerald-500' : 'border-slate-300 hover:border-blue-400' }}">
                            @if($item->is_completed)<i class="bi bi-check text-white text-[8px]"></i>@endif
                        </button>
                    </form>
                    <span class="text-xs {{ $item->is_completed ? 'text-slate-400 line-through' : 'text-slate-700' }} flex-1">{{ $item->name }}</span>
                    <form action="{{ route('admin.projects.closure-checklist.destroy', [$project->id, $item->id]) }}" method="POST" class="inline opacity-0 group-hover:opacity-100 transition-opacity">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete"><i class="bi bi-trash text-xs"></i></button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-list-check text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No checklist items yet</p>
    <button onclick="document.getElementById('inlineChecklistForm').classList.remove('hidden')" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Item</button>
</div>
@endif
