@if(isset($contracts) && $contracts->count() > 0)
<div class="space-y-3">
    @foreach($contracts as $contract)
    <div class="p-4 bg-white border border-slate-200 rounded-lg hover:border-slate-300 transition-all">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <i class="bi bi-file-signature text-slate-400 text-sm"></i>
                    <h4 class="text-sm font-semibold text-slate-800 truncate">{{ $contract->subject ?? 'Contract #' . $contract->id }}</h4>
                </div>
                @if($contract->description)
                <p class="text-xs text-slate-500 line-clamp-2">{{ $contract->description }}</p>
                @endif
                <div class="flex items-center gap-3 mt-2 text-[10px] text-slate-400">
                    @if($contract->contract_value)
                    <span>Value: <strong class="text-slate-700">${{ number_format($contract->contract_value, 2) }}</strong></span>
                    @endif
                    @if($contract->datestart)
                    <span>{{ $contract->datestart?->format('M j, Y') }}
                        @if($contract->dateend) - {{ $contract->dateend->format('M j, Y') }}@endif
                    </span>
                    @endif
                    <span>{{ $contract->signed ? 'Signed' : 'Pending' }}</span>
                </div>
            </div>
            <span class="shrink-0 text-[10px] px-2 py-0.5 rounded-full {{ $contract->trash ? 'bg-red-100 text-red-700' : ($contract->signed ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700') }}">
                {{ $contract->trash ? 'Trashed' : ($contract->signed ? 'Signed' : 'Draft') }}
            </span>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-file-signature text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No contracts yet</p>
</div>
@endif
