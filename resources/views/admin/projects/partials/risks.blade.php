<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Risks ({{ $project->risks->count() }})</h3>
    <button onclick="document.getElementById('inlineRiskForm').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Risk
    </button>
</div>

<form id="inlineRiskForm" action="{{ route('admin.projects.risks.store', $project->id) }}" method="POST" class="hidden mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Title</label>
            <input type="text" name="title" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
            <select name="type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="technical">Technical</option>
                <option value="schedule">Schedule</option>
                <option value="cost">Cost</option>
                <option value="resource">Resource</option>
                <option value="external">External</option>
                <option value="legal">Legal</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Probability</label>
            <select name="probability" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="1">Low (1)</option>
                <option value="2">Medium (2)</option>
                <option value="3">High (3)</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Impact</label>
            <select name="impact" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="1">Low (1)</option>
                <option value="2">Medium (2)</option>
                <option value="3">High (3)</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Assigned To</label>
            <select name="assigned_to" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Staff</option>
                @foreach($staff ?? [] as $s)
                <option value="{{ $s->id ?? $s->staffid }}">{{ $s->firstname }} {{ $s->lastname }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="identified">Identified</option>
                <option value="analyzing">Analyzing</option>
                <option value="mitigating">Mitigating</option>
                <option value="monitoring">Monitoring</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Mitigation Plan</label>
        <textarea name="mitigation" rows="2" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
    </div>
    <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('inlineRiskForm').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800">Cancel</button>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Risk</button>
    </div>
</form>

@if($project->risks->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-200">
                <th class="text-left py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Title</th>
                <th class="text-left py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Probability</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Impact</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Risk Score</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="text-left py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Assigned To</th>
                <th class="text-right py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($project->risks as $risk)
            @php
                $probScore = $risk->probability ?? 1;
                $impScore = $risk->impact ?? 1;
                $riskScore = $probScore * $impScore;
                $scoreColor = $riskScore >= 6 ? 'text-red-600' : ($riskScore >= 3 ? 'text-amber-600' : 'text-emerald-600');
                $scoreBg = $riskScore >= 6 ? 'bg-red-100' : ($riskScore >= 3 ? 'bg-amber-100' : 'bg-emerald-100');
            @endphp
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="py-2.5 px-3">
                    <span class="text-xs font-medium text-slate-700">{{ $risk->title }}</span>
                    @if($risk->mitigation)
                    <p class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[200px]">{{ $risk->mitigation }}</p>
                    @endif
                </td>
                <td class="py-2.5 px-3">
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 capitalize">{{ $risk->type ?? 'N/A' }}</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <span class="text-xs font-medium {{ $probScore == 3 ? 'text-red-600' : ($probScore == 2 ? 'text-amber-600' : 'text-emerald-600') }}">{{ $probScore }}</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <span class="text-xs font-medium {{ $impScore == 3 ? 'text-red-600' : ($impScore == 2 ? 'text-amber-600' : 'text-emerald-600') }}">{{ $impScore }}</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold {{ $scoreBg }} {{ $scoreColor }}">{{ $riskScore }}</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                        @if($risk->status == 'closed') bg-emerald-100 text-emerald-700
                        @elseif($risk->status == 'mitigating') bg-blue-100 text-blue-700
                        @elseif($risk->status == 'analyzing') bg-amber-100 text-amber-700
                        @else bg-slate-100 text-slate-600 @endif">
                        {{ ucfirst($risk->status ?? 'identified') }}
                    </span>
                </td>
                <td class="py-2.5 px-3">
                    @if($risk->assignedTo)
                    <span class="text-xs text-slate-600">{{ $risk->assignedTo->firstname }} {{ $risk->assignedTo->lastname }}</span>
                    @else
                    <span class="text-xs text-slate-400">Unassigned</span>
                    @endif
                </td>
                <td class="py-2.5 px-3 text-right">
                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="p-1 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="Edit"><i class="bi bi-pencil text-xs"></i></button>
                        <form action="{{ route('admin.projects.risks.destroy', [$project->id, $risk->id]) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this risk?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
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
        <i class="bi bi-exclamation-triangle text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No risks identified</p>
    <button onclick="document.getElementById('inlineRiskForm').classList.remove('hidden')" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Risk</button>
</div>
@endif
