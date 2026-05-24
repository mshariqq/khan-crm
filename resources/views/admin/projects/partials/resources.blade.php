<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Resources</h3>
    <button onclick="document.getElementById('inlineResourceForm').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Allocation
    </button>
</div>

<form id="inlineResourceForm" action="{{ route('admin.projects.resources.store', $project->id) }}" method="POST" class="hidden mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Staff</label>
            <select name="staff_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Select Staff</option>
                @foreach($staff ?? [] as $s)
                <option value="{{ $s->id ?? $s->staffid }}">{{ $s->firstname }} {{ $s->lastname }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Role</label>
            <input type="text" name="role" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Developer, Designer">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Allocation (%)</label>
            <input type="number" name="allocation_percentage" min="1" max="100" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
    </div>
    <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('inlineResourceForm').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800">Cancel</button>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Allocation</button>
    </div>
</form>

{{-- Utilization Summary --}}
@if(isset($utilization))
<div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
    <div class="p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
        <p class="text-[10px] text-blue-600 font-medium">Overall Utilization</p>
        <p class="text-xl font-bold text-blue-700">{{ $utilization['overall'] ?? 0 }}%</p>
    </div>
    <div class="p-3 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
        <p class="text-[10px] text-emerald-600 font-medium">Allocated Staff</p>
        <p class="text-xl font-bold text-emerald-700">{{ $utilization['total_allocated'] ?? 0 }}</p>
    </div>
    <div class="p-3 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl border border-amber-200">
        <p class="text-[10px] text-amber-600 font-medium">Total Allocation</p>
        <p class="text-xl font-bold text-amber-700">{{ $utilization['total_allocation'] ?? 0 }}%</p>
    </div>
    <div class="p-3 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
        <p class="text-[10px] text-purple-600 font-medium">Available Staff</p>
        <p class="text-xl font-bold text-purple-700">{{ $utilization['available_staff'] ?? 0 }}</p>
    </div>
</div>

@if(isset($utilization['per_staff']) && count($utilization['per_staff']) > 0)
<div class="mb-4 p-4 bg-slate-50 rounded-lg">
    <h4 class="text-xs font-semibold text-slate-700 mb-3">Per-Staff Utilization</h4>
    <div class="space-y-2">
        @foreach($utilization['per_staff'] as $staffUtil)
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-600 w-32 truncate">{{ $staffUtil['name'] }}</span>
            <div class="flex-1 h-2.5 bg-slate-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all {{ $staffUtil['percentage'] > 100 ? 'bg-red-500' : ($staffUtil['percentage'] > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min($staffUtil['percentage'], 100) }}%"></div>
            </div>
            <span class="text-xs font-medium {{ $staffUtil['percentage'] > 100 ? 'text-red-600' : ($staffUtil['percentage'] > 80 ? 'text-amber-600' : 'text-emerald-600') }}">{{ $staffUtil['percentage'] }}%</span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endif

{{-- Resource Allocation Table --}}
@if($project->resources->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-200">
                <th class="text-left py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Staff</th>
                <th class="text-left py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Allocation</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Allocated %</th>
                <th class="text-right py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($project->resources as $resource)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="py-2.5 px-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-400 flex items-center justify-center text-white text-[10px] font-semibold">
                            {{ substr($resource->staff->firstname ?? '?', 0, 1) }}
                        </span>
                        <span class="text-xs font-medium text-slate-700">{{ $resource->staff->firstname ?? '' }} {{ $resource->staff->lastname ?? '' }}</span>
                    </div>
                </td>
                <td class="py-2.5 px-3">
                    <span class="text-xs text-slate-600">{{ $resource->role ?? 'General' }}</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                    @php
                        $allocClass = $resource->allocation_percentage > 100 ? 'bg-red-100 text-red-700' : ($resource->allocation_percentage > 80 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
                    @endphp
                    <div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden mx-auto">
                        <div class="h-full rounded-full {{ $resource->allocation_percentage > 100 ? 'bg-red-500' : ($resource->allocation_percentage > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min($resource->allocation_percentage ?? 0, 100) }}%"></div>
                    </div>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <span class="text-xs font-medium {{ $allocClass }} px-2 py-0.5 rounded-full">{{ $resource->allocation_percentage ?? 0 }}%</span>
                </td>
                <td class="py-2.5 px-3 text-right">
                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <form action="{{ route('admin.projects.resources.destroy', [$project->id, $resource->id]) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Remove this allocation?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Remove"><i class="bi bi-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-people text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No resource allocations yet</p>
    <button onclick="document.getElementById('inlineResourceForm').classList.remove('hidden')" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Allocation</button>
</div>
@endif
