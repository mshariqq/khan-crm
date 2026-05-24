@php $recurring = $project->recurringConfig; @endphp

<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Recurring Configuration</h3>
    <button onclick="document.getElementById('recurringForm').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-gear"></i> {{ $recurring ? 'Edit Config' : 'Configure' }}
    </button>
</div>

@if($recurring)
<div class="mb-4 p-4 bg-emerald-50 rounded-lg border border-emerald-200">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-200 flex items-center justify-center">
                <i class="bi bi-arrow-repeat text-emerald-700 text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-emerald-800">
                    Repeats {{ ucfirst($recurring->recurring_type ?? 'monthly') }}
                    @if($recurring->repeat_every > 1)
                    (every {{ $recurring->repeat_every }})
                    @endif
                </p>
                @if($recurring->next_generation_date)
                <p class="text-[10px] text-emerald-600">Next generation: {{ $recurring->next_generation_date->format('M j, Y') }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2">
            @php
                $copied = [];
                if($recurring->copy_team) $copied[] = 'Team';
                if($recurring->copy_milestones) $copied[] = 'Milestones';
                if($recurring->copy_tasks) $copied[] = 'Tasks';
            @endphp
            @if(count($copied) > 0)
            <span class="text-[10px] text-emerald-600">Copies: {{ implode(', ', $copied) }}</span>
            @endif
            <form action="{{ route('admin.projects.recurring.deactivate', $project->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Deactivate recurring configuration?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                @csrf
                <button type="submit" class="px-2 py-1 text-[10px] bg-red-100 text-red-700 rounded-lg hover:bg-red-200 flex items-center gap-1">
                    <i class="bi bi-stop-circle"></i> Deactivate
                </button>
            </form>
        </div>
    </div>
</div>
@endif

<form id="recurringForm" action="{{ route('admin.projects.recurring.store', $project->id) }}" method="POST" class="{{ $recurring ? 'hidden' : '' }} mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Recurring Type</label>
            <select name="recurring_type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="daily" {{ $recurring && $recurring->recurring_type == 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ $recurring && $recurring->recurring_type == 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $recurring && $recurring->recurring_type == 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="yearly" {{ $recurring && $recurring->recurring_type == 'yearly' ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Repeat Every</label>
            <input type="number" name="repeat_every" min="1" value="{{ $recurring->repeat_every ?? 1 }}" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Max Occurrences</label>
            <input type="number" name="max_occurrences" min="0" value="{{ $recurring->max_occurrences ?? '' }}" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0 = unlimited">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Next Generation Date</label>
            <input type="date" name="next_generation_date" value="{{ $recurring?->next_generation_date?->format('Y-m-d') ?? '' }}" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-2">Copy on generation</label>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="checkbox" name="copy_team" value="1" {{ $recurring && $recurring->copy_team ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="text-xs text-slate-600">Copy Team</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="checkbox" name="copy_milestones" value="1" {{ $recurring && $recurring->copy_milestones ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="text-xs text-slate-600">Copy Milestones</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="checkbox" name="copy_tasks" value="1" {{ $recurring && $recurring->copy_tasks ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="text-xs text-slate-600">Copy Tasks</span>
            </label>
        </div>
    </div>
    <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="document.getElementById('recurringForm').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800">Cancel</button>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">{{ $recurring ? 'Update Config' : 'Save Config' }}</button>
    </div>
</form>

@if(!$recurring)
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-arrow-repeat text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No recurring configuration set</p>
    <button onclick="document.getElementById('recurringForm').classList.remove('hidden')" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Configure Now</button>
</div>
@endif
