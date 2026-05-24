<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Change Requests ({{ $project->changeRequests->count() }})</h3>
    <button onclick="document.getElementById('inlineCrForm').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> New Request
    </button>
</div>

<form id="inlineCrForm" action="{{ route('admin.projects.change-requests.store', $project->id) }}" method="POST" class="hidden mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Title</label>
            <input type="text" name="title" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Priority</label>
            <select name="priority" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Estimated Cost ($)</label>
            <input type="number" step="0.01" name="estimated_cost" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Estimated Days</label>
            <input type="number" name="estimated_days" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Description</label>
        <textarea name="description" rows="3" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required></textarea>
    </div>
    <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('inlineCrForm').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800">Cancel</button>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Submit Request</button>
    </div>
</form>

@if($project->changeRequests->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-200">
                <th class="text-left py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Title</th>
                <th class="text-left py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Submitter</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Priority</th>
                <th class="text-right py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Est. Cost</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Est. Days</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="text-right py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($project->changeRequests as $cr)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="py-2.5 px-3">
                    <div>
                        <span class="text-xs font-medium text-slate-700">{{ $cr->title }}</span>
                        @if($cr->description)
                        <p class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[250px]">{{ $cr->description }}</p>
                        @endif
                    </div>
                </td>
                <td class="py-2.5 px-3">
                    @if($cr->submitter)
                    <span class="text-xs text-slate-600">{{ $cr->submitter->firstname }} {{ $cr->submitter->lastname }}</span>
                    @else
                    <span class="text-xs text-slate-400">N/A</span>
                    @endif
                </td>
                <td class="py-2.5 px-3 text-center">
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                        @if($cr->priority == 'critical') bg-red-100 text-red-700
                        @elseif($cr->priority == 'high') bg-amber-100 text-amber-700
                        @elseif($cr->priority == 'medium') bg-blue-100 text-blue-700
                        @else bg-slate-100 text-slate-600 @endif">
                        {{ ucfirst($cr->priority ?? 'medium') }}
                    </span>
                </td>
                <td class="py-2.5 px-3 text-right text-xs font-medium text-slate-700">${{ number_format($cr->estimated_cost ?? 0, 2) }}</td>
                <td class="py-2.5 px-3 text-center text-xs text-slate-600">{{ $cr->estimated_days ?? '-' }}</td>
                <td class="py-2.5 px-3 text-center">
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                        @if($cr->status == 'approved') bg-emerald-100 text-emerald-700
                        @elseif($cr->status == 'rejected') bg-red-100 text-red-700
                        @elseif($cr->status == 'implemented') bg-blue-100 text-blue-700
                        @elseif($cr->status == 'under_review') bg-amber-100 text-amber-700
                        @else bg-slate-100 text-slate-600 @endif">
                        {{ ucfirst(str_replace('_', ' ', $cr->status ?? 'submitted')) }}
                    </span>
                </td>
                <td class="py-2.5 px-3 text-right">
                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        @if(in_array($cr->status, ['submitted', 'under_review']))
                        <form action="{{ route('admin.projects.change-requests.approve', [$project->id, $cr->id]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-1 text-emerald-500 hover:text-emerald-700 rounded hover:bg-emerald-50" title="Approve"><i class="bi bi-check-lg text-xs"></i></button>
                        </form>
                        <form action="{{ route('admin.projects.change-requests.reject', [$project->id, $cr->id]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-1 text-red-500 hover:text-red-700 rounded hover:bg-red-50" title="Reject"><i class="bi bi-x-lg text-xs"></i></button>
                        </form>
                        @endif
                        <form action="{{ route('admin.projects.change-requests.destroy', [$project->id, $cr->id]) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this change request?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete"><i class="bi bi-trash text-xs"></i></button>
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
        <i class="bi bi-arrow-left-right text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No change requests yet</p>
    <button onclick="document.getElementById('inlineCrForm').classList.remove('hidden')" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Submit Request</button>
</div>
@endif
