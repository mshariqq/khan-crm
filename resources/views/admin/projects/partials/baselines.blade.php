<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Baselines ({{ $project->baselines->count() }})</h3>
    <form action="{{ route('admin.projects.baselines.store', $project->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Capture current project state as a new baseline?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
        @csrf
        <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
            <i class="bi bi-plus"></i> Capture Baseline
        </button>
    </form>
</div>

@if($project->baselines->count() > 0)
<div class="space-y-4">
    @foreach($project->baselines as $baseline)
    @php $isActive = $baseline->is_active ?? false; @endphp
    <div class="p-4 {{ $isActive ? 'bg-blue-50 border-blue-300 ring-1 ring-blue-200' : 'bg-white border-slate-200' }} rounded-lg border transition-colors">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg {{ $isActive ? 'bg-blue-200' : 'bg-slate-100' }} flex items-center justify-center">
                    <i class="bi {{ $isActive ? 'bi-bookmark-check-fill text-blue-600' : 'bi-bookmark text-slate-500' }} text-sm"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold {{ $isActive ? 'text-blue-800' : 'text-slate-800' }}">Baseline #{{ $baseline->version ?? $loop->iteration }}</h4>
                    <p class="text-[10px] {{ $isActive ? 'text-blue-500' : 'text-slate-400' }}">{{ $baseline->created_at?->format('M j, Y g:i A') ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($isActive)
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">Active</span>
                @endif
                @if(!$isActive)
                <form action="{{ route('admin.projects.baselines.set-active', [$project->id, $baseline->id]) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-[10px] px-2 py-1 text-blue-600 hover:text-blue-800 font-medium">Set Active</button>
                </form>
                @endif
            </div>
        </div>

        @php
            $pv = $baseline->planned_value ?? 0;
            $ev = $baseline->earned_value ?? 0;
            $ac = $baseline->actual_cost ?? 0;
            $sv = $ev - $pv;
            $cv = $ev - $ac;
            $spi = $pv > 0 ? round($ev / $pv, 2) : 0;
            $cpi = $ac > 0 ? round($ev / $ac, 2) : 0;
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <div class="p-2 bg-white rounded-lg border border-slate-100">
                <p class="text-[10px] text-slate-400">Planned Value (PV)</p>
                <p class="text-sm font-bold text-slate-700">${{ number_format($pv, 2) }}</p>
            </div>
            <div class="p-2 bg-white rounded-lg border border-slate-100">
                <p class="text-[10px] text-slate-400">Earned Value (EV)</p>
                <p class="text-sm font-bold text-slate-700">${{ number_format($ev, 2) }}</p>
            </div>
            <div class="p-2 bg-white rounded-lg border border-slate-100">
                <p class="text-[10px] text-slate-400">Actual Cost (AC)</p>
                <p class="text-sm font-bold text-slate-700">${{ number_format($ac, 2) }}</p>
            </div>
            <div class="p-2 bg-white rounded-lg border border-slate-100">
                <p class="text-[10px] text-slate-400">Schedule Variance (SV)</p>
                <p class="text-sm font-bold {{ $sv >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $sv >= 0 ? '+' : '' }}${{ number_format($sv, 2) }}</p>
            </div>
            <div class="p-2 bg-white rounded-lg border border-slate-100">
                <p class="text-[10px] text-slate-400">Cost Variance (CV)</p>
                <p class="text-sm font-bold {{ $cv >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $cv >= 0 ? '+' : '' }}${{ number_format($cv, 2) }}</p>
            </div>
            <div class="p-2 bg-white rounded-lg border border-slate-100">
                <p class="text-[10px] text-slate-400">SPI</p>
                <p class="text-sm font-bold {{ $spi >= 1 ? 'text-emerald-600' : 'text-red-600' }}">{{ $spi }}</p>
            </div>
            <div class="p-2 bg-white rounded-lg border border-slate-100">
                <p class="text-[10px] text-slate-400">CPI</p>
                <p class="text-sm font-bold {{ $cpi >= 1 ? 'text-emerald-600' : 'text-red-600' }}">{{ $cpi }}</p>
            </div>
            <div class="p-2 bg-white rounded-lg border border-slate-100">
                <p class="text-[10px] text-slate-400">Budget at Completion</p>
                <p class="text-sm font-bold text-slate-700">${{ number_format($baseline->budget_at_completion ?? $pv, 2) }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-bookmark text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No baselines captured yet</p>
    <form action="{{ route('admin.projects.baselines.store', $project->id) }}" method="POST" class="mt-3 inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Capture current project state as a new baseline?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
        @csrf
        <button type="submit" class="inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Capture Baseline</button>
    </form>
</div>
@endif
